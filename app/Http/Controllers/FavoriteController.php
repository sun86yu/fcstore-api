<?php

namespace App\Http\Controllers;

use App\Models\FavoriteModel;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
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
        $lists = \Db::table('t_favorite as a')->join('t_product as b', 'a.pro_id', '=', 'b.id')->select('b.*')->where(['a.user_id' => $userId])->paginate(15);
        $lists = $lists->toArray();

        return ['status' => $this->status_success, 'info' => $lists['data'], 'count' => $lists['total']];
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

        // 数据验证
        $input = [
            'pro_id' => $proId,
        ];
        $rules = [
            'pro_id' => 'required|max:10',
        ];
        $messages = [
            'required' => ':attribute 值必须填写.',
            'max' => ':attribute 长度不能超过 :max.',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return ['status' => $this->status_error, 'info' => $validator->errors()->first()];
        }

        $item = new FavoriteModel();

        $item->user_id = $userId;
        $item->pro_id = $proId;
        $item->add_time = date('Y-m-d H:i:s');

        $item->save();

        return ['status' => $this->status_success, 'info' => '收藏夹添加商品成功!'];
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
        $userId = $this->getUserId();

        if ($id != 0) {
            FavoriteModel::where(['pro_id'=> $id, 'user_id'=> $userId])->delete();
        } else {
            FavoriteModel::where('user_id', $userId)->delete();
        }

        return ['status' => $this->status_success, 'info' => '商品从收藏夹删除成功!'];
    }
}
