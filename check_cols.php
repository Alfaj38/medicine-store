<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Users columns:\n";
print_r(Schema::getColumnListing('users'));

echo "\nBranches columns:\n";
print_r(Schema::getColumnListing('branches'));

echo "\nCompanies columns:\n";
print_r(Schema::getColumnListing('companies'));
