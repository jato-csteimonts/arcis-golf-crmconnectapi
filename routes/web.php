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
    return view('welcome');
});

Auth::routes();
Route::any('/register', function() {
    echo("not allowed");
});

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/unbounce/webhook', 'UnbounceController@webhook');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('/admin/users', 'Admin\UserController');
    Route::resource('/admin/leads', 'Admin\LeadController');
    Route::resource('/admin/unbounces', 'Admin\UnbounceController');
    Route::resource('/admin/reserveinteractives', 'Admin\ReserveInteractiveController');
});


// test routes
Route::any('/test_RI', 'LeadController@test_RI');