<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    // 首页幻灯片列表
    public function gallary()
    {
        $cacheKey = 'home_gallary';

        $lists = [];
        if (Cache::has($cacheKey) && Cache::get($cacheKey) !== false) {
            $lists = Cache::get($cacheKey);
        } else {
            $where = [
                ['is_galarry', '=', 1],
                ['status', '=', 1],
            ];
            $lists = DB::table('t_news')->select('id', 'title', 'head_img', 'is_link', 'link_url')
                ->where($where)->orderBy('id', 'desc')->get();

            Cache::forever($cacheKey, $lists);
        }

        return ['status' => $this->status_success, 'info' => $lists];
    }

    // 获得首页模块列表
    public function module()
    {
        // TODO:首页模块整理
        return ['info' => []];
    }

    // 获得系统分类列表
    public function category(Request $request)
    {
        $topId = $request->get('top', 0);

        if ($topId > 0){
            if ($topId == 9999){
                $lists = CategoryModel::getCategoryTj();
            }else{
                $lists = CategoryModel::getChild($topId);
            }
            $listTjs = [];
        }else{
            $lists = CategoryModel::getCategory();
            $listTjs = CategoryModel::getCategoryTj();
        }

        return ['status' => $this->status_success, 'info' => $lists, 'tj' => $listTjs];
    }
    // 获得分类列表
    public function categoryBySub(Request $request)
    {
        $topId = $request->get('cat_id', 0);

        if ($topId > 0){
            $lists = CategoryModel::getChildBySub($topId);
            $listTjs = [];
        }else{
            $lists = CategoryModel::getCategory();
            $listTjs = CategoryModel::getCategoryTj();
        }

        return ['status' => $this->status_success, 'info' => $lists, 'tj' => $listTjs];
    }
}
