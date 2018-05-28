<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use App\Models\OrdersModel;
use App\Models\ProductModel;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 0);
        $userId = $this->getUserId();

        $where = ['user_id' => $userId];
        if ($status > 0){
            $where['status'] = $status;
        }
        $lists = OrdersModel::where($where)->with(['product'])->get();

        return ['status' => $this->status_success, 'info' => $lists];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        // 下单的商品列表及个数
        $userId = $this->getUserId();

        $goods_id = $request->input('gid', 0);
        $address_id = $request->input('address', 0);
        $buyNums = $request->input('buyNums', 1);
        $message = $request->input('message', '');

        if (!$goods_id || !$address_id){
            return ['status' => $this->status_error, 'info' => '系统错误请重试'];
        }

        $goodsInfo = \Db::table('t_product')->find($goods_id);
        if ($buyNums > $goodsInfo->remain_cnt){
            return ['status' => $this->status_error, 'info' => '库存不足'];
        }

        $orderData = [
            'user_id' => $userId,
            'create_time' => date('Y-m-d H:i:s'),
            'status' => 1,
            'real_address_id' => 1,
            'pay_money' => $goodsInfo->price * $buyNums,
            'order_code' => $userId . $this->getOrderCode()
        ];
        $orderProData = [
            'pro_id' => $goodsInfo->id,
            'pro_name' => $goodsInfo->pro_name,
            'cat_id' => $goodsInfo->cat_id,
            'info' => $goodsInfo->info,
            'price' => $goodsInfo->price,
            'pro_img' => $goodsInfo->pro_img,
            'buy_num' => $buyNums
        ];
        $params = [
            'id' => $goods_id,
            'remain_cnt' => $goodsInfo->remain_cnt
        ];

        \Db::transaction(function () use ($params, $orderData, $orderProData) {
            $where = [
                'id' => $params['id'],
                'remain_cnt' => $params['remain_cnt']
            ];
            //减库存
            \Db::table('t_product')->where($where)->update(['remain_cnt' => $params['remain_cnt']- 1]);
            $order_id = \Db::table('t_orders')->insertGetId($orderData);
            $orderProData['order_id'] = $order_id;
            \Db::table('t_order_product')->insert($orderProData);
        });

        return ['status' => $this->status_success, 'info' => $orderData['order_code']];
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $code = $request->input('code');
        //
        $where = [
            'order_code' => $code
        ];
        $result = OrdersModel::where($where)->with(['product'])->first();

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

    function getOrderCode(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}
