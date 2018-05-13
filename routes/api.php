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
     $api->post('activitys', 'ActivityController@store');
     $api->get('activitys', 'ActivityController@index');
     $api->get('activitys/{activity}', 'ActivityController@show');
     $api->match(['put','patch'] ,'activitys/{activity}', 'ActivityController@update');
     $api->delete('activitys/{activity}', 'ActivityController@destroy');

     // candidate 管理
     $api->post('candidates', 'CandidateController@store');
     $api->get('candidates', 'CandidateController@index');
     $api->get('candidates/{candidate}', 'CandidateController@show');
     $api->match(['put','patch'] ,'candidates/{candidate}', 'CandidateController@update');
     $api->delete('candidates/{candidate}', 'CandidateController@destroy');

     // participant 管理
     $api->post('participants', 'ParticipantController@store');
     $api->get('participants', 'ParticipantController@index');
     $api->get('participants/{participant}', 'ParticipantController@show');
     $api->match(['put','patch'] ,'participants/{participant}', 'ParticipantController@update');
     $api->delete('participants/{participant}', 'ParticipantController@destroy');

 });