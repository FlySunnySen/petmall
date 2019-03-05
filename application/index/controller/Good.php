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
		$goods_images_list = model('GoodImages')->where("good_id", $goods_id)->column("image_url"); // 商品 图册
		$this->assign("good_images_list", $goods_images_list);
		// var_dump($goods_images_list);die;
		$this->assign('navigate_goods', navigate_goods($goods_id, 1)); // 面包屑导航
		// var_dump($goods);die;
		$this->assign("goods", $goods);
		return $this->fetch();
	}
}