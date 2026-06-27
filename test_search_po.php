<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

auth()->login(\App\Models\User::first());

$req = new \Illuminate\Http\Request();
$req->merge(['q' => 'ac', 'supplier_id' => 2]);
$ctrl = new App\Http\Controllers\RequisitionController();
$res = $ctrl->searchMedicines($req);
echo json_encode($res->getData());
