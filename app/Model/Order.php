<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Nov 2018 09:28:16 +0000.
 */

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Reliese\Database\Eloquent\Model as Eloquent;
use think\Exception;

/**
 * App\Model\Order
 *
 * @property int $order_id 订单索引id
 * @property int $company_id 企业id
 * @property int $order_flag 订单归属：0旗舰店；1云店
 * @property int $order_type 订单类型 0普通订单；1自提订单
 * @property int $order_sn 订单编号
 * @property int $pay_sn 支付单号
 * @property int $shop_id 旗舰店id
 * @property string|null $shop_name 云店名称
 * @property int $store_id 云店id
 * @property string|null $store_name 云店名称
 * @property int $buyer_id 买家id
 * @property string $buyer_name 买家姓名
 * @property int $add_time 订单生成时间
 * @property int $payment_code 支付方式id
 * @property int|null $payment_time 支付(付款)时间
 * @property int $finnshed_time 订单完成时间
 * @property float $goods_amount 商品总价格
 * @property float $order_amount 订单总价格
 * @property float $pd_amount 预存款支付金额
 * @property float|null $shipping_fee 运费
 * @property int|null $evaluation_state 评价状态 0未评价，1已评价，2已过期未评价
 * @property int $order_state 订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;50:退款中
 * @property bool|null $refund_state 退款状态:0是无退款,1是部分退款,2是全部退款
 * @property bool|null $lock_state 锁定状态:0是正常,大于0是锁定,默认是0
 * @property string|null $deleted_at
 * @property float|null $refund_amount 退款金额
 * @property int|null $delay_time 延迟时间,默认为0
 * @property string|null $shipping_code 物流单号
 * @property int $shipping_type 物流方式0物流配送；1到店自提
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Model\Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereBuyerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereDelayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereEvaluationState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereFinnshedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereGoodsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereLockState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereOrderFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereOrderState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order wherePaySn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order wherePaymentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order wherePaymentTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order wherePdAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereRefundState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereShippingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereShippingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereShippingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Order whereStoreName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Model\Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Model\Order withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Model\OrderShopGood[] $shop_goods
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Model\OrderStoreGood[] $store_goods
 * @property-read \App\Model\CoPayment $co_payment
 */
class Order extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = '7r_order';
	protected $primaryKey = 'order_id';
	public $timestamps = false;
    //order_flag
	const ORDER_FLAG_SHOP = 0;
	const ORDER_FLAG_STORE = 1;
	//order_state
    const ORDER_STATUS_CANCEL = 0;
    const ORDER_STATUS_NOT_PAY = 10;
    const ORDER_STATUS_PAY = 20;
    const ORDER_STATUS_SEND = 30;
    const ORDER_STATUS_RECEIVE = 40;
    const ORDER_STATUS_REFUND = 50;
    //order_type
    const ORDER_TYPE_STIFF = 1;
    const ORDER_TYPE_PUB = 0;
    //shipping_type
    const SHIPPING_TYPE_STIFF = 1;
    const SHIPPING_TYPE_SEND = 0;

	protected $casts = [
		'company_id' => 'int',
		'order_flag' => 'int',
		'order_type' => 'int',
		'order_sn' => 'int',
		'pay_sn' => 'int',
		'shop_id' => 'int',
		'store_id' => 'int',
		'buyer_id' => 'int',
		'add_time' => 'datetime:Y-m-d H:i',
		'payment_code' => 'int',
		'payment_time' => 'int',
		'finnshed_time' => 'datetime:Y-m-d H:i',
		'goods_amount' => 'float(10,2)',
		'order_amount' => 'float(10,2)',
		'pd_amount' => 'float(10.2)',
		'shipping_fee' => 'float',
		'evaluation_state' => 'int',
		'order_state' => 'int',
		'refund_state' => 'bool',
		'lock_state' => 'bool',
		'refund_amount' => 'float(10,2)',
		'delay_time' => 'datetime:Y-m-d H:i',
		'shipping_type' => 'int'
	];

	protected $fillable = [
		'company_id',
		'order_flag',
		'order_type',
		'order_sn',
		'pay_sn',
		'shop_id',
		'shop_name',
		'store_id',
		'store_name',
		'buyer_id',
		'buyer_name',
		'add_time',
		'payment_code',
		'payment_time',
		'finnshed_time',
		'goods_amount',
		'order_amount',
		'pd_amount',
		'shipping_fee',
		'evaluation_state',
		'order_state',
		'refund_state',
		'lock_state',
		'refund_amount',
		'delay_time',
		'shipping_code',
		'shipping_type'
	];
    protected $dates = ['delete_at'];

    /**
     * 订单列表
     * @param $orders
     * @param $param
     * @param $company_id
     * @return mixed
     */
	static function order($orders,$param,$company_id)
    {

        $orders = $orders->whereCompanyId($company_id);

        if(!empty($param['store_id'])){
            if($param['store_id'] == 999){
                $orders = $orders->whereOrderFlag(self::ORDER_FLAG_SHOP);
            }else{
                $orders = $orders->whereStoreId($param['store_id']);
            }
        }
        if(!empty($param['order_sn'])){
            $orders = $orders->whereOrderSn($param['order_sn']);
        }
        if(!empty($param['order_state'])){
            $orders = $orders->whereOrderState($param['order_state']);
        }
        if(!empty($param['start_time'])){
            $orders = $orders->where('add_time','>=',strtotime($param['start_time']));
        }
        if(!empty($param['end_time'])){
            $orders = $orders->where('add_time','<=',strtotime($param['end_time']));
        }
        if(!empty($param['shipping_type'])){
            $orders = $orders->whereShippingType($param['shipping_type']);
        }
        if(!empty($param['payment_code'])){
            $orders = $orders->wherePaymentCode($param['payment_code']);
        }
        if(!empty($param['order_type'])){
            $orders = $orders->whereOrderType($param['order_type']);
        }
        if(!empty($param['buyer_id'])){
            $order=$orders->whereBuyerId($param['buyer_id']);
        }
        $orders = $orders->select('order_id','order_sn','add_time','order_state','order_type','payment_code','shipping_type','order_amount','order_flag','shipping_code','shipping_fee')
            ->forPage($param['page'],$param['limit'])->withTrashed()->get();

        foreach ($orders as $order){
            if($order->order_flag == self::ORDER_FLAG_SHOP){
                $order->goods = $order->shop_goods()->select('goods_name','goods_num','goods_image')->get();
            }else{
                $order->goods = $order->store_goods()->select('goods_name','goods_num')->get();
            }
            $order->goods_count=$order->goods->count();
            $order = self::orderCN($order);
        }

        return $orders;
    }

    static function orderCN($order)
    {
        $order_state = array(
            self::ORDER_STATUS_CANCEL => '已取消',
            self::ORDER_STATUS_NOT_PAY => "未付款",
            self::ORDER_STATUS_PAY => "已发货",
            self::ORDER_STATUS_SEND => "已送货",
            self::ORDER_STATUS_RECEIVE => "已收货",
            self::ORDER_STATUS_REFUND => "退款中"
        );

        $order_type = array(
            self::ORDER_TYPE_PUB => "普通",
            self::ORDER_TYPE_STIFF => "自提"
        );
        $shipping_type = array(
            self::SHIPPING_TYPE_SEND => "物流配送",
            self::SHIPPING_TYPE_STIFF => "到店自提"
        );

        $order->order_state_name = $order_state[$order->order_state];
        $order->order_type_name = $order_type[$order->order_type];
        $order->shipping_type_name = $shipping_type[$order->shipping_type];

        $pay_name = $order->co_payment()->first()->payment()->first();
        $order->payment_name = $pay_name->payment_name;
        return $order;
    }
    //订单信息扩展
    public function order_common()
    {
        return $this->hasOne(OrderCommon::class,'order_id','order_id');
    }
    //订单商品
    public function order_shop_goods()
    {
        return $this->hasMany(OrderShopGood::class);
    }
    //旗舰店商品
    public function shop_goods()
    {
        return $this->HasMany(OrderShopGood::class,'order_id','order_id');
    }
    //门店商品
    public function store_goods()
    {
        return $this->hasMany(OrderStoreGood::class,'order_id','order_id');
    }
    //支付方式
    public function co_payment()
    {
        return $this->hasOne(CoPayment::class,'id','payment_code');
    }

    //订单单号
    static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
            usleep(100);
        }
        \Log::warning(sprintf('find order no failed'));

        return $no;
    }

    static function orderDetail($order)
    {
        if($order->order_state == self::ORDER_STATUS_REFUND){
            $order->refund_no = RefundReturn::whereOrderId($order->order_id)->value('refund_sn');
        }

        $order->stiff_address = '';
        $store = Store::whereStoreId($order->store_id)->first();
        if($order->order_flag == self::ORDER_FLAG_STORE){
            $order->stiff_address = Store::addressCN($store->area_info).$store->store_address;//配送门店
        }

        $order_comment = OrderCommon::whereOrderId($order->order_id)->first();
        $order->order_message = $order_comment->order_message;//留言
        $order->coupon='';
        if($order_comment->coupon_code&&$order_comment->coupon_price){//如果订单使用了优惠卷
            $order->coupon=array('coupon_price'=>$order_comment->coupon_price);
        }
        if($order->order_type == self::ORDER_TYPE_PUB){
            $order->order_type_name = '配送';

            $receive_address = json_decode($order_comment->reciver_address,true);
            $order->reciver_address = Store::addressCN($order_comment->reciver_address).$receive_address['address'];
            $order->reciver_name = $order_comment->reciver_name;

            $reciver_info = unserialize($order_comment->reciver_info);
            $order->reciver_info_mobile = $reciver_info['mobile'];
        }
        if($order->order_type == self::ORDER_TYPE_STIFF){
            $order->order_type_name = '到店自提';
        }

        return $order;
    }
    /*
     * 关闭订单
     */
    public function closeOrder(){
        //判断订单是否是未付款
	    if($this->order_state!=self::ORDER_STATUS_NOT_PAY){
	        return;
        }

        DB::beginTransaction();
	    try{
            if($this->update(['order_state'=>self::ORDER_STATUS_CANCEL])){
                if($this->order_flag){//云店商品
                }else{//旗舰店商品
                    foreach ($this->shop_goods as $item){
                        //将商品库存还回去
                        if(ShopGood::find($item->shop_goods_id)->increment('goods_storage',1)){
                            //如果使用了优惠券将优惠卷还回去
                            if($this->order_common->coupon_code){
                                $mcoupon=MCoupon::where('coupon_code',$this->order_common->coupon_code)->first();
                                if($mcoupon){
                                    if($mcoupon->update(['coupon_state'=>1])){
                                        $mcoupon->coupon_info()->decrement('coupon_t_used',1);
                                        DB::commit();
                                        return true;
                                    }
                                }

                            }
                        }
                    }
                    throw new \Exception('关闭错误');
                }
            }
        }catch (\Exception $e){
	        DB::rollBack();
	        return false;
        }

    }
    /*
     * 订单收货
     * param $scene场景 1用户 2后台管理
     */
    public function received($scene=1){

        if($this->order_state!=self::ORDER_STATUS_SEND){
            return;
        }

        $user_info=array('log_user'=>$this->buyer_name,'log_role'=>0);
        if($scene==2){//后台操作
            $user_info['log_user']='';
            $user_info['log_role']=0;
        }
        DB::beginTransaction();
        try{
            if($this->update(['order_state'=>self::ORDER_STATUS_RECEIVE,'finnshed_time'=>time()])){

                $msg='订单收货';
                if(OrderLog::create(['order_id'=>$this->order_id,'log_msg'=>$msg,'log_time'=>time()
                    ,'log_user'=>$user_info['log_user'],'log_role'=>$user_info['log_role'],'log_orderstate'=>self::ORDER_STATUS_RECEIVE])){
                    DB::commit();
                    return true;
                }

            }
            throw new \Exception('收货错误');

        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }

    }
}
