<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2018/12/3
 * Time: 9:33 AM
 */
namespace  App\Modules\Shop\Http\Controllers\Order;
use App\Http\Controllers\ShopBascController;
use App\Model\CouponShop;
use App\Model\MAddress;
use App\Model\MCoupon;
use App\Model\Order;
use App\Model\ShopCart;
use App\Model\ShopGood;
use App\Model\WebShop;
use App\Modules\Shop\Requests\OrderRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Log;

class OrderController extends ShopBascController{

    public function orderCreate(Request $request,OrderService $orderService)
    {
        $message=array(
            'address_id.required' => '请填写地址id',
            'address_id.exists' => '地址不存在',
            'cart_id.required' => '请填写购物车id'
        );
        $validator=Validator::make($request->all(),[
            'address_id'     => ['required', Rule::exists('7r_m_address', 'address_id')->where('member_id', $this->member->member_id)],
            'cart_id'          => ['required',
                'required',
                function ($attribute, $value, $fail) {
                    foreach (explode(',',$value) as $val){
                        if(!$shop_cart = ShopCart::whereCartId($val)->first()){
                            $fail('该购物车参数不正确');
                            return;
                        }
                        if($shop_cart->shop_id != $this->shop_id){
                            $fail('该购物车参数不正确');
                            return;
                        }
                        if($shop_cart->buyer_id != $this->member->member->id){
                            $fail('该购物车参数不正确');
                            return;
                        }
                        if (!$shop_good = ShopGood::whereShopId($this->shop_id)->whereGoodsId($shop_cart->goods_id)->whereCompanyId($this->company_id)->first()) {
                            $fail('该商品不存在');
                            return;
                        }
                        if (!$shop_good->goods_state == ShopGood::GOOD_STATE_DOWN) {
                            $fail('该商品未上架');
                            return;
                        }
                        if ($shop_good->goods_state == ShopGood::GOOD_STATE_SALE_OUT) {
                            $fail('该商品已售完');
                            return;
                        }
                        if ($shop_good->goods_storage < $shop_cart->goods_num) {
                            $fail('该商品数量不足');
                            return;
                        }
                    }
                },
            ],
        ],$message);
        if($validator->fails()){
            return $this->failed($validator->errors()->first());
        }

        $address = MAddress::find($request->input('address_id'));
        $coupon = null;

        // 如果用户提交了优惠码
        if ($code = $request->input('coupon_code')) {
            $coupon = MCoupon::whereCouponCode($code)->whereCouponOwnerId($this->member->member_id)->first();
            if (!$coupon) {
                return $this->failed('优惠券不存在');
            }
            if(!CouponShop::whereShopId($this->shop_id)->whereCouponTId($coupon->coupon_t_id)->exists()){
                return $this->failed('优惠券不存在');
            }
        }

        return $orderService->store($request->user('member'), $address, $request->input('order_message'), $request->input('cart_id'), $coupon,$this->company_id,$this->shop_id);
    }
    /*
     * 订单详情
     */
    public function orderDetail(Request $request,Order $order){
            $order_id=$request->input('order_id');
            if(!$order_id){
                return $this->failed('订单id不能为空');
            }
            $order_info=$order->with(['shop_goods'=>function($query){
                $query->select(['order_id','shop_goods_id','goods_name','goods_price','goods_num','goods_image','goods_pay_price','goods_type','promotions_id']);
            }])->find($order_id);
            if(empty($order_info)){
                return $this->failed('无效数据');
            }
            $order_info = Order::orderCN($order_info);
            $data=$order->orderDetail($order_info);



        return $this->success($data);
    }
    /*
     * 取消订单
     */
    public function orderClose(Request $request){
        $order_id=$request->input('order_id');
        if(!$order_id){
            return $this->failed('订单id不能为空');
        }
        $order=Order::find($order_id);
        if(!$order){
            return $this->failed('无效数据');
        }
        if($order->closeOrder()){
            return $this->message('取消订单成功');
        }
        return $this->failed('取消订单失败');
    }
    /*
     * 收货
     */
    public function received(Request $request){
        $order=Order::find($request->input('order_id',0));
        if(!$order){
            return $this->failed('无效数据');
        }
        if($order->received()){
            return $this->message('收货成功');
        }
        return $this->failed('收货失败');
    }
}
