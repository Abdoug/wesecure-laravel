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

Route::group(['middleware' => ['auth', 'checkSingleSession']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/messages/{id}', 'HomeController@messages')->name('messages');
    Route::post('/message', 'HomeController@sendMessage');
    Route::group(['prefix' => 'keys'], function () {
        Route::post('/store', 'KeyController@store')->name('keys.store');
    });
});

