<?php

namespace App\Listeners;

use App\Events\GoodsChange;
use App\Model\ShopCart;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
class ClearCart
{
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
     * @param  GoodsChange  $event
     * @return void
     */
    public function handle(GoodsChange $event)
    {
        //
        if(empty($event->goods)){
            return;
        }
        if($event->goods instanceof Collection){
           $event->goods->each(function($item,$key) {
               //清空旗舰店购物车商品
               ShopCart::where('goods_id',$item->shopGoods()->value('shop_goods_id'))->delete();
               //清空云店购物车商品
            });

        }else{
            //清空旗舰店购物车商品
            ShopCart::where('goods_id',$event->goods->shopGoods()->value('shop_goods_id'))->delete();
            //清空云店购物车商品

        }

    }
}
