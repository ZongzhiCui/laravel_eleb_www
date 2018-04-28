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
//修改密码
Route::post('/changePassword','PasswordController@changePassword');
//忘记密码
Route::post('/forgetPassword','PasswordController@forgetPassword');

//Route::resource('/address','PositionController.php');
/**地址路由**/
//地址列表
Route::get('/addressList','PositionController@addressList');
//地址添加
Route::post('/addAddress','PositionController@addAddress');
//点击修改回显
Route::get('/address','PositionController@address');
//地址修改
Route::post('editAddress','PositionController@editAddress');
//地址删除
Route::get('delete','PositionController@destroy');

/**购物车接口**/
//去结算
Route::post('addCart','CartController@addCart');
//订单详情
Route::get('cart','CartController@cart');

/**订单生成接口**/
Route::post('addorder','OrderController@addorder');
//订单显示
Route::get('order','OrderController@order');
//订单列表
Route::get('orderList','OrderController@orderList');
//订单支付
Route::post('pay','OrderController@pay');
