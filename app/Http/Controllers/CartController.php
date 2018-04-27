<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    //去结算
    public function addCart(Request $request)
    {
        $foods = $request->goodsList;
        $counts = $request->goodsCount;
        $user_id = Auth::user()->id;
        //查询出当前用户之前的订单吧 状态改为2.表示订单失效.(后面状态1表示成功付款)
        $row = DB::table('carts')
            ->where('users_id',$user_id)
            ->get();
        if ($row != null){//有数据在修改状态为2
            foreach ($row as $val){
                if ($val->status == 0){
//                    dd($val);
                    DB::table('carts')
                        ->where('id',$val->id)
                        ->update([
                            'status'=>2,
                        ]);
                }
            }
        }

        foreach ($foods as $key=>$food){
            Cart::create([
                'goodsList'=>$food,
                'goodsCount'=>$counts[$key],
                'users_id'=>$user_id,
            ]);
        }
        return [
            "status"=> "true",
            "message"=> "生成订单!"
        ];
    }
    //订单详情
    public function cart(Request $request)
    {
        //查询状态等于0的
        $foods = Cart::where([
            ['users_id',Auth::user()->id],
            ['status',0],
        ])
            ->get();
        $goods = [];
        $totalCost = 0;
        foreach ($foods as $food){
//            $food->goodsList
//            $food->goodsCount
            $row = DB::table('foods')
                ->where('id',$food->goodsList)
                ->first();
            $food->goods_id = $row->id;
            $food->goods_name = $row->name;
            $food->goods_img = $row->logo;
            $food->amount = $food->goodsCount;
            $food->goods_price = $row->price;
            $totalCost += $food->amount*$food->goods_price;
        }
        $goods['goods_list'] = $foods;
        $goods['totalCost'] = $totalCost;
        return $goods;
    }
}
