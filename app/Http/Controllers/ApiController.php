<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function shops()
    {
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
