<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 03 Dec 2018 09:03:33 +0000.
 */

namespace App\Model;

use App\Exceptions\CouponCodeUnavailableException;
use Carbon\Carbon;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * App\Model\MCoupon
 *
 * @property int $coupon_id 主键id
 * @property string|null $coupon_code 优惠券编码
 * @property int $coupon_t_id 优惠卷编号
 * @property string $coupon_title 优惠券名称
 * @property string $coupon_desc 优惠券使用说明
 * @property int $coupon_start_date 优惠券有效期开始时间
 * @property int $coupon_end_date 优惠券有效期结束时间
 * @property int $coupon_price 优惠券金额
 * @property float $coupon_limit 优惠券使用门槛 订单满xx
 * @property int $coupon_state 优惠券状态(1-未用,2-已用,3-过期,4-收回)
 * @property int|null $coupon_active_date 优惠券发放日期
 * @property int $coupon_owner_id 优惠券所有者id
 * @property string $coupon_owner_name 优惠券所有者名称
 * @property int|null $coupon_order_id 使用该优惠券的订单编号
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponActiveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponTId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MCoupon whereCouponTitle($value)
 * @mixin \Eloquent
 */
class MCoupon extends Eloquent
{
	protected $table = '7r_m_coupon';
	protected $primaryKey = 'coupon_id';
	public $timestamps = false;

	//coupon_state
	const COUPON_STATE_NOT_USE = 1;
    const COUPON_STATE_USED = 2;
    const COUPON_STATE_OVER_TIME = 3;
    const COUPON_STATE_RECYCLE = 4;

	protected $casts = [
		'coupon_t_id' => 'int',
		'coupon_start_date' => 'int',
		'coupon_end_date' => 'int',
		'coupon_price' => 'int',
		'coupon_limit' => 'float',
		'coupon_state' => 'int',
		'coupon_active_date' => 'int',
		'coupon_owner_id' => 'int',
		'coupon_order_id' => 'int'
	];

	protected $fillable = [
		'coupon_code',
		'coupon_t_id',
		'coupon_title',
		'coupon_desc',
		'coupon_start_date',
		'coupon_end_date',
		'coupon_price',
		'coupon_limit',
		'coupon_state',
		'coupon_active_date',
		'coupon_owner_id',
		'coupon_owner_name',
		'coupon_order_id'
	];
	/*
	 * 优惠券信息
	 */
    public function coupon_info(){
        return $this->hasOne(CouponTemplate::class,'coupon_t_id','coupon_t_id');
    }
	//效验是否可以使用
    public function checkAvailable(Member $user, $orderAmount = null)
    {
        if (!$this->enabled) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        if ($this->coupon_start_date && strtotime($this->coupon_start_date)->gt(Carbon::now())) {
            throw new CouponCodeUnavailableException('该优惠券现在还不能使用');
        }

        if ($this->coupon_end_date && strtotime($this->coupon_end_date)->lt(Carbon::now())) {
            throw new CouponCodeUnavailableException('该优惠券已过期');
        }
        if ($this->coupon_state == self::COUPON_STATE_RECYCLE) {
            throw new CouponCodeUnavailableException('该优惠券已回收');
        }

        if (!is_null($orderAmount) && $orderAmount < $this->coupon_limit) {
            throw new CouponCodeUnavailableException('订单金额不满足该优惠券最低金额');
        }
    }
    //订单金额与优惠券
    public function getAdjustedPrice($orderAmount)
    {
        // 固定金额
        if ($this->coupon_price > 0) {
            // 为了保证系统健壮性，我们需要订单金额最少为 0.01 元
            return max(0.01, bcsub($orderAmount,$this->coupon_price,2));
        }

        //return number_format($orderAmount * (100 - $this->value) / 100, 2, '.', '');
    }


}
