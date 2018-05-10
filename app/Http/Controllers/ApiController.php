<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function shops()
    {
        $keyword = $_GET['keyword']??'';
        if (!empty($keyword)){
//            dd($keyword);
            $cl = new \App\SphinxClient();
            $cl->SetServer ( '127.0.0.1', 9312);
//$cl->SetServer ( '10.6.0.6', 9312);
//$cl->SetServer ( '10.6.0.22', 9312);
//$cl->SetServer ( '10.8.8.2', 9312);
            $cl->SetConnectTimeout ( 10 );
            $cl->SetArrayResult ( true );
// $cl->SetMatchMode ( SPH_MATCH_ANY);
            $cl->SetMatchMode ( SPH_MATCH_EXTENDED2);
            $cl->SetLimits(0, 1000);
//            $info = '小厨';
            $res = $cl->Query($keyword, 'shops');//搜索的索引名称 csft.conf里设置的名字
//print_r($cl);
            if ($res['total']>0){
                $ids = collect($res['matches'])->pluck('id'); //得到ID数组
                //写构造器 查询所有ID的数据
//        dd($ids);
                $data = DB::table('shop_businesses')->whereIn('id',$ids)->get();
//        dd($data);
                return $data;
            }else{
                //没有搜索到你找的内容
                return [
                    'success'=>false,
                    'data'=>'null,没有查询到<span style="color: red;">'.$keyword.'</span>相关的数据!'];
            }
        }
        $shops = DB::table('shop_businesses')->get();
        foreach ($shops as $shop){
            $shop->distance = 637;
        }
//        dd($shops);
        return $shops;
    }

    public function business(Request $request)
    {
        $id = $request->id;
        $business = DB::select('select * from `shop_businesses` where id = ?',[$id]);//->toJson();//->toArray();
        $food_cates = DB::select('select * from `food_cates` where business_id = ?',[$id]);
        foreach ($food_cates as $row){
            $foods = DB::select('select * from `foods` where business_is = ? and `food_cates_id` = ?',[$id,$row->id]);
            foreach ($foods as $item){
                $item->goods_id = $item->id;
                $item->goods_price = $item->price;
            }
            $row->goods_list = $foods;
        }
        foreach ($business as $row){
            $row->commodity = $food_cates;
        }
//        dd($shops);
//        var_dump($shops);die;
//        return json_encode($shops);
//        dd($business[0]);
//        var_dump($business[0]);die;
        return json_encode($business[0]);
    }

}
