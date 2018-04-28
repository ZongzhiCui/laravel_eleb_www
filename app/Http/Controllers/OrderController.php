<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Order_foods;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //订单生成addorder
    public function addorder(Request $request)
    {
        //接收数据 address_id:2   $request->address_id
        $addr = Position::find($request->address_id);
        //随机一个订单号
        $order_code = date('Ymd_H:i:s').uniqid('code');
        //根据$addr->users_id 查询购物车 status为0的所有商品
        $foods = Cart::where([
            ['users_id',$addr->users_id],
            ['status',0],
        ])
            ->get();
        //根据 菜品ID $foods[0]->goodList 查询店铺ID
        $shop_id = DB::table('shop_businesses')
            ->where('id',$foods[0]->goodsList)
            ->first();
        //添加数据到Order
            //计算订单总价
            $money = 0;
            foreach ($foods as $row){
            //     $row->goodsList   $row->goodsCount
                $food = DB::table('foods')->find($row->goodsList);
                $money += $food->price*$row->goodsCount;
            }
        DB::transaction(function () use($order_code,$shop_id,$money,$addr,$foods) {
            $order = Order::create([
                'order_code'=>$order_code,
                'users_id'=>Auth::user()->id,
                'shop_id'=>$shop_id->id,
                'shop_name'=>$shop_id->shop_name,
                'shop_img'=>$shop_id->shop_img,
                'order_price'=>$money,
                'receipt_name'=>$addr->name,
                'receipt_tel'=>$addr->tel,
                'receipt_provence'=>$addr->provence,
                'receipt_city'=>$addr->city,
                'receipt_area'=>$addr->area,
                'receipt_detail_address'=>$addr->detail_address,
            ]);
            foreach ($foods as $value){
                $food = DB::table('foods')->find($value->goodsList);
                Order_foods::create([
                    'order_id'=>$order->id,
                    'foods_id'=>$value->goodsList,
                    'foods_name'=>$food->name,
                    'foods_logo'=>$food->logo,
                    'foods_price'=>$food->price,
                    'foods_amount'=>$value->goodsCount,
                ]);
            }
        });
        $id = Order::where('order_code',$order_code)->first()->id;
        return [
            "status"=> "true",
            "message"=> "添加成功",
            "order_id"=>$id
        ];
    }
    /**订单详情**/
    public function order(Request $request)
    {
        $order = Order::find($request->id);
        //拼如.创建时间,商品数组,地址拼接
        //需要查询出商品数组 根据$order->id 从order_foods表里查
        $goods_list = Order_foods::where('order_id',$order->id)->get();
//            dd($goods_list);
        foreach ($goods_list as $good){
            $good->goods_id = $good->foods_id;
            $good->goods_name = $good->foods_name;
            $good->goods_img = $good->foods_logo;
            $good->amount = $good->foods_amount;
            $good->goods_price = $good->foods_price;
        }
        $order->order_birth_time = substr($order->created_at,0,16);
        $order->order_status = $order->order_status==0?'代付款':'已付款';
        $order->goods_list = $goods_list;
        $order->order_address = $order->receipt_provence.$order->receipt_city.$order->receipt_area.$order->receipt_detail_address.$order->receipt_name.$order->receipt_tel;
        return $order;
    }
    
    /**订单列表**/
    public function orderList()
    {
        $orders = Order::where('users_id',Auth::user()->id)->get();
        foreach ($orders as $order){
            $goods_list = Order_foods::where('order_id',$order->id)->get();
            foreach ($goods_list as $good){
                $good->goods_id = $good->foods_id;
                $good->goods_name = $good->foods_name;
                $good->goods_img = $good->foods_logo;
                $good->amount = $good->foods_amount;
                $good->goods_price = $good->foods_price;
            }
            $order->order_birth_time = substr($order->created_at,0,16);
            $order->order_status = $order->order_status==0?'代付款':'已付款';
            $order->goods_list = $goods_list;
            $order->order_address = $order->receipt_provence.$order->receipt_city.$order->receipt_area.$order->receipt_detail_address.$order->receipt_name.$order->receipt_tel;
        }
        return $orders;
    }
    
    /**支付订单**/
    public function pay(Request $request)
    {
        if (false){
            return [
                "status"=> "false",
                "message"=> "支付失败"
            ];
        }
        /**$order = Order::find($request->id)->update([
            'order_status'=>1,
        ]);*/
        return [
            "status"=> "true",
            "message"=> "支付成功"
        ];
    }
}
