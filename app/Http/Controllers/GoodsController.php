<?php

namespace App\Http\Controllers;

use App\Events\ViewGoodsEvent;
use Dingo\Api\Http\Request;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{
    // 商品列表.查询 ES
    public function index(Request $request)
    {

        $cat_id = $request->input('cat_id', 0);
        $where = '';
        if ($cat_id > 0){
            $where .= ' and cat_id = ' . $cat_id;
        }
        $goods = DB::table('t_product')->whereRaw('1 = 1'.$where)->orderBy('id', 'desc')->paginate(20);

        return ['status' => $this->status_success, 'info' => $goods->toArray()['data']];

        $hosts = [
            Config::get('database.elasticsearch.host') . ':' . Config::get('database.elasticsearch.port'),
        ];
        $client = ClientBuilder::create()->setHosts($hosts)->build();

        $params = [
            'index' => Config::get('database.elasticsearch.index'),
            'type' => Config::get('database.elasticsearch.type'),
        ];

        if ($request->get('cat_id', 0) > 0) {
            $params['body']['query']['match']['category'] = $request->get('cat_id');
        }
        if ($request->get('name', '') != '') {
            $params['body']['query']['match']['pro_name'] = [
                "query" => $request->get('name'),
                "operator" => "and"
            ];
        }

        $sortFiled = $request->get('sort', '1');
        $sortOrder = $request->get('order', '1');
        $order = $sortOrder == '1' ? 'desc' : 'asc';

        $sort = [];
        if ($sortFiled == 1) {
            $sort = [
                'saled_cnt' => [
                    "order" => $order
                ]
            ];
        } else if ($sortFiled == 2) {
            $sort = [
                'price' => [
                    "order" => $order
                ]
            ];
        } else if ($sortFiled == 3) {
            $sort = [
                'create_time' => [
                    "order" => $order
                ]
            ];
        }

        $params['body']['sort'] = [
            $sort
        ];

        $response = $client->search($params);
        $result = $response['hits'];

        $total = $result['total'];
        $lists = $result['hits'];

        $goods = [];
        foreach ($lists as $loop) {
            $info = $loop['_source'];

            $item['category'] = $info['category'];
            $item['price'] = $info['price'];
            $item['remain_cnt'] = $info['remain_cnt'];
            $item['sale_cnt'] = $info['sale_cnt'];
            $item['pro_img'] = $info['pro_img'];
            $item['info'] = $info['info'];
            $item['create_time'] = $info['create_time'];
            $item['pro_name'] = $info['pro_name'];

            array_push($goods, $item);
        }

        return ['status' => $this->status_success, 'info' => $goods, 'total' => $total];
    }

    // 商品详情.访问 memcached,并设置浏览记录
    public function detail($id)
    {
        $userId = $this->getUserId();
        $cacheKey = 'Good_Detail_' . $id;

        $info = [];
        if (Cache::has($cacheKey) && Cache::get($cacheKey) !== false) {
            $info = Cache::get($cacheKey);
        } else {
            $info = DB::table('t_product')->find($id);
            // TODO:模块、常量等对应及设置

            Cache::forever($cacheKey, $info);
        }

        if ($userId > 0) {
            event(new ViewGoodsEvent($userId, $id));
            $cartCount = DB::table('t_cart')->where('user_id', $userId)->count();
            $favCount = DB::table('t_favorite')->where(['user_id'=> $userId, 'pro_id' => $id])->count();
        }

        return ['status' => $this->status_success, 'info' => $info, 'cart' => $cartCount ?? 0, 'fav' => $favCount ?? 0];
    }

    public function goodsDetail(Request $request){

        $buyParams = $request->input('buyParams', []);
        $userId = $this->getUserId();

        //商品详情
        $goods = DB::table('t_product')->find($buyParams['gid']);

        //默认收货地址
        $address = DB::table('t_address')->where(['user_id'=> $userId, 'is_default' => 1])->first();

        return ['status' => $this->status_success, 'goods' => $goods, 'address' => $address];

    }
}
