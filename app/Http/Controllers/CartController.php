<?php

namespace App\Http\Controllers;

use App\Models\CartModel;
use Dingo\Api\Http\Request;

class CartController extends Controller
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
        $lists = \Db::table('t_cart as a')->join('t_product as b', 'a.pro_id', '=', 'b.id')->select('b.*', 'a.id as cart_id')->where(['a.user_id' => $userId])->get();

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
        $userId = $this->getUserId();

        // 添加商品到收藏夹
        $proId = $request->input('pro_id', 0);
        $where = [
            'user_id' => $userId,
            'pro_id' => $proId
        ];
        $info = \Db::table('t_cart')->where($where)->first();
        if ($info){
            return ['status' => $this->status_error, 'info' => '已添加此宝贝!'];
        }

        $item = new CartModel();
        $item->user_id = $userId;
        $item->pro_id = $proId;
        $item->add_time = date('Y-m-d H:i:s');

        $item->save();

        return ['status' => $this->status_success, 'info' => '购物车添加商品成功!'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id', 0);
        //
        $userId = $this->getUserId();

        if ($id != 0) {
            CartModel::destroy($id);
        } else {
            CartModel::where('user_id', $userId)->delete();
        }

        return ['status' => $this->status_success, 'info' => '商品从购物车删除成功!'];
    }
}
