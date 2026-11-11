<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\CollectorApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Syncs all artworks of one Collector Systems collection into a company (see App\Services\CollectorApi).
 * Only IDs are queued; the artworks are fetched from the API when the job runs.
 */
class SyncArtworksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public int $tries = 1;

    public function __construct(
        public Company $company,
        public int $collectionId,
        public string $collectionName = '',
    ) {
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        set_time_limit(60 * 60);

        (new CollectorApi($this->company))
            ->syncArtworksFromCollection($this->collectionId, $this->collectionName);
    }

    public function failed(?\Throwable $exception = null): void
    {
        Log::channel('collector-sync-report')->error(
            "############ Collection sync failed ############ \n " . ($exception?->getMessage() ?? 'Unknown error')
        );
    }
}
