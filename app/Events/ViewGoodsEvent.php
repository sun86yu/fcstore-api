<?php

namespace App\Events;

class ViewGoodsEvent extends Event
{
    public $goodId;
    public $userId;

    /**
     * 查看商品详情事件.往浏览记录表里添加数据
     *
     * @return void
     */
    public function __construct($userId, $goodid)
    {
        //
        $this->userId = $userId;
        $this->goodId = $goodid;
    }
}
