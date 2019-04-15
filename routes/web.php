<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	if(Auth::check()) {
		return redirect("home");
	} else {
		return redirect("login");
	}
});

Auth::routes();
Route::any('/register', function() {
    echo("not allowed");
});

Route::get('/home', 'HomeController@index')->name('home');

//Route::post('/unbounce/webhook', 'UnbounceController@webhook');
Route::post('/unbounce/webhook', "Webhooks\\UnBounce@process"); // Catch old Webhooks and route them to the new processing system
Route::post('/webforms', 'WebformController@process');

Route::get('/admin/reports/update', 'Admin\ReportController@update');
Route::get('/admin/reports/show', 'Admin\ReportController@show');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('/admin/users', 'Admin\UserController');
    Route::resource('/admin/leads', 'Admin\LeadController');
    Route::resource('/admin/unbounces', 'Admin\UnbounceController');
    Route::resource('/admin/reserveinteractives', 'Admin\ReserveInteractiveController');
    Route::resource('/admin/domains', 'Admin\DomainController');
    Route::resource('/admin/fields', 'Admin\FieldController');
	Route::resource('/admin/clubs', 'Admin\ClubController');
	Route::get('/admin/ajax', 'Controller@ajax');
});

Route::get('/webform-javascript', 'WebformController@serve_js');


// test routes
//Route::any('/test_RI', 'LeadController@test_RI');
Route::any('/webformtest', function() {
    return view('web.webformtest');
});