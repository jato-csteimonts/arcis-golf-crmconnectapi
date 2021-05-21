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
	"v2"            => "v2",
	"unbounce"      => "UnBounce",
	"distribion"    => "Distribion",
	"beloandco"     => "BeloAndCo",
	"facebook"      => "Facebook",
	"clubessential" => "ClubEssential",
	"instagram"     => "Instagram",
	"tableau"       => "Tableau",
	"iauditor"      => [
		"controller" => "iAuditor",
		"actions" => [
			"get",
		]
	],
];

foreach($serviceProviders as $type => $controller) {
	if(is_array($controller)) {
		foreach($controller['actions'] as $action) {
			Route::any("{$type}/{$action}", "Webhooks\\{$controller['controller']}@{$action}");
		}
	} else {
		Route::any($type, "Webhooks\\{$controller}@process");
	}
}