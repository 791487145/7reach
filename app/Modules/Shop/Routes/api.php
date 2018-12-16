<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/shop', function (Request $request) {

})->middleware('auth:api');
Route::prefix('shop')->group(function() {
    Route::namespace('Member')->group(function () {
        //发送短信
        Route::post('sendsms', 'LoginController@sendSms')->name('sendSms');
        //会员分组
        Route::prefix('member')->group(function () {
            //会员登录
            Route::post('login', 'LoginController@login')->name('shopLogin');
            //会员中心
            Route::post('home', 'MemberController@home')->name('memberHome');
            //会员我的订单
            Route::post('order', 'MemberController@myOrder')->name('mamberOrder')->name('memberOrder');
            //我的喜欢
            Route::post('favorites', 'MemberController@favorites')->name('memberFavorites');
            //我的消息
            Route::post('message', 'MemberController@myMessage')->name('memberMessage');
            //会员我的资料
            Route::match(['get', 'post'], 'info', 'MemberController@info')->name('memberInfo');
            //会员我的卡卷
            Route::post('coupon','MemberController@coupon')->name('memberCoupon');
            //会员领劵
            Route::post('getcoupon', 'MemberController@getCoupon')->name('memberGetCoupon');
            //会员地址分组
            Route::prefix('addresses')->group(function () {
                //地址列表
                Route::post('list', 'MemberAddressesController@list')->name('addressesList');
                //添加地址
                Route::post('add', 'MemberAddressesController@add')->name('addressesAdd');
                //编辑地址
                Route::match(['get', 'post'], 'edit', 'MemberAddressesController@edit')->name('addressesEdit');
                //删除地址
                Route::post('delete', 'MemberAddressesController@delete')->name('addressesDelete');
            });
        });
    });
    //购物车
    Route::namespace('Cart')->group(function () {

        Route::post('carts', 'CartController@carts')->name('carts');
        Route::prefix('cart')->group(function () {
            Route::post('create', 'CartController@cardCreate')->name('cardCreate');
            Route::post('delete', 'CartController@cartDelete')->name('cartDelete');
            Route::post('submit', 'CartController@cardSubmit')->name('cardSubmit');
        });
    });

    //订单
    Route::namespace('Order')->group(function () {

        Route::prefix('order')->group(function () {
            Route::post('create', 'OrderController@orderCreate')->name('orderCreate');
            //订单详情
            Route::post('info','OrderController@orderDetail')->name('orderDetail');
            //取消订单
            Route::post('close','OrderController@orderClose')->name('orderClose');
            //收货
            Route::post('received','OrderController@received')->name('orderReceived');
        });
    });
    //首页
    Route::namespace('Home')->group(function () {
        Route::prefix('home')->group(function () {
            //旗舰店首页
            Route::post('/', 'HomeController@home')->name('shopHome');
            //领劵中心
            Route::post('receive', 'HomeController@receiveCoupon')->name('receiveCoupon');
        });
    });
    //商品
    Route::namespace('Goods')->group(function () {
        Route::prefix('goods')->group(function () {
            //商品分组列表
            Route::post('grouplist', 'GoodsController@groupList')->name('shopGrouplist');
            //商品列表
            Route::post('list','GoodsController@getGroupGoods')->name('shopGoodsList');
            //商品详情
            Route::post('info','GoodsController@info')->name('shopGoodsInfo');
        });
    });

});



