<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\SignatureHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SendSmsController extends Controller
{
    /**
     * 发送短信
     */
    function sendSms(Request $request) {
        $params = array ();

        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIzKh1EmME4I6Q";
        $accessKeySecret = "MB2AePM9RVQ3Kd11z69w6XCwiPncvS";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $request->tel;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "天天披萨";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_133805015";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => mt_rand(100000,999999),
//            "product" => "阿里通信"
        );
        Redis::setex('code'.$request->tel,600,$params['TemplateParam']['code']);
//        dd(Redis::get('code'));

        // fixme 可选: 设置发送短信流水号
//        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
//        dd($content);
        //同一手机一分钟只能发送一条短信.一小时5条.一天最多10条
        /*{#209
            +"Message": "OK"
        +"RequestId": "66F6A9A9-6A3B-403F-8C9C-CDF212E378EF"
        +"BizId": "946819824712443743^0"
        +"Code": "OK"
        }*/
        if ($content->Message == 'OK'){
            //短信发送成功!
            return ["status"=> "true",
                    "message"=> "获取短信验证码成功"];
        }else{
            //发送失败
            return ["status"=> "false",
                    "message"=> "获取短信验证码失败!稍后再试"];
        }
//        return $content;
    }
    
    //接收用户注册
    public function regist(Request $request)
    {
//        {username: "12123123", tel: "123", sms: "123", password: "123"}
//        return $request->input();
        $code = Redis::get('code'.$request->tel);
        if ($code != $request->sms){
            return ["status"=> "false",
                    "message"=> "验证码不正确"];
        }
        //记录下Rules    Rule::unique('users')->ignore($user->id)
        $validator = Validator::make($request->all(),[
            'username'=> 'required|unique:users|min:4',
            'password'=>'required|min:6',
            'tel'=>[
                'required','unique:users',
                "regex:/^1[34578][0-9]{9}$/"
            ]
        ],[
            'username.unique'=>'用户名已经存在!',
            'username.min'=>'用户名至少4位!',
            'password.min'=>'密码至少6位!',
            'tel.unique'=>'电话号码已经存在!',
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
//        User::create($request->except('sms'));
        User::create([
            'username'=>$request->username,
            'password'=>bcrypt($request->password),
            'tel'=>$request->tel,
            'email'=>uniqid('EM_').'@qq.com',
        ]);
        return ["status"=>"true",
                "message"=> "注册成功"];
    }

    //登录验证loginCheck
    public function loginCheck(Request $request)
    {
//        dd($request->name,$request->password);
        $validator = Validator::make($request->all(),[
            'name'=> 'required',
            'password'=>'required',
        ],[
            'name.required'=>'用户名必须填写',
            'password.required'=>'密码必须填写',
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
        if (Auth::attempt(['username'=>$request->name,'password'=>$request->password,'status'=>'0'])){
            // 认证通过...
            return [
                "status"=>"true",
                "message"=>"登录成功",
                "user_id"=>Auth::user()->id,
                "username"=>$request->username
                ];
        }else{
            return [
                "status"=>"false",
                "message"=>"登录失败!",
                ];
        }
    }
}
