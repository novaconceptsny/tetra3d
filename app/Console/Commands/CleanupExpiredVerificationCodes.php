<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class CleanupExpiredVerificationCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired verification codes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredUsers = User::where('verification_code_expires_at', '<', Carbon::now())
            ->whereNotNull('verification_code')
            ->get();

        $count = 0;
        foreach ($expiredUsers as $user) {
            $user->update([
                'verification_code' => null,
                'verification_code_expires_at' => null,
            ]);
            $count++;
        }

        $this->info("Cleaned up {$count} expired verification codes.");
        return 0;
    }
} 