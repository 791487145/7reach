<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Nov 2018 02:47:51 +0000.
 */

namespace App\Model;

use Carbon\Carbon;
use Reliese\Database\Eloquent\Model as Eloquent;

class CouponTemplate extends Eloquent
{
	protected $table = '7r_coupon_template';
	protected $primaryKey = 'coupon_t_id';
	public $timestamps = false;

	protected $casts = [
		'coupon_t_id' => 'int',
		'coupon_t_start_date' => 'datetime:Y-m-d H:i',
		'coupon_t_end_date' => 'datetime:Y-m-d H:i',
		'coupon_t_price' => 'float',
		'coupon_t_limit' => 'float',
		'coupon_t_total' => 'int',
		'coupon_t_giveout' => 'int',
		'coupon_t_used' => 'int',
		'coupon_t_add_date' => 'datetime:Y-m-d H:i',
		'coupon_t_eachlimit' => 'int',

	];

	protected $fillable = [
		'coupon_t_id',
		'coupon_t_title',
        'company_id',
		'coupon_t_desc',
		'coupon_t_start_date',
		'coupon_t_end_date',
		'coupon_t_price',
		'coupon_t_limit',
		'coupon_t_state',
		'coupon_t_total',
		'coupon_t_giveout',
		'coupon_t_used',
		'coupon_t_add_date',
		'coupon_t_eachlimit',
		'coupon_t_style',
		'coupon_t_recommend',
        'use_range',
	];
	/*
	 * 优惠券旗舰店关联
	 */
	public function shop(){
	    return $this->belongsToMany(WebShop::class,'7r_coupon_shop','coupon_t_id','shop_id');
    }
    /*
     * 优惠券云店关联
     */
    public function store(){
        return $this->belongsToMany(Store::class,'7r_coupon_store','coupon_t_id','store_id');
    }
    /*
     * 优惠券列表
     */
    public function list($request,$company_id){
        $where['company_id']=$company_id;
        if($request->input('coupon_t_title')){ //优惠券名称
            $where['coupon_t_title']=$request->input('coupon_t_title');
        }
        if($request->input('coupon_t_state')){//优惠卷状态
            $where['coupon_t_state']=$request->input('coupon_t_state');
        }
        $list=$this->where($where)->forPage($request->input('page',1),$request->input('limit',10))->get();
        $list->each(function ($item,$key){
            $item->coupon_t_state=$item->coupon_t_state==1?'有效':'过期';
            $item->use_range=$item->use_range==1?"全部店铺":'指定店铺';
            $limit=$item->coupon_t_limit?"订单满{$item->coupon_t_limit}元,":'无门槛,';
            $item->pre_content="{$limit}减免金额：{$item->coupon_t_price}元";
        });
        return $list;
    }
    /*
     * 检查优惠卷是否可用
     * @param $type 1领劵时
     */
    public function checkAvailable($member,$type=1,$orderAmount=null){

        if($type!=1){//不是领劵时验证
            //优惠卷可使用时间
            if($this->coupon_t_start_date&&$this->coupon_t_start_date->gt(Carbon::now())){
                throw new \Exception('该优惠券现在还不能使用');
            }
            //优惠卷过期
            if($this->coupon_t_state==2||($this->coupon_t_end_date&&$this->coupon_t_end_date->lt(Carbon::now()))){
                throw new \Exception('优惠券已过期');
            }
        }else{//领劵时验证
            //优惠券可发放总量
            if($this->coupon_t_total<=0||$this->coupon_t_giveout>$this->coupon_t_total){
                throw new \Exception('该优惠已领完');
            }
            //优惠券限领次数
            if($this->coupon_t_eachlimit){
               //获取当前优惠卷会员持有数量
               $count=$member->coupon_count($this->coupon_t_id);
               if($this->coupon_t_eachlimit-$count<=0){
                   throw new \Exception('已超过限领次数');
               }
            }
        }

    }
}
