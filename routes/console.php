<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fetch:bangladesh-pharma', function () {
    (new \App\Console\Commands\FetchBangladeshPharma)->handle();
})->purpose('Fetch Bangladeshi pharma companies and map to item types & categories');

\Illuminate\Support\Facades\Schedule::command('subscription:process-downgrades')->daily();
