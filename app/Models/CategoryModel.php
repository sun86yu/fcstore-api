<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CategoryModel extends Model
{
    //
    protected $table = 't_category';

    public $timestamps = false;

    public static function getChild($id)
    {
        $catKey = 'Cat_Child_' . $id;
        $cat = Cache::get($catKey);
        if ($cat == null) {

            $where = [
                ['is_active', '=', 1],
                ['cat_parent', '=', $id]
            ];

            $cat = self::where($where)->get();

            Cache::forever($catKey, $cat);
        }

        return $cat;
    }

    /**
     * 获得所有1级
     * @return mixed
     */
    public static function getCategory()
    {
        $catKey = 'Cat_Top';
        $cat = Cache::get($catKey);
        if ($cat == null) {

            $where = [
                ['is_active', '=', 1],
                ['cat_level', '=', 1]
            ];

            $cat = self::where($where)->get();

            Cache::forever($catKey, $cat);
        }

        return $cat;
    }
    /**
     * 推荐分类
     * @return mixed
     */
    public static function getCategoryTj()
    {

            $where = [
                ['is_active', '=', 1],
                ['cat_level', '=', 2]
            ];

            $cat = self::where($where)->orderBy(\DB::raw('RAND()'))->take(10)->get();

        return $cat;
    }
    /**
     * 二级分类
     * @return mixed
     */
    public static function getChildBySub($id)
    {
        $catInfo = self::find($id);

        $where = [
            ['is_active', '=', 1],
            ['cat_parent', '=', $catInfo['cat_parent']]
        ];
        $cat = self::where($where)->get();

        return $cat;
    }
}
