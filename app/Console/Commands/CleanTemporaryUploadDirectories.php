<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanTemporaryUploadDirectories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tetra:clean-temp-uploads
                            {--days=2 : Only delete hash directories untouched for at least this many days}
                            {--dry-run : List what would be deleted without deleting anything}
                            {--force : Ignore the age check and delete all matching directories}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete leftover Media Library Pro temporary-upload hash directories from storage/app/public';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $root   = storage_path('app/public');
        $days   = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days)->getTimestamp();
        $dryRun = (bool) $this->option('dry-run');
        $force  = (bool) $this->option('force');

        $deleted = 0;
        $skipped = 0;
        $freed   = 0;

        // 1) md5-style hash directories at the public disk root.
        //    Real app data (media/, 3dmodel/, tours, ...) never matches this pattern.
        $targets = collect(File::directories($root))
            ->filter(fn ($dir) => preg_match('/^[0-9a-f]{32}$/', basename($dir)));

        // 2) numbered staging directories under media/temporary_uploads
        //    ({temporary_upload_id}/{media_id}/file, created by the custom PathGenerator).
        $stagingRoot = $root.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'temporary_uploads';

        if (File::isDirectory($stagingRoot)) {
            $targets = $targets->merge(
                collect(File::directories($stagingRoot))
                    ->filter(fn ($dir) => preg_match('/^\d+$/', basename($dir)))
            );
        }

        foreach ($targets as $dir) {
            // Skip anything recently touched - it may be an in-progress upload.
            if (! $force && $this->newestTimestamp($dir) > $cutoff) {
                $skipped++;
                continue;
            }

            $size = $this->directorySize($dir);

            if ($dryRun) {
                $this->line('[dry-run] would delete '.str_replace($root.DIRECTORY_SEPARATOR, '', $dir).' ('.$this->formatBytes($size).')');
            } else {
                File::deleteDirectory($dir);
            }

            $deleted++;
            $freed += $size;
        }

        $verb = $dryRun ? 'Would delete' : 'Deleted';
        $this->info("{$verb} {$deleted} hash directories (".$this->formatBytes($freed)."), skipped {$skipped} recent.");

        return self::SUCCESS;
    }

    /**
     * Newest modification time of the directory or any file inside it.
     */
    private function newestTimestamp(string $dir): int
    {
        $newest = @filemtime($dir) ?: 0;

        foreach (File::allFiles($dir, true) as $file) {
            $newest = max($newest, $file->getMTime());
        }

        return $newest;
    }

    /**
     * Total size in bytes of all files inside the directory.
     */
    private function directorySize(string $dir): int
    {
        $size = 0;

        foreach (File::allFiles($dir, true) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i     = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }
}
