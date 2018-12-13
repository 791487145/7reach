<?php

/**
 * 会员模型
 * Created by Reliese Model.
 * Date: Wed, 14 Nov 2018 01:24:09 +0000.
 */

namespace App\Model;

use App\Http\Controllers\BaiscController;
use App\Modules\Shop\Http\Controllers\Member\MemberAddressesController;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Reliese\Database\Eloquent\Model as Eloquent;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class 7rMember
 *
 * @property int $member_id
 * @property string $member_truename
 * @property bool $member_sex
 * @property int $member_grade_id
 * @property int $member_birthday
 * @property string $member_paypwd
 * @property string $member_mobile
 * @property int $member_mobile_bind
 * @property string $member_time
 * @property string $member_login_time
 * @property string $member_old_login_time
 * @property string $member_login_ip
 * @property string $member_old_login_ip
 * @property int $source_channel
 * @property string $weixin_unionid
 * @property string $weixin_info
 * @property int $member_points
 * @property float $available_predeposit
 * @property float $freeze_predeposit
 * @property int $member_exppoints
 * @property string $member_wxopenid
 * @property int $company_id
 * @property int $store_id
 * @package App\Models
 * @property-read \App\Model\MemberGrade $grade
 * @property-read \App\Model\Store $store
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Model\MemberTag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereAvailablePredeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereFreezePredeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberExppoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberLoginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberMobileBind($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberOldLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberOldLoginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberPaypwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberTruename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereMemberWxopenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereSourceChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereWeixinInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Member whereWeixinUnionid($value)
 * @mixin \Eloquent
 */
class Member extends Authenticatable implements  JWTSubject
{
	protected $table = '7r_member';
	protected $primaryKey = 'member_id';
	public $timestamps = false;



	protected $fillable = [
		'member_truename',
		'member_sex',
		'member_grade_id',
		'member_birthday',
		'member_paypwd',
		'member_mobile',
		'member_mobile_bind',
		'member_time',
		'member_login_time',
		'member_old_login_time',
		'member_login_ip',
		'member_old_login_ip',
		'source_channel',
		'weixin_unionid',
		'weixin_info',
		'member_points',
		'available_predeposit',
		'freeze_predeposit',
		'member_exppoints',
		'member_wxopenid',
		'company_id',
		'store_id'
	];
	public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }

    /*
     * 获取来源
     */
	protected function getChannel($key){
	    $source_channel=array(
	        '1'=>'自主注册',
            '2'=>'导购招募',
            '3'=>'活动',
        );
	    return $source_channel[$key];
    }
	/*
	 * 会员标签
	 */
	public function tags(){
	    return $this->belongsToMany(MemberTag::class,'7r_mtag_member','member_id','mtag_id');
    }
    /*
     * 会员等级
     */
    public function grade(){
        return $this->hasOne(MemberGrade::class,'grade_id','member_grade_id');
    }
    /**
     * 购物车
     */
    public function cart()
    {
        return $this->belongsTo(ShopCart::class,'buyer_id','member_id');
    }
    /*
     * 所属门店
     */
    public function store(){

       return $this->hasOne(Store::class,'store_id','store_id');

    }
    /*
     * 会员地址
     */
    public function addresses(){
        return $this->hasMany(MAddress::class,'member_id','member_id');
    }
    /*
     * 会员订单
     */
    public function orders(){
        return $this->hasMany(Order::class,'buyer_id','member_id');
    }
    /*
     * 获取会员指定优惠券个数
     */
    public function coupon_count($coupon_t_id){
        return $this->belongsToMany(CouponTemplate::class,'7r_m_coupon','coupon_owner_id','coupon_t_id')
            ->wherePivot('coupon_t_id',$coupon_t_id)->count();
    }

    /*
     * 会员卡卷
     */
    public function coupons(){
        return $this->belongsToMany(CouponTemplate::class,'7r_m_coupon','coupon_owner_id','coupon_t_id');
    }
    /*
     * 会员列表
     */
    public function getList($request,$company_id){
        $where['company_id']=$company_id;
        if($request->input('member_truename')){//姓名筛选
            $where['member_truename']=$request->input('member_truename');
        }
        if($request->input('member_mobile')){//手机号筛选
            $where['member_mobile']=$request->input('member_mobile');
        }
        if($request->input('store_id')){//所属门店
            $where['store_id']=$request->input('store_id');
        }
        if($request->input('source_channel')){//来源渠道
            $where['source_channel']=$request->input('source_channel');
        }
        $list=$this->with(['grade'=>function($query){
            $query->select(['grade_id','grade_name']);
        },'store'])->where($where)->forPage($request->input('page',1),$request->input('limit',BaiscController::LIMIT))->get();
        $list->each(function ($item,$key){
            $item->member_sex=$item->member_sex?'男':'女';
            $item->source_channel=$this->getChannel($item->source_channel);
            $item->member_birthday=date('Y-m-d H:i:s',$item->member_birthday);
        });
        return $list;
    }
	/*
	 * 添加会员
	 */
	public function add($date){
        $date['member_time']=time();
        if(!$member=$this->create($date)){
            return false;
        }
        if(isset($date['tag_id'])){//如果有会员标签
            $tag_ids=explode(',',$date['tag_id']);
            if(!is_array($tag_ids)){
                $tag_ids=compact('tag_ids');
            }
            //添加会员与标签的关联
            if(!$member->tags()->sync($tag_ids,false)){
                return false;
            }
        }
        return true;
    }
    /*
     * 编辑会员
     */
    public function edit($date){
        $member=$this->find($date['member_id']);
        if(!$member->update($date)){
            return false;
        }
        if(isset($date['tag_id'])){//如果有会员标签
            $tag_ids=explode(',',$date['tag_id']);
            if(!is_array($tag_ids)){
                $tag_ids=compact('tag_ids');
            }
            //修改会员与标签的关联
            if(!$member->tags()->sync($tag_ids,true)){
                return false;
            }
        }
        return true;
    }
    /*
     * 会员领取卡卷
     */
    public function receive($coupon_info){
        $data=array(
            'coupon_title'=>$coupon_info->coupon_t_title,
            'coupon_desc'=>$coupon_info->coupon_t_desc,
            'coupon_start_date'=>$coupon_info->coupon_t_start_date->timestamp,
            'coupon_end_date'=>$coupon_info->coupon_t_end_date->timestamp,
            'coupon_price'=>$coupon_info->coupon_t_price,
            'coupon_limit'=>$coupon_info->coupon_t_limit,
            'coupon_owner_name'=>$this->member_truename,
            'coupon_code'=>$this->get_coupon_code($this->member_id),
        );
        $this->coupons()->attach($coupon_info->coupon_t_id,$data);
        $this->coupons()->decrement('coupon_t_total',1);
        $this->coupons()->increment('coupon_t_giveout',1);
    }
    /*
     * 获取优惠券编码
     */
    private function get_coupon_code($member_id = 0){
        static $num = 1;
        $sign_arr = array();
        $sign_arr[] = sprintf('%02d',mt_rand(10,99));
        $sign_arr[] = sprintf('%03d', (float) microtime() * 1000);
        $sign_arr[] = sprintf('%010d',time() - 946656000);
        if($member_id){
            $sign_arr[] = sprintf('%03d', (int) $member_id % 1000);
        } else {
            //自增变量
            $tmpnum = 0;
            if ($num > 99){
                $tmpnum = substr($num, -1, 2);
            } else {
                $tmpnum = $num;
            }
            $sign_arr[] = sprintf('%02d',$tmpnum);
            $sign_arr[] = mt_rand(1,9);
        }
        $code = implode('',$sign_arr);
        $num += 1;
        return $code;
    }
}
