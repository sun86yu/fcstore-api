<?php

namespace App\Listeners;

use App\Events\ViewGoodsEvent;
use App\Models\HistoryModel;
use Illuminate\Contracts\Queue\ShouldQueue;

class ViewGoodsListener implements ShouldQueue
{
    /**
     * 任务应该发送到的队列的连接的名称
     *
     * @var string|null
     */
    public $connection = 'redis';


    public $queue = 'default';

    /**
     * 任务可以尝试的最大次数。如果不指定，任务会无限次尝试。
     *
     * @var int
     */
    public $tries = 5;

    /**
     * 超时时间。
     *
     * @var int
     */
    public $timeout = 10;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ViewGoodsEvent $event
     * @return void
     */
    public function handle(ViewGoodsEvent $event)
    {
        //
        print_r('处理事件触发!' . $event->goodId);
        $item = new HistoryModel();

        $item->user_id = $event->userId;
        $item->pro_id = $event->goodId;
        $item->view_time = date('Y-m-d H:i:s');

        $item->save();
    }

    public function failed(ViewGoodsEvent $event, $exception)
    {
        //
//        print_r('处理事件触发失败!' . $event->goodId);
    }
}
