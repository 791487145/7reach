<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2018/12/3
 * Time: 9:33 AM
 */
namespace  App\Modules\Shop\Http\Controllers\Member;
use App\Http\Controllers\BaiscController;
use App\Http\Controllers\ShopBascController;
use App\Model\CouponTemplate;
use App\Model\MemberCenterDecoration;
use App\Model\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MemberController extends ShopBascController{
    /*
     * 会员中心
     */
    public function home(Request $request){
        $memberCenterDecoration=MemberCenterDecoration::where('company_id',$this->company_id)->first();
        //根据中心装修获取对应的设置信息
        $data=$memberCenterDecoration->checkAvailable($this->member);
        return $this->success($data);
    }
    /*
     * 我的订单
     */
    public function myOrder(Request $request,Order $order){
        $where=array(
            'page'=>$request->input('page',1),
            'limit'=>$request->input('limit',BaiscController::LIMIT),
            'buyer_id'=>$this->member->member_id,
            'order_state'=>$request->input('order_state'),
        );
        $order_list=Order::order($order,$where,$this->company_id);
        $data['order_count']=$order_list->count();
        $data['order_list']=$order_list;
        return $this->success($data);

    }
    /*
     * 我的喜欢
     */
    public function favorites(Request $request){

    }
    /*
     * 我的消息
     */
    public function myMessage(Request $request){

    }
    /*
     * 我的资料
     */
    public function info(Request $request){
        if($request->isMethod('post')){
            $message=array(
                'member_truename.required'=>'会员姓名不能为空',
                'member_sex.required'=>'会员性别不能为空',
                'member_birthday.required'=>'会员生日不能为空',
            );
            $validator=Validator::make($request->all(),[
                'member_truename'=>'required',
                'member_sex'=>'required',
                'member_birthday'=>'required'
            ],$message);
            if($validator->fails()){
                return $this->failed($validator->errors()->first());
            }
            $data=$request->all();
            if($this->member->update($data)){
                return $this->success('修改会员资料成功');
            }
            return $this->failed('修改会员资料失败');
        }

        if(empty($this->member)){
            return $this->failed('无此用户');
        }
        return $this->success($this->member);
    }
    /*
     * 会员领劵
     */
    public function getCoupon(Request $request){
        $coupon_t_id=$request->input('coupon_t_id');
        if(!$coupon_t_id){
            return $this->failed('优惠券id不能为空');
        }
        DB::beginTransaction();
        try{
            if(!$coupon_info=CouponTemplate::find($coupon_t_id)){
                throw new \Exception('无此优惠券');
            }
            //检查优惠卷是或否可用
            $coupon_info->checkAvailable($this->member);
            $this->member->receive($coupon_info);
            DB::commit();
            return $this->message('会员领劵成功');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->failed($e->getMessage());
        }

    }
}