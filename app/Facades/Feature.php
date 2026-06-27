<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Feature extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'feature_manager';
    }
}
