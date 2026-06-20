<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invs = Illuminate\Support\Facades\DB::table('inventories')->whereIn('medicine_id', [1186, 15178])->get();
print_r($invs->toArray());

$sessionPath = storage_path('framework/sessions');
$files = scandir($sessionPath);
$latestSession = null;
$latestTime = 0;
foreach($files as $file) {
    if ($file == '.' || $file == '..') continue;
    $time = filemtime("$sessionPath/$file");
    if ($time > $latestTime) {
        $latestTime = $time;
        $latestSession = "$sessionPath/$file";
    }
}
if ($latestSession) {
    $content = file_get_contents($latestSession);
    $data = unserialize($content);
    echo "Latest session user ID: " . ($data['login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'] ?? 'none') . "\n";
}
