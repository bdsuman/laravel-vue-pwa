#!/usr/bin/env php
<?php

/**
 * Standalone OTP Queue Worker
 * 
 * Run this script to process OTP queue jobs:
 * php artisan otp:worker
 * 
 * For production, use supervisor or systemd service.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run the OTP queue worker
$kernel->call('queue:work', [
    'connection' => 'redis',
    'queue' => 'otp,default',
    '--sleep' => 3,
    '--tries' => 3,
    '--max-time' => 3600,
]);