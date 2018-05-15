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
}
