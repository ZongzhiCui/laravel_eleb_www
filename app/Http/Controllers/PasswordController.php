<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    //忘记密码
    public function forgetPassword(Request $request)
    {
        //验证手机合法
        $validator = Validator::make($request->all(),[
            'tel'=>[
                "regex:/^1[34578][0-9]{9}$/"
            ]
        ],[
            'tel.regex'=>'电话号码不合法!',
        ]);
        //手动验证错误信息.
        if ($validator->fails()) {
            //输出错误信息到前端
            $errors = $validator->errors();
            return [
                "status"=> "false",
                "message"=> $errors->first(),
            ];
        }
        //验证验证码
        $code = Redis::get('code'.$request->tel);
        if ($code != $request->sms){
            return ["status"=> "false",
                "message"=> "验证码不正确"];
        }
        //验证手机
        $user = User::where('tel',$request->tel)->first();
        if ($user == null){
            return [
                "status"=> "false",
                "message"=> "手机号码不存在!"
            ];
        }
        //成功!
        $user->update([
            'password'=>bcrypt($request->password),
        ]);
        return [
            'status'=>'true',
            'message'=>'密码修改成功!'
        ];
    }
    //修改密码
    public function changePassword(Request $request)
    {
//        oldPassword: 旧密码
//        newPassword: 新密码
        $validator = Validator::make($request->all(),[
            'newPassword'=>'required|min:6'
        ],[
            'newPassword.min'=>'密码不能小于6位'
        ]);
        if ($validator->fails()){
            $errors = $validator->errors();
            return [
                'status'=>'false',
                'message'=>$errors->first(),
            ];
        }
        if (!Hash::check($request->oldPassword,Auth::user()->password)){
            return [
                'status'=>'false',
                'message'=>'旧密码错误!'
            ];
        }
        DB::table('users')
            ->where('id',Auth::user()->id)
            ->update([
                'password'=>bcrypt($request->newPassword)
            ]);
       return [
           'status'=>'true',
           'message'=>'密码修改成功!'
       ];
    }
}
