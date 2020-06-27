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

use App\Config;
use phpseclib\Crypt\RSA;

Route::get('/', function () {
//    $private_server = Config::where('key', 'private_key')->first()->value;
//
//    $rsa = new RSA();
//    $ckey = "HYNNA3hF2agLmsJ6dDC0k2GZMOETlvNgOzODqg5fRiTiOrYdVkNFl+0bQT4CVKDqD6NqOCno4gINRD5GpF7Bb6eQFKqc5Blo7rz5OKEfUYz6qSjHBZox+uE5RggaSI1Xrfh+aG6Xq5/dlu3Vg/Sth+j4i/49X5cUCf0Mzl5ei0thWLYI2Ish769wtHiORL8zeM7mYZd6anfpcz8/BnhRpjb/Yhv3k8dn46QEUnCcgyRqVF/BvWa9umVWxHA0GjnKYoJeZ7z3+lbwmdcCnIuFdUeEhMmiyAAQGyFjvixZAg2hts210YdMNUMO4nyfhqvchJD5pyZBFvcP6q9bDISlcqjzVZf/T8ydPjP7blKI1sAnbGkmRG3r+kVWRVcFKFBla/Qj/Hte2atApWPce91Zi0EjmBBBIg8SCm6N/LJo2OIa1g8HYafoiVKmbFEdoX73Szz0WcCAm93FGiT6+wd/edP/azDjVnhJ7nCMsldEA+bjEIki6XPkvjPOOkWgFlE21vmftpr5vpVzxCU+kYXNRGtUM3ogCUTWWweVuZotoOq2j/K04r7MkFUK1OS13AeGFXhlmOgZAiCPi2B/RBANinRB1OCFH3Enw/iolkAzGjE6bMWpGqWxX4LTnfOQj9/AYumfa77rtjYERtuPbTLd190KFb+zHFV+RBIj+5/XVuk=";
//    $rsa->loadKey($private_server);
//    $ciphertext = $ckey;
//    dump(base64_decode($ciphertext));
//    $plain = $rsa->decrypt(base64_decode($ciphertext));
//
//
//
//
//
//    dump($plain);
//    exit;
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

