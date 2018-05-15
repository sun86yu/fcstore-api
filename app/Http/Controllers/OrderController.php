<?php

namespace App\Http\Controllers;

use App\Models\OrdersModel;
use App\Models\ProductModel;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $userId = $this->getUserId();
        $lists = OrdersModel::where(['user_id' => $userId])->get();

        return ['status' => $this->status_success, 'info' => $lists];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        // 下单的商品列表及个数
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $order = OrdersModel::find($id);
        // 商品详情
        $goodsListStr = $order['pro_info'];

        $goodsList = json_decode($goodsListStr);

        $goodsArr = [];
        $price = 0;
        foreach ($goodsList as $goodId => $loopGood) {
            array_push($goodsArr, $goodId);
        }

        $goodsInfoList = ProductModel::select('pro_name as name', 'pro_img as images', 'info')->find($goodsArr);

        $result = [];
        foreach ($goodsList as $goodId => $loopGood) {
            foreach ($goodsInfoList as $loopInfo) {
                if ($loopInfo['id'] == $goodId) {
                    $result['goods'][$goodId]['count'] = $loopGood['cnt'];
                    $result['goods'][$goodId]['price'] = $loopGood['price'];
//                    $result['goods'][$goodId]['info'] = $loopInfo;

                    // TODO:模块、常量等对应及设置

                    $price += $result[$goodId]['count'] * $result[$goodId]['price'];

                    break;
                }
            }
        }
        $result['price'] = $price;

        return ['status' => $this->status_success, 'info' => $result];
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        OrdersModel::where('id', $id)
            ->update(['status' => OrdersModel::$ST_CANCLED]);

        return ['status' => $this->status_success, 'info' => '取消订单成功!'];
    }
}
