<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class UserController extends Controller
{
    protected $jwt;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $jwt)
    {
        //
        $this->jwt = $jwt;
    }

    public function token(Request $request)
    {
        if (!$token = $this->jwt->attempt($request->only('user_tel', 'password'))) {
            return ['status' => $this->status_error, 'info' => '用户登录信息错误!'];
        }

        UserModel::where('user_tel', $request->input('user_tel'))
            ->update(['last_login_time' => date('Y-m-d H:i:s')]);

        return ['status' => $this->status_success, 'token' => $token];
    }

    public function index()
    {
        return ['status' => $this->status_success, 'info' => Auth::user()];
    }

    public function registe(Request $request)
    {
        // 数据验证
        $input = [
            'user_tel' => $request->input('user_tel'),
            'password' => $request->input('password'),
            'user_mail' => $request->input('user_mail'),
        ];
        $rules = [
            'user_tel' => 'required|max:20',
            'password' => 'required|max:60',
            'user_mail' => 'required|max:100|email',
        ];
        $messages = [
            'required' => ':attribute 值必须填写.',
            'max' => ':attribute 长度不能超过 :max.',
            'email' => ':attribute 不是正确的邮箱格式.',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return ['status' => $this->status_error, 'info' => $validator->errors()->first()];
        }

        // 检查唯一性
        $where = [
            'user_tel' => $request->input('user_tel')
        ];
        $exsis = UserModel::where($where)->get();

        if ($exsis != null && count($exsis) > 0 && $exsis[0]['id'] > 0) {
            return ['status' => $this->status_error, 'info' => '用户电话已经存在!'];
        }

        $item = new UserModel();

        $item->user_tel = $request->input('user_tel');
        $item->user_name = $request->input('user_tel');
        $item->password = $this->generatePwd($request->input('password'));
        $item->user_mail = $request->input('user_mail');
        $item->reg_ip = $request->input('user_mail');
        $item->user_identy = $request->getClientIp();
        $item->reg_time = date('Y-m-d H:i:s');
        $item->last_login_time = date('Y-m-d H:i:s');

        $item->save();

        $token = $token = $this->jwt->attempt($request->only('user_tel', 'password'));

        return ['status' => $this->status_success, 'token' => $token];
    }

    public function update(Request $request)
    {
        $userId = $this->getUserId();

        // 设置用户信息.主要是用户名称，邮箱，密码
        $email = $request->input('user_mail', '');
        $pwd = $request->input('password', '');
        $userName = $request->input('user_name', '');

        // 数据验证
        $input = [
            'user_name' => $userName,
            'password' => $pwd,
            'user_mail' => $email,
        ];
        $rules = [
            'user_name' => 'max:20',
            'password' => 'max:60',
            'user_mail' => 'max:100|email',
        ];
        $messages = [
            'max' => ':attribute 长度不能超过 :max.',
            'email' => ':attribute 不是正确的邮箱格式.',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return ['status' => $this->status_error, 'info' => $validator->errors()->first()];
        }

        $input = [];
        if ($email != '') {
            $input['user_mail'] = $email;
        }
        if ($pwd != '') {
            $input['password'] = $this->generatePwd($pwd);
        }
        if ($userName != '') {
            $input['user_name'] = $userName;
        }

        UserModel::where('id', $userId)
            ->update($input);

        return ['status' => $this->status_success, 'info' => '用户信息更新成功!'];
    }
}
