<?php
namespace app\index\controller;
use app\common\logic\GoodsLogic;
use think\Controller;

// use app\admin\model\GoodType as category;

class Good extends Base {

	/**
	 * 商品详情页
	 */
	public function goodsInfo() {
		//C('TOKEN_ON',true);
		$goodsLogic = new GoodsLogic();
		$goods_id = input("id");
		$Goods = new \app\common\model\Good();
		$goods = $Goods::get($goods_id);
		// var_dump($goods);die;
		if (empty($goods) || ($goods['is_on_sale'] == 0)) {
			$this->error('该商品已经下架', url('Index/index'));
		}
		$spec_goods_price = model('spec_goods_price')->where("goods_id", $goods_id)->column("key,item_id,price,store_count,market_price"); // 规格 对应 价格 库存表
		$goods_images_list = model('GoodImages')->where("good_id", $goods_id)->column("image_url"); // 商品 图册
		$filter_spec = $goodsLogic->get_spec($goods_id);
		$this->assign('filter_spec', $filter_spec); //规格参数
		$this->assign('spec_goods_price', json_encode($spec_goods_price, true)); // 规格 对应 价格 库存表
		$this->assign("good_images_list", $goods_images_list);
		// var_dump($goods_images_list);die;
		$this->assign('navigate_goods', navigate_goods($goods_id, 1)); // 面包屑导航
		// var_dump($goods);die;
		$this->assign("goods", $goods);
		return $this->fetch();
	}

	public function activity() {
		$goods_id = input('goods_id/d'); //商品id
		$item_id = input('item_id/d'); //规格id
		$goods_num = input('goods_num/d'); //欲购买的商品数量
		$Goods = new \app\common\model\Good();
		$goods = $Goods::get($goods_id);
		$this->ajaxReturn(['status' => 1, 'msg' => '该商品没有参与活动', 'result' => ['goods' => $goods]]);
		die;
		$goodsPromFactory = new GoodsPromFactory();
		if ($goodsPromFactory->checkPromType($goods['prom_type'])) {
			//这里会自动更新商品活动状态，所以商品需要重新查询
			if ($item_id) {
				$specGoodsPrice = SpecGoodsPrice::get($item_id);
				$goodsPromLogic = $goodsPromFactory->makeModule($goods, $specGoodsPrice);
			} else {
				$goodsPromLogic = $goodsPromFactory->makeModule($goods, null);
			}
			if ($goodsPromLogic->checkActivityIsAble()) {
				$goods = $goodsPromLogic->getActivityGoodsInfo();
				$goods['activity_is_on'] = 1;
				$this->ajaxReturn(['status' => 1, 'msg' => '该商品参与活动', 'result' => ['goods' => $goods]]);
			} else {
				if (!empty($goods['price_ladder'])) {
					$goodsLogic = new GoodsLogic();
					$goods->shop_price = $goodsLogic->getGoodsPriceByLadder($goods_num, $goods['shop_price'], $goods['price_ladder']);
				}
				$goods['activity_is_on'] = 0;
				$this->ajaxReturn(['status' => 1, 'msg' => '该商品没有参与活动', 'result' => ['goods' => $goods]]);
			}
		}
		if (!empty($goods['price_ladder'])) {
			$goodsLogic = new GoodsLogic();
			$goods->shop_price = $goodsLogic->getGoodsPriceByLadder($goods_num, $goods['shop_price'], $goods['price_ladder']);
		}
		$this->ajaxReturn(['status' => 1, 'msg' => '该商品没有参与活动', 'result' => ['goods' => $goods]]);
	}

}