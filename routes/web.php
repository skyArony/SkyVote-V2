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

// session 登录验证
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/socialite/qq', 'SocialiteController@QQ');
Route::get('/socialite/callback', 'SocialiteController@callback');

Route::get('/test', 'Controller@Test');