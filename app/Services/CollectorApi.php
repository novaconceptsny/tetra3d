<?php

namespace App\Services;

use App\Jobs\SyncArtworksJob;
use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Company;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Collector Systems Public API 2.0 client.
 *
 * Docs: https://apis.collectorsystems.com/swagger/index.html
 * - Base URL: https://apis.collectorsystems.com/public/v2/{AccountGUID}
 * - Auth: header "Ocp-Apim-Subscription-Key: <key>" (config services.collector.key / COLLECTOR_API_KEY)
 * - AccountGUID: per company, companies.collector_account_guid (from the Collector Systems web address
 *   app.collectorsystems.com/Items/Index/{AccountGUID}?subscriptionId=...)
 * - OData query options ($filter, $expand, $select, $top, $skip). Responses use PascalCase field names.
 *
 * The old API (api.collectorsystems.com/{subscription_id}/objects) was shut down and returns 404.
 */
class CollectorApi
{
    private const PAGE_SIZE = 100;

    /** Originals bigger than this (or not a web image, e.g. TIFF) are replaced by Collector's ~1440px JPEG rendition. */
    private const MAX_ORIGINAL_IMAGE_BYTES = 50 * 1024 * 1024;

    /** Collector rendition used when the original can not be used ("iphone" = largest JPEG rendition, ~1440px). */
    private const FALLBACK_IMAGE_SIZE = 'iphone';

    private const WEB_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    /** Related records needed to build a Tetra3D artwork. */
    private const EXPAND = 'Artist($select=ArtistName),'
        . 'MainDimension($select=WidthImperial,HeightImperial),'
        . 'ObjectType($select=ObjectTypeName),'
        . 'Collection($select=CollectionId,CollectionName),'
        . 'MainImageAttachment($select=AttachmentId,ContentType,FileSize)';

    private Company $company;

    /** Result of the last syncArtwork() call: 'created', 'updated' or 'unchanged'. */
    public string $lastSyncStatus = '';

    /** What changed in the last syncArtwork() call, e.g. 'Height: 10" → 12"'. Empty for new / unchanged artworks. */
    public array $lastSyncChanges = [];

    private string $baseUrl;

    private string $apiKey;

    /**
     * @throws Exception
     */
    public function __construct(Company $company)
    {
        $this->company = $company;

        $guid = trim((string) $company->collector_account_guid);
        $this->apiKey = (string) config('services.collector.key');

        if ($guid === '') {
            throw new Exception("Company \"{$company->name}\" has no Collector Systems Account GUID. Add it in Backend → Companies → Edit.");
        }

        if ($this->apiKey === '') {
            throw new Exception('Collector Systems API key is missing. Set COLLECTOR_API_KEY in .env and run php artisan config:clear.');
        }

        $this->baseUrl = rtrim((string) config('services.collector.url'), '/') . "/public/v2/{$guid}";
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * Check the collection exists, then queue the sync of all its artworks.
     *
     * @throws Exception
     */
    public function syncCollection($collection_id): void
    {
        $collection_id = (int) $collection_id;

        if (!$collection_id) {
            throw new Exception('Collection ID can not be empty', 403);
        }

        $collection = $this->getCollectionById($collection_id);

        SyncArtworksJob::dispatch($this->company, $collection_id, $collection->CollectionName ?? (string) $collection_id);
    }

    /**
     * Sync every artwork of a Collector Systems collection. Called from SyncArtworksJob.
     * Progress and errors are written to storage/logs/collector.log (Backend → /backend/collector-sync-report).
     */
    public function syncArtworksFromCollection(int $collection_id, string $collection_name = ''): void
    {
        ini_set('memory_limit', -1);

        // Empty the report instead of deleting it: Monolog keeps the file open, and a deleted file makes the next
        // log write fail with "fileinode(): stat failed" (an exception inside a long-running queue worker).
        File::put(storage_path('logs/collector.log'), '');

        $log = Log::channel('collector-sync-report');
        $label = $collection_name ?: $collection_id;

        $log->info("*********** Syncing Collection $label (ID $collection_id) for company {$this->company->name} ***********");

        $objects = $this->getCollectionObjects($collection_id);
        $total = count($objects);
        $counts = ['created' => 0, 'updated' => 0, 'unchanged' => 0, 'skipped' => 0];

        foreach ($objects as $index => $object) {
            $current = $index + 1;
            $inventoryNumber = $object->InventoryNumber ?? '-';
            $title = $object->Title ?? '';

            try {
                $this->syncArtwork($object);
                $counts[$this->lastSyncStatus]++;
                $log->info("[$current/$total] Object {$object->ObjectId} ($inventoryNumber) \"$title\": {$this->lastSyncSummary()}");
            } catch (Exception $exception) {
                $counts['skipped']++;
                $log->error(
                    "############ [$current/$total] Error in Object {$object->ObjectId} ($inventoryNumber) \"$title\" ############ \n {$exception->getMessage()}"
                );
            }
        }

        $synced = $total - $counts['skipped'];
        $log->info(
            "*********** Collection synced: $synced of $total artworks — "
            . "{$counts['created']} new, {$counts['updated']} updated, {$counts['unchanged']} unchanged, {$counts['skipped']} skipped ***********"
        );
    }

    /**
     * Create or update one Tetra3D artwork from a Collector Systems "Art" record
     * (as returned by getObjectById / getCollectionObjects).
     *
     * Compares the current Tetra3D values with Collector first and only saves / downloads what changed.
     * The result is in $lastSyncStatus ('created' | 'updated' | 'unchanged') and $lastSyncChanges
     * (e.g. 'Height: 10" → 12"'), shown in the sync report and the Collector Tools modal.
     *
     * @throws Exception
     */
    public function syncArtwork($object): Artwork
    {
        $this->lastSyncStatus = '';
        $this->lastSyncChanges = [];

        [$width, $height] = $this->getDimensions($object);

        $values = [
            'name' => (string) ($object->Title ?? ''),
            'artist' => $object->Artist->ArtistName ?? null,
            'type' => $object->ObjectType->ObjectTypeName ?? null,
            'collection' => $object->Collection->CollectionName ?? 'Collector Systems',
        ];
        $imageId = (int) ($object->MainImageAttachmentId ?? 0);

        // Match on company + Collector object ID so two companies on the same Collector account
        // never take over each other's artworks.
        $artwork = Artwork::withoutGlobalScopes()->firstOrNew([
            'company_id' => $this->company->id,
            'collector_object_id' => $object->ObjectId,
        ]);
        $isNew = !$artwork->exists;

        $changes = [];
        $dimensionsChanged = $isNew;
        $imageChanged = $isNew;

        if (!$isNew) {
            $oldCollection = ArtworkCollection::withoutGlobalScopes()
                ->whereKey($artwork->artwork_collection_id)->value('name');

            // Raw DB values: Artwork::getTypeAttribute() shows an empty type as "Unknown".
            $this->compareText($changes, 'Name', $artwork->getRawOriginal('name'), $values['name']);
            $this->compareText($changes, 'Artist', $artwork->getRawOriginal('artist'), $values['artist']);
            $this->compareText($changes, 'Type', $artwork->getRawOriginal('type'), $values['type']);
            $this->compareText($changes, 'Collection', $oldCollection, $values['collection']);
            $widthChanged = $this->compareInches($changes, 'Width', $artwork->data->get('width_inch'), $width);
            $heightChanged = $this->compareInches($changes, 'Height', $artwork->data->get('height_inch'), $height);
            $dimensionsChanged = $widthChanged || $heightChanged;

            // collector_image_id is stored since the API 2.0 upgrade; artworks synced before have none.
            $oldImageId = (int) $artwork->data->get('collector_image_id');
            if (!$artwork->getFirstMedia('image')) {
                $imageChanged = true;
                $changes[] = 'Image: added (artwork had no image)';
            } elseif ($oldImageId === 0) {
                $imageChanged = true;
                $changes[] = 'Image: downloaded again (first sync since the Collector API upgrade)';
            } elseif ($oldImageId !== $imageId) {
                $imageChanged = true;
                $changes[] = 'Image: replaced with the new main image from Collector Systems';
            }
        }

        $this->lastSyncChanges = $changes;

        if (!$isNew && !$changes) {
            $this->lastSyncStatus = 'unchanged';

            return $artwork;
        }

        // Download the image before saving, so a failed download never leaves an artwork without an image.
        // A new size also needs the original again, because resizeImage() resizes the stored file.
        $image = ($imageChanged || $dimensionsChanged) ? $this->downloadMainImage($object) : null;

        $collection = ArtworkCollection::withoutGlobalScopes()->firstOrCreate([
            'company_id' => $this->company->id,
            'name' => $values['collection'],
        ]);
        $this->forceCompany($collection);

        // collector_object_id and artist are not in Artwork::$fillable, so set every field explicitly.
        $artwork->company_id = $this->company->id;
        $artwork->collector_object_id = $object->ObjectId;
        $artwork->artwork_collection_id = $collection->id;
        $artwork->name = $values['name'];
        $artwork->artist = $values['artist'];
        $artwork->type = $values['type'];
        $artwork->data = array_merge($artwork->data?->all() ?? [], [
            'width_inch' => $width,
            'height_inch' => $height,
            'collector_image_id' => $imageId,
        ]);
        if ($dimensionsChanged) {
            $artwork->original_unit = 'inch';
            $artwork->original_value = ['height' => $height, 'width' => $width, 'unit' => 'inch'];
        }
        $artwork->save();
        $this->forceCompany($artwork);

        if ($image) {
            [$imageBytes, $imageExtension] = $image;
            unset($image);

            $fileName = Str::slug($values['name']) ?: 'artwork';
            $artwork->addMediaFromString($imageBytes)
                ->usingFileName("{$object->ObjectId}-{$fileName}.{$imageExtension}")
                ->toMediaCollection('image');
            unset($imageBytes);

            // refresh model, to ensure the media is attached!
            $artwork->refresh();

            $artwork->resizeImage();
        }

        $this->lastSyncStatus = $isNew ? 'created' : 'updated';

        return $artwork;
    }

    /** Text summary of the last syncArtwork() call, for the sync report. */
    public function lastSyncSummary(): string
    {
        return match ($this->lastSyncStatus) {
            'created' => 'New artwork added',
            'unchanged' => 'No changes (already up to date)',
            'updated' => 'Updated: ' . implode('; ', $this->lastSyncChanges),
            default => '',
        };
    }

    private function compareText(array &$changes, string $label, $old, $new): void
    {
        $old = trim((string) $old);
        $new = trim((string) $new);

        if ($old !== $new) {
            $changes[] = "$label: " . ($old === '' ? '(empty)' : "\"$old\"") . ' → ' . ($new === '' ? '(empty)' : "\"$new\"");
        }
    }

    private function compareInches(array &$changes, string $label, $old, float $new): bool
    {
        $format = fn ($value) => ($value === null || $value === '')
            ? '(empty)'
            : rtrim(rtrim(number_format((float) $value, 4, '.', ''), '0'), '.') . '"';

        if ($old !== null && $old !== '' && round((float) $old, 4) === round($new, 4)) {
            return false;
        }

        $changes[] = "$label: {$format($old)} → {$format($new)}";

        return true;
    }

    /**
     * @throws Exception
     */
    public function getObjectById($object_id)
    {
        $object_id = (int) $object_id;

        if (!$object_id) {
            throw new Exception('Object ID can not be empty', 403);
        }

        $response = $this->request()->get("{$this->baseUrl}/Art($object_id)", [
            '$expand' => self::EXPAND,
        ]);

        if ($response->status() === 404) {
            throw new Exception("No artwork found in Collector Systems with Object ID $object_id", 404);
        }

        $this->ensureSuccessful($response);

        return $response->object();
    }

    /**
     * @throws Exception
     */
    public function getCollectionById(int $collection_id)
    {
        $response = $this->request()->get("{$this->baseUrl}/Collections($collection_id)", [
            '$select' => 'CollectionId,CollectionName',
        ]);

        if ($response->status() === 404) {
            throw new Exception("No collection found in Collector Systems with ID $collection_id", 404);
        }

        $this->ensureSuccessful($response);

        return $response->object();
    }

    /**
     * All "Art" records directly in the collection (sub-collections are not included), page by page.
     *
     * @throws Exception
     */
    public function getCollectionObjects(int $collection_id): array
    {
        $objects = [];
        $skip = 0;

        do {
            $response = $this->request()->get("{$this->baseUrl}/Art", [
                '$filter' => "CollectionId eq $collection_id",
                '$expand' => self::EXPAND,
                '$orderby' => 'ObjectId',
                '$top' => self::PAGE_SIZE,
                '$skip' => $skip,
            ]);

            $this->ensureSuccessful($response);

            $page = $response->object()->value ?? [];
            array_push($objects, ...$page);
            $skip += self::PAGE_SIZE;
        } while (count($page) === self::PAGE_SIZE);

        return $objects;
    }

    /**
     * Width / height in inches from the object's main dimension.
     *
     * @throws Exception
     */
    private function getDimensions($object): array
    {
        $width = (float) ($object->MainDimension->WidthImperial ?? 0);
        $height = (float) ($object->MainDimension->HeightImperial ?? 0);

        if ($width <= 0 || $height <= 0) {
            throw new Exception(
                'No dimensions found in Collector Systems (width/height missing or 0), artwork not synced',
                404
            );
        }

        return [$width, $height];
    }

    /**
     * Download the main image (the API needs the key header, so addMediaFromUrl can not be used).
     * Uses the original file when it is a web image (JPEG/PNG/GIF/WebP) under MAX_ORIGINAL_IMAGE_BYTES;
     * otherwise (e.g. a 180 MB TIFF) Collector's FALLBACK_IMAGE_SIZE JPEG rendition.
     * Returns [bytes, extension]; the extension comes from the file content, not the HTTP header
     * (Collector labels JPEG renditions of a TIFF as image/tiff).
     *
     * @throws Exception
     */
    private function downloadMainImage($object): array
    {
        $attachmentId = $object->MainImageAttachmentId ?? null;

        if (!$attachmentId) {
            throw new Exception('No main image in Collector Systems, artwork not synced', 404);
        }

        $attachment = $object->MainImageAttachment ?? null;
        $useOriginal = $attachment
            && in_array(strtolower((string) $attachment->ContentType), self::WEB_IMAGE_TYPES, true)
            && (int) $attachment->FileSize > 0
            && (int) $attachment->FileSize <= self::MAX_ORIGINAL_IMAGE_BYTES;

        $response = $this->request()->timeout(300)->get(
            "{$this->baseUrl}/Attachments($attachmentId)/\$value",
            $useOriginal ? [] : ['size' => self::FALLBACK_IMAGE_SIZE]
        );

        $this->ensureSuccessful($response);

        $bytes = $response->body();
        $info = @getimagesizefromstring($bytes);

        $extension = match ($info['mime'] ?? null) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => throw new Exception(
                'Main image from Collector Systems is not a JPEG/PNG/GIF/WebP image, artwork not synced', 415
            ),
        };

        return [$bytes, $extension];
    }

    /**
     * HasCompany adds a "created" hook that sets company_id to the logged-in user's company.
     * When a super admin syncs for another company, put the record back on the right company.
     */
    private function forceCompany($model): void
    {
        if ((int) $model->company_id !== (int) $this->company->id) {
            $model->company_id = $this->company->id;
            $model->saveQuietly();
        }
    }

    private function request(): PendingRequest
    {
        return Http::withHeaders(['Ocp-Apim-Subscription-Key' => $this->apiKey])
            ->acceptJson()
            ->timeout(60)
            ->retry(2, 1000, fn ($exception) => $exception instanceof ConnectionException, throw: false);
    }

    /**
     * @throws Exception
     */
    private function ensureSuccessful(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $message = match ($response->status()) {
            401, 403 => 'Collector Systems rejected the request (HTTP ' . $response->status() . '). Check COLLECTOR_API_KEY and the company\'s Account GUID.',
            500 => 'Collector Systems returned an error (HTTP 500). This usually means the company\'s Account GUID is wrong.',
            default => 'Collector Systems request failed (HTTP ' . $response->status() . ').',
        };

        Log::warning('Collector Systems API error', [
            'company_id' => $this->company->id,
            'url' => (string) $response->effectiveUri(),
            'status' => $response->status(),
            'body' => Str::limit($response->body(), 500),
        ]);

        throw new Exception($message, $response->status());
    }
}
