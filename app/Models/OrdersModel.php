<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersModel extends Model
{
    //
    protected $table = 't_orders';
    public $timestamps = false;

    // 0：未处理；1：已付款，未发货；2：已发货；3：已收货，订单完成
    public static $ST_NORMAL = 0;
    public static $ST_PAYED = 1;
    public static $ST_SENDED = 2;
    public static $ST_RECEIVED = 3;
    public static $ST_CANCLED = 4;
}
