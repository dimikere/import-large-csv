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
    (new App\Insurance())->importToDb();
    //dd('done');
    return view('welcome');
});

Route::get('/import', 'InsuranceController@create');
Route::post('/import', 'InsuranceController@store');
