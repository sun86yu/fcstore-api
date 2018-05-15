<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    protected $status_success = 200;
    protected $status_not_found = 400;
    protected $status_error = 500;

    public function getUserId()
    {
        // 获得用户ID
        $user = Auth::user();
        if ($user != null && ($user instanceof User)) {
            return $user['id'];
        }
        return 0;
    }

    public function generatePwd($userPwd)
    {
        return password_hash($userPwd, PASSWORD_BCRYPT);
    }
}
