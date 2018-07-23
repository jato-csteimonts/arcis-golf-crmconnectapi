<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$serviceProviders = [
	"unbounce"   => "UnBounce",
	"distribion" => "Distribion",
	"beloandco"     => "BeloAndCo",
];

foreach($serviceProviders as $type => $controller) {
	Route::post($type, "Webhooks\\{$controller}@process");
}