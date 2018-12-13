<?php
/**
 * Created by PhpStorm.
 * User: 23261
 * Date: 2018/11/19
 * Time: 17:58
 */
namespace APP\Modules\Member\Http\Controllers;
use App\Http\Controllers\BaiscController;
use App\Model\MemberCenterDecoration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoyaltyController extends BaiscController{
    /*
     * 会员中心装修设置
     */
    public function decoration(Request $request){
        if($request->isMethod('post')){
            $message=array(
                'mc_dec_id.required'=>'主键id不能为空',
                'level_enable.required'=>'会员等级开关不能为空',
                'qr_code_enable.required'=>'二维码开关不能为空',
                'rights_enable.required'=>'权益开关不能为空',
                'page_style.required'=>'页面风格开关不能为空',

            );
            $validator=Validator::make($request->all(),[
                'mc_dec_id'=>'required',
                'level_enable'=>'required',
                'qr_code_enable'=>'required',
                'rights_enable'=>'required',
                'page_style'=>'required'
            ],$message);
            if($validator->fails()){
                return $this->failed($validator->errors()->first());
            }
            $date=$request->all();
            //处理模块功能序列化信息
            $moudle_enable=array('points'=>0, 'predeposit'=>0, 'coupon'=>0);
            if(isset($date['moudle_enable'])){
                $moudle_enable_request=explode(',',$date['moudle_enable']);
                //dd($moudle_enable);
                foreach ($moudle_enable_request as $value){
                    $moudle_enable[$value]=$value?1:0;
                }
            }
            $moudle_enable=serialize($moudle_enable);

            //处理组件序列化信息
            $element_setting=array('order'=>0,'like'=>0,'message'=>0,'info'=>0,'store'=>0);
            if(isset($date['element_setting'])){
                $element_setting_request=explode(',',$date['element_setting']);
                foreach ($element_setting_request as $value){
                    $element_setting[$value]=$value?1:0;
                }
            }
            $element_setting=serialize($element_setting);
            $date['moudle_enable']=$moudle_enable;
            $date['element_setting']=$element_setting;
            if(MemberCenterDecoration::where('mc_dec_id',$date['mc_dec_id'])->update($date)){
                return $this->message('设置成功');
            }
            return $this->failed('设置失败');
        }
        $decoration_info=MemberCenterDecoration::where('company_id',$this->company_id)->first();
        if($decoration_info){
            $decoration_info->moudle_enable=unserialize($decoration_info->moudle_enable);
            $decoration_info->element_setting=unserialize($decoration_info->element_setting);
        }
        return $this->success($decoration_info);
    }
}