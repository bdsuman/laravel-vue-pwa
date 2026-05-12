<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartQueueWorkers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisor:start 
                            {--queue= : Specific queue to work on}
                            {--sleep=3 : Number of seconds to sleep when no job is available}
                            {--tries=3 : Number of times to attempt a job before logging it failed}
                            {--timeout=60 : The number of seconds a child process can run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start queue workers for OTP and default queues';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $queue = $this->option('queue') ?? 'otp,default';
        $sleep = $this->option('sleep');
        $tries = $this->option('tries');
        $timeout = $this->option('timeout');

        $this->info('Starting Laravel Queue Workers...');
        $this->info("Queue: {$queue}");
        $this->info("Sleep: {$sleep}s | Tries: {$tries} | Timeout: {$timeout}s");
        $this->info('');
        
        // Check Redis connection
        try {
            \Illuminate\Support\Facades\Redis::ping();
            $this->info('✓ Redis connection OK');
        } catch (\Exception $e) {
            $this->error('✗ Redis connection failed: ' . $e->getMessage());
            $this->warn('Make sure Redis is running or update .env');
            return Command::FAILURE;
        }
        
        $this->info('');
        $this->info('Starting workers (Ctrl+C to stop)...');
        $this->line('');

        $command = [
            'php',
            base_path('artisan'),
            'queue:work',
            'redis',
            '--queue=' . $queue,
            '--sleep=' . $sleep,
            '--tries=' . $tries,
            '--timeout=' . $timeout,
        ];

        $process = Process::fromShellCommandline(implode(' ', $command));
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        return Command::SUCCESS;
    }
}
