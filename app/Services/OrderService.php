<?php

namespace App\Services;

use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\OrderUnavailableException;
use App\Model\MAddress;
use App\Model\MCoupon;
use App\Model\Member;
use App\Model\Order;
use App\Model\ShopCart;
use App\Model\ShopGood;
use Carbon\Carbon;
use DB;

class OrderService
{
    public function store(Member $member, MAddress $address, $remark, $carts_id, MCoupon $coupon = null,$company_id,$shop_id)
    {
        if ($coupon) {
            $coupon->checkAvailable($member);
        }
        // 开启一个数据库事务
        $order = DB::transaction(function () use ($member, $address, $remark, $carts_id, $coupon,$company_id,$shop_id) {

            $order = new Order([
                'total_amount' => 0,
                'order_sn' => Order::findAvailableNo(),
                'company_id' => $company_id
            ]);
            $order->buyer_id = $member->member_id;
            $order->save();

            $totalAmount = 0;
            // 遍历用户提交的 SKU
            foreach (explode(',',$carts_id) as $cart_id) {
                $cart  = ShopCart::find($cart_id);
                $good = ShopGood::whereGoodsId($cart->goods_id)->whereShopId($cart->shop_id)->first();
                // 创建一个 OrderItem 并直接与当前订单关联
                $area_info = json_decode($address->area_info,true);

                $order_common = $order->order_common()->make([
                    'order_message' => $remark,
                    'reciver_name'  => $address->true_name,
                    'reciver_info'  => serialize($address),
                    'reciver_address' => json_encode($area_info.$address->address),
                    'order_id' => $order->order_id
                ]);
                $order_common->save();

                $totalAmount += bcmul($cart->goods_price * $cart->goods_num,2);
                if ($good->decreaseStock($cart->goods_num) <= 0) {
                    throw new OrderUnavailableException('该商品库存不足');
                }

                $order_shop_goods = $order->order_shop_goods()->make([
                    'order_id' => $order->order_id,
                    'shop_goods_id' => $good->goods_id,
                    'goods_name' => $cart->goods_name,
                    'goods_price' => $cart->goods_price,
                    'goods_num' => $cart->goods_num,
                    'goods_image' => $cart->goods_image,
                    'shop_id' => $shop_id,
                    'buyer_id' => $member->member_id,
                    'gc_id' => 0
                ]);
                $order_shop_goods->save();
            }

            $order->update(['goods_amount' => $totalAmount]);//商品总价

            if ($coupon) {

                $coupon->checkAvailable($member, $totalAmount);

                $totalAmount = $coupon->getAdjustedPrice($totalAmount);

                $order_common->update(['coupon_code' => $coupon->coupon_code,'coupon_price' => $coupon->coupon_price]);
                $coupon->update(['coupon_order_id' => $order->order_id]);

            }
            // 更新订单总金额
            $order->update(['order_amount' => $totalAmount]);

            ShopCart::whereIn('cart_id',$carts_id)->delete();

            return $order;
            
        });

        // 这里我们直接使用 dispatch 函数
        //dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
}
