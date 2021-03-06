<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

Route::post('/login', 'AuthController@login');
Route::post('/user/signUp', 'Auth\RegisterController@register');
Route::get('/user/sendVerifyMail/{email}', 'Api\UserController@sendVerifyMail');
Route::post('/set_password', 'Api\UserController@setPassword');
Route::post('/reset_password', 'Api\UserController@resetPassword');
Route::get('/user/emailVerify/{token}', 'Api\UserController@emailVerify');
Route::get('/user/signUpEmailVerify/{token}', 'Api\UserController@signUpEmailVerify');
Route::get('/category/get', 'Api\CategoryController@index');
Route::get('/menu/get/{id}', 'Api\MenuController@get'); // こっちはカテゴリIDにひもづくメニュー一覧を取得

Route::group(['middleware' => 'auth:api'], function () {

    Route::post('/logout', 'AuthController@logout');
    Route::get('/me', 'AuthController@me');

    Route::get('/category/get/{id}', 'Api\CategoryController@get');
    Route::get('/user/get', 'Api\UserController@getAll');
    Route::get('/user/get/{id}', 'Api\UserController@get');
    Route::get('/user/getCreator', 'Api\UserController@getCreator');
    Route::get('/menu/get', 'Api\MenuController@getAll');
    Route::post('/proposition/store', 'Api\PropositionController@store');
    Route::post('/proposition/payment', 'Api\PropositionController@payment');
    Route::get('/proposition/get', 'Api\PropositionController@get');
    Route::get('/proposition/get/{id}', 'Api\PropositionController@getOne');
    Route::get('/proposition/getAll', 'Api\PropositionController@getAll');
    Route::get('/messages/get/{id}', 'Api\PropositionController@getMessages');
    Route::get('/propositionUsers/get/{id}', 'Api\PropositionController@getUsers');
    Route::post('/message/store', 'Api\PropositionController@storeMessage');
    Route::post('/user/edit', 'Api\UserController@edit');
    Route::get('/information/get', 'Api\InformationController@get');
    Route::get('/information/get/{id}', 'Api\InformationController@getOne');
    Route::post('/proposition/edit/progress', 'Api\PropositionController@editProgress');

});


// 管理者のみアクセス可能
Route::middleware('auth:api', 'admin')->group(function () {
    Route::post('/user/store', 'Auth\RegisterController@register');
    Route::get('/user/sendTempRegistMail/{email}', 'Api\UserController@sendTempRegistMail');
    Route::post('/category/store', 'Api\CategoryController@store');
    Route::post('/menu/store', 'Api\MenuController@store');
    Route::post('/proposition/attachCreator', 'Api\PropositionController@attachCreator');
    Route::delete('/user/delete/{id}', 'Api\UserController@delete');
    Route::post('/category/edit', 'Api\CategoryController@edit');
    Route::delete('/category/delete/{id}', 'Api\CategoryController@delete');
    Route::get('/menu/getOne/{id}', 'Api\MenuController@getOne'); // こっちはメニューIDにマッチするメニューを一つ取得
    Route::post('/menu/edit', 'Api\MenuController@edit');
    Route::delete('/menu/delete/{id}', 'Api\MenuController@delete');
    Route::post('/information/store', 'Api\InformationController@store');
    Route::post('/information/fileUpload', 'Api\InformationController@fileUpload');
    Route::post('/information/edit', 'Api\InformationController@edit');
    Route::delete('/information/delete/{id}', 'Api\InformationController@delete');
});
