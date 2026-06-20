<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$session = Illuminate\Support\Facades\DB::table('sessions')->whereNotNull('user_id')->orderBy('last_activity', 'desc')->first();
echo "Latest user ID: " . ($session ? $session->user_id : 'none') . "\n";
