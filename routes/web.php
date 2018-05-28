<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    // 获得 token.只需要帐户和密码验证
    $api->post('token', 'App\Http\Controllers\UserController@token');
    // 注册
    $api->post('register', 'App\Http\Controllers\UserController@registe');
    // 首页幻灯片信息
    $api->get('gallary', 'App\Http\Controllers\IndexController@gallary');
    // 首页商品版块信息
    $api->get('module', 'App\Http\Controllers\IndexController@module');
    // 系统分类信息
    $api->get('category', 'App\Http\Controllers\IndexController@category');
    // 系统分类信息
    $api->get('catesub', 'App\Http\Controllers\IndexController@categoryBySub');
    // 商品列表/查询
    $api->get('goods', 'App\Http\Controllers\GoodsController@index');
    // 商品详情
    $api->get('detail/{id}', 'App\Http\Controllers\GoodsController@detail');

    // 需要 token 验证才能成功的请求
    $api->group(['middleware' => 'auth:api'], function ($api) {
        $api->get('refreshtoken', 'App\Http\Controllers\UserController@refresh');
        // 获得用户详情
        $api->post('userinfo', 'App\Http\Controllers\UserController@index');
        // 更新用户信息
        $api->post('userupdate', 'App\Http\Controllers\UserController@update');

        // 获得用户收货地址列表
        $api->get('address', 'App\Http\Controllers\AddressController@index');
        // 获得用户收货地址列表
        $api->get('add-detail/{id}', 'App\Http\Controllers\AddressController@detail');
        // 新建地址
        $api->post('add-add', 'App\Http\Controllers\AddressController@create');
        // 更新地址
        $api->post('add-update', 'App\Http\Controllers\AddressController@update');
        // 设置地址为默认
        $api->get('add-default/{id}', 'App\Http\Controllers\AddressController@store');
        // 删除地址
        $api->post('add-del/{id}', 'App\Http\Controllers\AddressController@destroy');

        // 获得用户收藏夹商品列表
        $api->get('favorites', 'App\Http\Controllers\FavoriteController@index');
        // 添加商品到收藏夹
        $api->post('fav-add', 'App\Http\Controllers\FavoriteController@create');
        // 从收藏夹删除商品
        $api->post('fav-del', 'App\Http\Controllers\FavoriteController@destroy');

        // 获得用户购物车商品列表
        $api->get('carts', 'App\Http\Controllers\CartController@index');
        // 添加商品到购物车
        $api->post('cart-add', 'App\Http\Controllers\CartController@create');
        // 从购物车删除商品
        $api->post('cart-del', 'App\Http\Controllers\CartController@destroy');

        // 获得用户浏览历史记录
        $api->get('history', 'App\Http\Controllers\HistoryController@index');
        // 清空历史记录
        $api->post('history-del', 'App\Http\Controllers\HistoryController@destroy');

        $api->post('goods-order', 'App\Http\Controllers\GoodsController@goodsDetail');
        // 获得用户订单列表
        $api->get('orders', 'App\Http\Controllers\OrderController@index');
        // 获得用户订单详情
        $api->get('order-info', 'App\Http\Controllers\OrderController@show');
        // 下单
        $api->post('order-add', 'App\Http\Controllers\OrderController@create');
        // 撤单
        $api->post('order-del/{id}', 'App\Http\Controllers\OrderController@destroy');

    });
});