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
    $array = array( 'name' => '候选人1',
                    'intro' => '候选人介绍',
                    'belong_ac' => '6f17584a-9c6a-4978-b302-a19011810cd9',
                    'type' => 1,
                    'media_array' => array( 'img_url' => 'https://google.com',
                                            'video_url' => 'https://google.com',
                                            'audio_url' => 'https://google.com',
                                            'link_url' => 'https://google.com',
                                            'linkcover_url' => 'https://google.com'),
                    'tel' => 18890336732,
                    'QQ' => 1450872874
            );
    $data = json_encode($array, JSON_UNESCAPED_UNICODE);
    echo($data);
    exit;
    return view('welcome');
});

Route::post('/test', 'ActivityController@create');

// session 登录
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');