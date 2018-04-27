<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
/*    public function __construct()
    {
        $this->middleware('auth',[
            'except'=>[]
        ]);
        $this->middleware('guest',[
            'only'=>[]
        ]);
    }*/
    //地址列表
    public function addressList()
    {
        $list = Position::where('users_id',Auth::user()->id)->get();
        return $list;
    }
    //地址添加
    public function addAddress(Request $request)
    {
        //验证手机
        $validator = Validator::make($request->all(),[
            'tel'=>[
                'required',
                'regex:/^1[35789][0-9]{9}$/',
            ]
        ],[
            'tel.regex'=>'手机号码格式不正确!',
        ]);
        if ($validator->fails()){
            //输出错误信息到前端
            $errors = $validator->errors();
            return [
                'status'=>'false',
                'message'=>$errors->first(),
            ];
        }
       /* "name" => "123""tel" => "123""provence" => "123"
      "city" => "123""area" => "123""detail_address" => "123"*/
       $request->offsetSet('users_id',Auth::user()->id);
       Position::create($request->input());
       return [
           "status"=> "true",
           "message"=> "添加成功"
       ];
    }
    //地址修改回显
    public function address(Request $request)
    {
        $address = Position::find($request->id);
        return response()->json($address);
    }
    //地址修改保存
    public function editAddress(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'tel'=>[
               'required',
               'regex:/^1[135789][0-9]{9}$/',
           ]
        ],[
            'tel.regex'=>'手机号不正确!',
        ]);
        if ($validator->fails()){
            //吧错误信息提交到前端
            $errors = $validator->errors();
            return [
                'status'=>'false',
                'message'=>$errors->first(),
            ];
        }
        DB::table('positions')
            ->where('id',$request->id)
            ->update($request->input());
        return [
            "status"=> "true",
            "message"=> "修改成功"
        ];
    }
    //地址删除
    public function destroy(Request $request)
    {
        DB::table('position')
            ->where('id',$request->id)
            ->delete();
        return [
            "status"=> "true",
            "message"=> "删除成功"
        ];
    }
}
