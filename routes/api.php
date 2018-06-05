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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['namespace' => 'App\Http\Controllers'], function ($api) {
    // token 相关
    $api->group(['prefix' => 'auth'], function ($api) {
        $api->post('login', 'AuthController@login');
        $api->post('logout', 'AuthController@logout');
        $api->post('refresh', 'AuthController@refresh');
        $api->post('me', 'AuthController@me');
    });

    // activity 管理
    $api->post('activities', 'ActivityController@store');
    $api->get('activities', 'ActivityController@index');
    $api->get('activities/{activity}', 'ActivityController@show');
    $api->match(['put','patch'] ,'activities/{activity}', 'ActivityController@update');
    $api->delete('activities/{activity}', 'ActivityController@destroy');

    // candidate 管理
    $api->post('candidates', 'CandidateController@store');
    $api->get('candidates', 'CandidateController@index');
    $api->get('candidates/{candidate}', 'CandidateController@show');
    $api->match(['put','patch'] ,'candidates/{candidate}', 'CandidateController@update');
    $api->delete('candidates/{candidate}', 'CandidateController@destroy');

    // voter 管理
    $api->post('voters', 'VoterController@store');
    $api->get('voters', 'VoterController@index');
    $api->get('voters/{voter}', 'VoterController@show');
    $api->match(['put','patch'] ,'voters/{voter}', 'VoterController@update');
    $api->delete('voters/{voter}', 'VoterController@destroy');

    // 文件上传
    $api->post('file', 'FileController@uploadImg');

    // 投票
    $api->post('vote','VoteController@vote');
 });