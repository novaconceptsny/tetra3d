<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email configuration...");
        $this->info("Sending test email to: {$email}");
        
        try {
            // Test with a sample verification code
            $testCode = 'TEST123';
            $testName = 'Test User';
            
            Mail::to($email)->send(new VerificationCodeMail($testCode, $testName));
            
            $this->info("✅ Email sent successfully!");
            $this->info("Check your email inbox for the test message.");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email:");
            $this->error($e->getMessage());
            
            // Show configuration info
            $this->info("\n📧 Current Mail Configuration:");
            $this->info("MAIL_MAILER: " . config('mail.default'));
            $this->info("MAIL_HOST: " . config('mail.mailers.smtp.host'));
            $this->info("MAIL_PORT: " . config('mail.mailers.smtp.port'));
            $this->info("MAIL_USERNAME: " . config('mail.mailers.smtp.username'));
            $this->info("MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption'));
            
            return 1;
        }
    }
} 