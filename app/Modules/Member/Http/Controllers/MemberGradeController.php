<?php
/**
 * Created by PhpStorm.
 * User: 23261
 * Date: 2018/11/19
 * Time: 9:58
 */
namespace App\Modules\Member\Http\Controllers;
use App\Http\Controllers\BaiscController;
use App\Model\MemberGrade;
use App\Model\MemberTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MemberGradeController extends BaiscController{
    /*
     * 会员等级列表
     */
    public function list(){
        $grade_list=MemberGrade::where('company_id',$this->company_id)->get();
        $grade_list->each(function ($item,$key){
            $item->grade_rights=unserialize($item->grade_rights);
        });
        return $this->success($grade_list);
    }
    /*
     * 查询启用的会员等级
     */
    public function enableGrade(){
        $grade_list=MemberGrade::Enable()->where('company_id',$this->company_id)->get();
        $grade_list->each(function ($item,$key){
            $item->grade_rights=unserialize($item->grade_rights);
        });
        return $this->success($grade_list);
    }
    /*
     * 添加会员等级
     */
    public function add(Request $request){
        $message=array(
            'grade_name.required'=>'等级名称不能为空',
            'grade_name.unique'=>'此等级已存在',
            'grade_level.required'=>'等级标识不能为空',
            'grade_level.numeric'=>'等级标识必须为数字',
            'grade_exppoints.required'=>'所需经验值不能为空',
            'grade_exppoints.numeric'=>'经验值必须是数字',
            'is_enable.required'=>'是否激活不能为空'
        );
        $validator=Validator::make($request->all(),[
            'grade_name'=>['required',
                Rule::unique('7r_member_grade')->where(function($query){
                    $query->where('company_id',$this->company_id);
                }),
                ],
            'grade_level'=>['required','numeric',function($attribute, $value, $fail){
                $split_value="VIP".$value;
                $count=MemberGrade::where(['company_id'=>$this->company_id,'grade_level'=>$split_value])->count();
                if($count){
                    $fail('此等级已经存在');
                }
            }],
            'grade_exppoints'=>'required|numeric',
            'is_enable'=>'required',
        ],$message);
        if($validator->fails()){
            return $this->failed($validator->errors()->first());
        }
        //序列化等级权益
        $grade_rights=array(
            'is_freeShipping'=>0,
            'discount'=>0,
            'points'=>0,
            'coupon_id'=>0
        );
        $date=$request->all();
        $date['company_id']=$this->company_id;
        $date['grade_rights']=serialize($grade_rights);
        $date['grade_level']='VIP'.$date['grade_level'];
        if(MemberGrade::create($date)){
            return $this->message('会员等级添加成功');
        }
        return $this->failed('会员等级添加失败');

    }
    /*
     * 编辑会员等级
     */
    public function edit(Request $request){
        if($request->isMethod('post')){
            $message=array(
                'grade_id.required'=>'等级id不能为空',
                'grade_name.required'=>'等级名称不能为空',
                'grade_level.required'=>'等级标识不能为空',
                'grade_level.numeric'=>'等级标识必须为数字',
                'grade_exppoints.required'=>'所需经验值不能为空',
                'grade_exppoints.numeric'=>'经验值必须是数字',
                'is_enable.required'=>'是否激活不能为空'
            );
            $validator=Validator::make($request->all(),[
                'grade_id'=>'required',
                'grade_name'=>'required',
                'grade_level'=>'required|numeric',
                'grade_exppoints'=>'required|numeric',
                'is_enable'=>'required',
            ],$message);
            if($validator->fails()){
                return $this->failed($validator->errors()->first());
            }
            //序列化等级权益
            $grade_rights=array(
                'is_freeShipping'=>0,
                'discount'=>0,
                'points'=>0,
                'coupon_id'=>0
            );
            $date=$request->all();
            $date['company_id']=$this->company_id;
            $date['grade_rights']=serialize($grade_rights);
            $date['grade_level']='VIP'.$date['grade_level'];
            if(MemberGrade::find($date['grade_id'])->update($date)){
                return $this->message('会员等级修改成功');
            }
            return $this->failed('会员等级修改失败');
        }
        $grade_id=$request->input('grade_id');
        if($grade_id){
            $grade_info=MemberGrade::find($grade_id);
            if($grade_info){
                $grade_info->grade_rights=unserialize($grade_info->grade_rights);
                $grade_info->grade_level=intval(substr($grade_info->grade_level,'3'));
                return $this->success($grade_info);
            }
            return $this->failed('无此等级');
        }
        return $this->failed('等级id不能为空');
    }
    /*
     * 删除会员等级
     */
    public function delete(Request $request){
        $grade_id=$request->input('grade_id');
        if($grade_id){
            if(MemberGrade::destroy($grade_id)){
                return $this->message('会员等级删除成功');
            }
            return $this->failed('会员等级删除失败');
        }
        return $this->failed('等级id不能为空');
    }
}