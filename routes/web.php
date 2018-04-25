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
//阿里云OSS转存储文件
Route::get('/oss', function()
{
    $client = App::make('aliyun-oss');
//    $client->putObject(getenv('OSS_BUCKET'), "eleb/www/1.txt", "上传文件3个参数:BUCKET名,文件名,文件内容");
//    $result = $client->getObject(getenv('OSS_BUCKET'), "eleb/www/1.txt");
//    echo $result;
    //将D:\www\eleb\eleb_shop\storage\app\public\date0422\SuncCvPZ1aSE7FjfUB2Zz7LrI39MGgrKnhhmzMSQ.jpeg
    //上传到阿里云OSS服务器
    try{
        $client->uploadFile(getenv('OSS_BUCKET'),
            'eleb/shop/public\date0422\SuncCvPZ1aSE7FjfUB2Zz7LrI39MGgrKnhhmzMSQ.jpeg',
            storage_path('app\public\date0422\SuncCvPZ1aSE7FjfUB2Zz7LrI39MGgrKnhhmzMSQ.jpeg'));
        echo '上传成功';
        //访问文件的地址
        //https://tina-laravel.oss-cn-beijing.aliyuncs.com/eleb/www/
        //urlencode('public\date0422\SuncCvPZ1aSE7FjfUB2Zz7LrI39MGgrKnhhmzMSQ.jpeg');
    } catch(\OSS\Core\OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        echo '上传失败';
        return;
    }
});
Route::get('/shops','ApiController@shops');
Route::get('/business','ApiController@business');
