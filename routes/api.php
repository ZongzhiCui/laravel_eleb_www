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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//短信验证发送api接口 有前缀api
Route::get('/sms','SendSmsController@sendSms');
//前端注册信息短信验证和注册
Route::post('/regist','SendSmsController@regist');
//前端登录的验证
Route::post('/loginCheck','SendSmsController@loginCheck');