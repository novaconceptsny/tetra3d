<?php

namespace App\Jobs;

use App\Services\CollectorApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncArtworksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public $collection;
    public CollectorApi $collector;

    public function __construct(CollectorApi $collector, $collection)
    {
        $this->collection = $collection;
        $this->collector = $collector;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        set_time_limit(60 * 60);

        $collection = $this->collection;
        $this->collector->syncArtworksFromCollection($collection);

    }

    public function fail($exception = null): void
    {
        Log::channel('collector-sync-report')->error($exception);
    }
}
