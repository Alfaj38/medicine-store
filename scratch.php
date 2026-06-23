<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$codes = App\Models\ReferralCode::all()->toArray();
echo json_encode($codes, JSON_PRETTY_PRINT);
