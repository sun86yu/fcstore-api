<?php

namespace App\Http\Controllers;

use App\Models\AddressModel;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    //
    public function index()
    {
        $userId = $this->getUserId();
        $lists = AddressModel::where(['user_id' => $userId])->get();

        return ['status' => $this->status_success, 'info' => $lists];
    }

    public function detail($id)
    {
        return ['status' => $this->status_success, 'info' => AddressModel::find($id)];
    }

    public function create(Request $request)
    {
        $userId = $this->getUserId();

        // 设置地址信息
        $adProvince = $request->input('ad_province', '');
        $adCity = $request->input('ad_city', '');
        $adDetail = $request->input('ad_detail', '');
        $recName = $request->input('rec_name', '');
        $recPhone = $request->input('rec_phone', '');

        // 数据验证
        $input = [
            'ad_province' => $adProvince,
            'ad_city' => $adCity,
            'ad_detail' => $adDetail,
            'rec_name' => $recName,
            'rec_phone' => $recPhone,
        ];
        $rules = [
            'ad_province' => 'max:3',
            'ad_city' => 'max:5',
            'ad_detail' => 'max:500',
            'rec_name' => 'max:45',
            'rec_phone' => 'max:20',
        ];
        $messages = [
            'max' => ':attribute 长度不能超过 :max.',
            'email' => ':attribute 不是正确的邮箱格式.',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return ['status' => $this->status_error, 'info' => $validator->errors()->first()];
        }

        $item = new AddressModel();

        $item->user_id = $userId;
        $item->ad_province = $adProvince;
        $item->ad_city = $adCity;
        $item->ad_detail = $adDetail;
        $item->rec_name = $recName;
        $item->rec_phone = $recPhone;
        $item->is_default = 0;

        $item->save();

        return ['status' => $this->status_success, 'info' => '地址添加成功!'];
    }

    public function update(Request $request)
    {
        // 设置地址信息
        $id = $request->input('id', '');
        $adProvince = $request->input('ad_province', '');
        $adCity = $request->input('ad_city', '');
        $adDetail = $request->input('ad_detail', '');
        $recName = $request->input('rec_name', '');
        $recPhone = $request->input('rec_phone', '');

        // 数据验证
        $input = [
            'ad_province' => $adProvince,
            'ad_city' => $adCity,
            'ad_detail' => $adDetail,
            'rec_name' => $recName,
            'rec_phone' => $recPhone,
        ];
        $rules = [
            'ad_province' => 'max:3',
            'ad_city' => 'max:5',
            'ad_detail' => 'max:500',
            'rec_name' => 'max:45',
            'rec_phone' => 'max:20',
        ];
        $messages = [
            'max' => ':attribute 长度不能超过 :max.',
            'email' => ':attribute 不是正确的邮箱格式.',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return ['status' => $this->status_error, 'info' => $validator->errors()->first()];
        }

        AddressModel::where('id', $id)
            ->update([
                'ad_province' => $adProvince,
                'ad_city' => $adCity,
                'ad_detail' => $adDetail,
                'rec_name' => $recName,
                'rec_phone' => $recPhone,
            ]);

        return ['status' => $this->status_success, 'info' => '地址信息变更成功!'];
    }

    public function store($id)
    {
        // 将某个地址设为默认
        $userId = $this->getUserId();

        AddressModel::where('user_id', $userId)
            ->update(['is_default' => 0]);

        AddressModel::where('id', $id)
            ->update(['is_default' => 1]);

        return ['status' => $this->status_success, 'info' => '默认地址设置成功!'];
    }

    public function destroy($id)
    {
        AddressModel::destroy($id);

        return ['status' => $this->status_success, 'info' => '地址删除成功!'];
    }
}
