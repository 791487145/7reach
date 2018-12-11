<?php
/**
 * Created by PhpStorm.
 * User: 23261
 * Date: 2018/11/21
 * Time: 11:46
 */
namespace App\Modules\Member\Http\Controllers;
use App\Http\Controllers\BaiscController;
use App\Model\MemberTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class MemberTagController extends BaiscController{
    /*
     * 会员标签列表
     */
    public function list(Request $request){
        $page=$request->input('page',1);
        $limit=$request->input('limit');
        if(empty($limit)){
            $list=MemberTag::where('company_id',$this->company_id)->get();
        }else{
            $list=MemberTag::where('company_id',$this->company_id)->
            forPage($page,$limit)->get();

        }
        $data['tag_count']=$list->count();
        $data['memberTags']=$list;
        return $this->success($data);
    }
    /*
     * 添加会员标签
     */
    public function add(Request $request){
        $message=array(
            'mtag_name.required'=>'标签名称不能为空',
            'mtag_name.unique'=>'此标签已经存在',
        );
        $validator=Validator::make($request->all(),[
            'mtag_name'=>['required',
                Rule::unique('7r_member_tag')->where(function($query){
                    $query->where('company_id',$this->company_id);
                })]
        ],$message);
        if($validator->fails()){
            return $this->failed($validator->errors()->first());
        }
        $date=$request->all();
        $date['company_id']=$this->company_id;
        if(MemberTag::create($date)){
            return $this->message('添加标签成功');
        }
        return $this->failed('添加标签失败');

    }
    /*
     * 编辑会员标签
     */
    public function edit(Request $request){
        if($request->isMethod('post')){
            $message=array(
                'tag_id.required'=>'标签id不能为空',
                'mtag_name.required'=>'标签名称不能为空',
            );
            $validator=Validator::make($request->all(),[
                'tag_id'=>'required',
                'mtag_name'=>'required',
            ],$message);
            if($validator->fails()){
                return $this->failed($validator->errors()->first());
            }
            $date=$request->all();
            if(MemberTag::find($date['tag_id'])->update($date)){
                return $this->message('编辑标签成功');
            }
            return $this->failed('编辑标签失败');
        }
        $tag_id=$request->input('tag_id');
        if($tag_id){
            $tag_info=MemberTag::find($tag_id);
            if(empty($tag_info)){
                return $this->failed('无此标签');
            }
            return $this->success($tag_info);
        }
        return $this->failed('标签id不能为空');
    }
    /*
     * 删除会员标签
     */
    public function delete(Request $request){
        $tag_id=$request->input('tag_id');
        if($tag_id){
            MemberTag::destroy($tag_id);
            return $this->message('标签删除成功');
        }
        return $this->failed('标签id不能为空');
    }
}