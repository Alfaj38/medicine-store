<?php

use Illuminate\Support\Facades\Request;
use App\Models\User;
use App\Http\Middleware\HandleInertiaRequests;

$request = Request::create('/dashboard', 'GET');
$request->setUserResolver(function() { return User::find(3); });
$middleware = new HandleInertiaRequests();

echo json_encode($middleware->share($request)['auth']);
