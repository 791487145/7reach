<?php
/**
 * Created by PhpStorm.
 * User: 23261
 * Date: 2018/11/13
 * Time: 12:58
 */
namespace App\Modules\Goods\Http\Controllers;
use App\Events\GoodsChange;
use App\Http\Controllers\BaiscController;
use App\Model\Goods;
use App\Model\GoodsGroup;
use App\Model\ShopGood;
use App\Model\StoreGood;
use App\Model\WebShop;
use App\Model\GoodsClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GoodsController extends BaiscController
{


    /*
     * 总商品池
     */
    public function goodsList(Request $request,Goods $goods){
        $goods_list=$goods->getList($request,$this->company_id);
        $data['goods_count']=$goods_list->count();
        $data['goods']=$goods_list;
        return $this->success($data);
    }
    /*
     * 只查询上架的商品
     */
    public function onlineGoods(Request $request){
        $list=Goods::upShelves()->where('company_id',$this->company_id)->forPage($request->input('page',1),$request->input('limit',BaiscController::LIMIT))->get();
        $list->each(function($item,$key){
            $item->goods_state=$item->goods_state?'上架':'下架';
        });
        $data['goods_count']=$list->count();
        $data['online_goods']=$list;
        return $this->success($data);
    }
    /*
     * 旗舰店商品池
     */
    public function shopGoodsList(Request $request,ShopGood $shopGood){
        $goods_list=$shopGood->getList($request,$this->company_id);
        $data['shop_goods_count']=$goods_list->count();
        $data['shop_goods']=$goods_list;
        return $this->success($data);
    }
    /*
     * 云店商品池
     */
    public function storeGoodsList(Request $request,StoreGood $storeGood){
        if(empty($request->input('store_id'))){
            return $this->failed('云店id为空');
        }
        $goods_list=$storeGood->getList($request,$this->company_id);
        $data['store_goods_count']=$goods_list->count();
        $data['store_goods']=$goods_list;
        return $this->success($data);
    }
    /*
     * 商品类目
     */
    public function goodsClass(){
      $tree=GoodsClass::all()->toTree();
      return $this->success($tree);
    }
    /*
     * 添加商品
     */
    public function goodsAdd(Request $request,Goods $goods){
        $message=array(
            'goods_name.required'=>'商品名称不能为空',
            'gc_id.required'=>'商品类目不能为空',
            'goods_price.required'=>'商品价格不能为空',
            'goods_images.required'=>'商品图片不能为空',
            'goods_price.numeric'=>'商品价格必须是数字',

        );
        $validator = Validator::make($request->all(), [
            'goods_name' => 'required',
            'gc_id'=>'required',
            'goods_price'=>'required|numeric',
            'goods_images'=>'required',

        ],$message);
        if ($validator->fails()) {
           return $this->failed($validator->errors()->first());
        }
        $date=$request->all();
        $date['company_id']=$this->company_id;
        $goods_id=$goods->addGoods($date);
        if($goods_id){
            return $this->success(array('goods_id'=>$goods_id));
        }
        return $this->failed('添加失败');

    }
    /*
     * 旗舰店添加商品
     */
    public function addShopGoods(Request $request,ShopGood $shopGood){
        $message=array(
            'goods_id.required'=>'商品id不能为空',
            'goods_id.unique'=>'已添加过此商品',
            'goods_shop_price.required'=>'旗舰店售价不能为空',
            'goods_storage.required'=>'商品库存不能为空',
            'goods_storage.numeric'=>'库存必须为数字',
            'goods_commend.required'=>'商品是否推荐不能为空',

        );
        $validator = Validator::make($request->all(), [
            'goods_id' => ['required',
                Rule::unique('7r_shop_goods')->where(function ($query) {
                    $query->where('company_id', $this->company_id);
                })
                ],
            'goods_shop_price'=>'required',
            'goods_storage'=>'required|numeric',
            'goods_commend'=>'required'

        ],$message);
        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }
        $date=$request->all();
        $date['company_id']=$this->company_id;
        $webshop=WebShop::where('company_id',$this->company_id)->first();
        if(empty($webshop)){
            return $this->failed('旗舰店信息错误');
        }
        $date['shop_id']=$webshop->shop_id;
        $shopGood->add($date);
        return $this->message('添加商品成功');
    }
    /*
     * 云店添加商品
     */
    public function addStoreGoods(Request $request,StoreGood $storeGood){
        $message=array(
            'goods_id.required'=>'商品id不能为空',
            'goods_id.unique'=>'已添加过此商品',
            'goods_store_price.required'=>'云店售价不能为空',
            'goods_storage.required'=>'商品库存不能为空',
            'goods_storage.numeric'=>'库存必须为数字',
            'store_id.required'=>'请指定所属门店',
            'goods_commend.required'=>'商品推荐不能为空',
        );
        $validator = Validator::make($request->all(), [
            'goods_id' => ['required',
                Rule::unique('7r_store_goods')->where(function ($query) use($request){
                    $query->where(['company_id'=>$this->company_id,'store_id'=>$request->input('store_id')]);
                })
            ],
            'goods_store_price'=>'required',
            'goods_storage'=>'required|numeric',
            'store_id'=>'required',
            'goods_commend'=>'required',
        ],$message);
        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }
        $date=$request->all();
        $date['company_id']=$this->company_id;
        $storeGood->add($date);
        return $this->message('添加商品成功');

    }
    /*
     * 编辑商品
     */
    public function editGoods(Request $request,Goods $goods){
        if($request->isMethod("post")){//更新商品
            $message=array(
                'goods_id'=>'商品id不能为空',
                'goods_name.required'=>'商品名称不能为空',
                'gc_id.required'=>'商品类目不能为空',
                'goods_price.required'=>'商品价格不能为空',
                'goods_images.required'=>'商品图片不能为空',
                'goods_price.numeric'=>'商品价格必须是数字',

            );
            $validator = Validator::make($request->all(), [
                'goods_id'=>'required',
                'goods_name' => 'required',
                'gc_id'=>'required',
                'goods_price'=>'required|numeric',
                'goods_images'=>'required'
            ],$message);
            if ($validator->fails()) {
                return $this->failed($validator->errors()->first());
            }
            $date=$request->all();
            $date['company_id']=$this->company_id;
            $goods->edit($date);
            return $this->message('编辑商品成功');

        }
        $goods_id=$request->input('goods_id');
        $goods=$goods->with(['goods_images'])->find($goods_id);
        $goods_group=GoodsGroup::where('company_id',$this->company_id)->get();
        $data=array();
        if($goods){
            $data['goods']=$goods;
            $data['goods_gc_info']=GoodsClass::ancestorsAndSelf($goods->gc_id);
            $goods_class=GoodsClass::all()->toTree();
            $data['goods_groups']=$goods_group;
            $data['goods_all_class']=$goods_class;
            return $this->success($data);
        }
        return $this->failed('无此商品');

    }
    /*
     * 编辑商品详情
     */
    public function editBody(Request $request){
        $goods_id=$request->input('goods_id');
        if(!$goods_id){
            return $this->failed('商品id不能为空');
        }
        $data=$request->only(['goods_body']);
        if(Goods::find($goods_id)->update($data)){
            return $this->message('修改成功');
        }
        return $this->failed('修改失败');
    }
    /*
     * 编辑旗舰店商品
     */
    public function editShopGoods(Request $request,ShopGood $shopGood){
        if($request->isMethod("post")) {//更新商品
            $message=array(
                'shop_goods_id'=>'旗舰店商品id不能为空',
                'goods_shop_price.required'=>'旗舰店价格不能为空',
                'goods_shop_price.numeric'=>'商品价格必须是数字',
                'goods_storage.required'=>'商品库存不能为空',
                'goods_storage.numeric'=>'库存必须为数字',
            );
            $validator = Validator::make($request->all(), [
                'shop_goods_id'=>'required',
                'goods_shop_price'=>'required|numeric',
                'goods_storage'=>'required|numeric',

            ],$message);
            if ($validator->fails()) {
                return $this->failed($validator->errors()->first());
            }
            $date=$request->all();
            $date['company_id']=$this->company_id;
            $shopGood->edit($date);
            return $this->message('修改商品成功');
        }
        $shop_goods_id=$request->input('shop_goods_id');
        $goods=$shopGood->with('goods')->find($shop_goods_id);
        if($goods){
            return $this->success($goods);
        }
        return $this->failed('无此商品');
    }
    /*
     * 编辑云店商品
     */
    public function editStoreGoods(Request $request,StoreGood $storeGood){
        if($request->isMethod('post')){
            $message=array(
                'store_goods_id'=>'云店商品id不能为空',
                'goods_store_price.required'=>'云店价格不能为空',
                'goods_store_price.numeric'=>'商品价格必须是数字',
                'goods_storage.required'=>'商品库存不能为空',
                'goods_storage.numeric'=>'库存必须为数字',
            );
            $validator = Validator::make($request->all(), [
                'store_goods_id'=>'required',
                'goods_store_price'=>'required|numeric',
                'goods_storage'=>'required|numeric',
            ],$message);
            if ($validator->fails()) {
                return $this->failed($validator->errors()->first());
            }
            $date=$request->all();
            $date['company_id']=$this->company_id;
            $storeGood->edit($date);
            return $this->message('修改商品成功');
        }
        $store_goods_id=$request->input('store_goods_id');
        $goods=$storeGood->with('goods')->find($store_goods_id);
        if($goods){
            return $this->success($goods);
        }
        return $this->failed('无此商品');
    }
    /*
     * 商品池上下架
     */
    public function shelves(Request $request,Goods $goods){
        $goods_id=$request->input('goods_id');
        $flag=$request->input('flag',1);
        if(empty($goods_id)){
            return $this->failed('商品id为空');
        }
        if($flag==1){//上架
            if($goods->find($goods_id)->update(['goods_state'=>1])){
                return $this->message('操作成功');
            }
        }else{//下架
            if($goods->find($goods_id)->update(['goods_state'=>0])){
                //同时将旗舰店、云店商品下架
                $goods->find($goods_id)->shopGoods->update(['goods_state'=>0]);
                $goods->find($goods_id)->storeGoods->each(function ($item,$key){
                    $item->update(['goods_state'=>0]);
                });
                //调用事件清空购物车无效数据
                event(new GoodsChange($goods->find($goods_id)));
                return $this->message('操作成功');
            }
        }
        return $this->failed('操作失败');
    }
    /*
     * 批量操作
     */
    public function batch(Request $request,Goods $goods){
        $flag=$request->input('flag');
        $goods_ids=explode(',',$request->input('goods_id'));
        if(is_array($goods_ids)){
            if($flag=='goods_class'){//修改商品分类
                $gc_id=$request->input('gc_id');
                if(empty($gc_id)){
                    return $this->failed('分类id为空');
                }
                $good_list=Goods::whereIn('goods_id',$goods_ids)->select('goods_id','gc_id')->get();
                $good_list->each(function ($item, $key) use($gc_id){
                    $item->gc_id=$gc_id;
                });
                $goods->updateBatch($good_list->toArray());
                return $this->message('修改分类成功');
            }elseif($flag=='delete'){//删除商品
                event(new GoodsChange(null,$goods->find($goods_ids)));
                $goods->destroy($goods_ids);
                return $this->message('删除成功');
            }else{//下架
                $good_list=Goods::whereIn('goods_id',$goods_ids)->select('goods_id','goods_state')->get();
                $good_list->each(function ($item, $key){
                    $item->goods_state=0;
                });
                $goods->updateBatch($good_list->toArray());

                $good_list->each(function($item,$key){
                    //同时将旗舰店、云店商品下架
                    $item->shopGoods->update(['goods_state'=>0]);
                    $item->storeGoods->each(function ($item,$key){
                        $item->update(['goods_state'=>0]);
                    });
                });
                //调用事件清空购物车无效数据
                event(new GoodsChange(null,$good_list));
                return $this->message('下架成功');
            }

        }
        return $this->failed('商品id为空');
    }
    /*
     * 旗舰店批量操作
     */
    public function shopBatch(Request $request,ShopGood $shopGood){
        $flag = $request->input('flag');
        $shop_goods_ids=explode(',',$request->input('shop_goods_id'));
        if(is_array($shop_goods_ids)){
            if($flag=='delete'){//删除商品
                $goods=Goods::whereHas('shopGoods',function ($query) use($shop_goods_ids){
                    $query->whereIn('shop_goods_id',$shop_goods_ids);
                })->get();
                event(new GoodsChange(null,$goods));
                $shopGood->destroy($shop_goods_ids);
                return $this->message('删除成功');
            }
        }
        return $this->failed('商品id不能为空');
    }
    /*
     * 云店批量操作
     */
    public function storeBatch(Request $request,StoreGood $storeGood){
        $flag=$request->input('flag');
        $store_goods_ids=explode(',',$request->input('store_goods_id'));
        if(is_array($store_goods_ids)){
            if($flag=='delete'){//删除商品
                $storeGood->destroy($store_goods_ids);
                return $this->message('删除成功');
            }
        }
        return $this->failed('商品id不能为空');
    }
}