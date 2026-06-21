<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class FetchBangladeshPharmaSeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        Artisan::call('fetch:bangladesh-pharma');
    }
}
?>
