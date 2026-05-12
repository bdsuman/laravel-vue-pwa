<?php

namespace App\Console\Commands;

use App\Jobs\SendOtpEmailJob;

use Illuminate\Console\Command;

class TestOtpEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:test-email {email? : Email address to send OTP to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test OTP email via queue worker (for Mailpit testing)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email') ?? 'test@example.com';
        
        $this->info("Testing OTP Email Queue...");
        $this->info("Email: {$email}");
        $this->info("Queue: otp");
        $this->info("");
        
        // Check if queue worker is running
        $this->info("Make sure a queue worker is running:");
        $this->line("  php artisan queue:work redis --queue=otp,default");
        $this->line("  OR run: php artisan supervisor:start");
        $this->line("");
        
        // Generate test OTP
        $otp = rand(100000, 999999);
        $name = 'Test User';
        
        $this->info("OTP: {$otp}");
        $this->info("");
        
        // Dispatch job to queue
        SendOtpEmailJob::dispatch($email, $otp, $name)
            ->onQueue('otp');
        
        $this->info("✓ OTP email job dispatched to queue!");
        $this->info("");
        $this->info("Check Mailpit at: http://localhost:8025");
        $this->info("(or http://localhost:3000 if using Laravel Sail)");
        $this->info("");
        
        return Command::SUCCESS;
    }
}
