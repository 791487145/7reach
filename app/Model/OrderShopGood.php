<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 14 Nov 2018 01:51:17 +0000.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * App\Model\OrderShopGood
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood query()
 * @mixin \Eloquent
 * @property int $rec_id 订单商品表索引id
 * @property int $order_id 订单id
 * @property int $shop_goods_id 旗舰店商品id
 * @property string $goods_name 商品名称
 * @property float $goods_price 商品价格
 * @property int $goods_num 商品数量
 * @property string|null $goods_image 商品图片
 * @property float $goods_pay_price 商品实际成交价
 * @property int $shop_id 旗舰店ID
 * @property int $buyer_id 买家ID
 * @property string $goods_type 1默认2团购商品3限时折扣商品4组合套装5赠品
 * @property int $promotions_id 促销活动ID（团购ID/限时折扣ID/优惠套装ID）与goods_type搭配使用
 * @property int $gc_id 商品分类ID
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereGcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereGoodsImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereGoodsNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereGoodsPayPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereGoodsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood wherePromotionsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereRecId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereShopGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\OrderShopGood whereShopId($value)
 */
class OrderShopGood extends Eloquent
{
    protected $table = '7r_order_shop_goods';
	protected $primaryKey = 'rec_id';
	public $timestamps = false;

	protected $casts = [
		'order_id' => 'int',
		'shop_goods_id' => 'int',
		'goods_price' => 'float',
		'goods_num' => 'int',
		'goods_pay_price' => 'float(10,2)',
		'shop_id' => 'int',
		'buyer_id' => 'int',
		'promotions_id' => 'int',
		'gc_id' => 'int'
	];

	protected $fillable = [
		'order_id',
		'shop_goods_id',
		'goods_name',
		'goods_price',
		'goods_num',
		'goods_image',
		'goods_pay_price',
		'shop_id',
		'buyer_id',
		'goods_type',
		'promotions_id',
		'gc_id'
	];
}
