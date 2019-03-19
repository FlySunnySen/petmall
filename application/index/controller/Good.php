<?php
namespace app\index\controller;
use app\common\logic\GoodsLogic;
use think\Controller;
use think\Db;
use think\Page;

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

	/**
	 * 商品列表页
	 */
	public function goodsList() {

		// $key = md5($_SERVER['REQUEST_URI'] . input('start_price') . '_' . input('end_price'));
		// $html = S($key);
		if (!empty($html)) {
			return $html;
		}

		$filter_param = array(); // 帅选数组
		$id = input('id', 1); // 当前分类id
		$brand_id = input('get.brand_id', 0);
		$spec = input('get.spec', 0); // 规格
		$attr = input('get.attr', ''); // 属性
		$sort = input('get.sort', 'sort'); // 排序
		$sort_asc = input('get.sort_asc', 'desc'); // 排序
		$price = input('get.price', ''); // 价钱
		$start_price = trim(input('post.start_price', '0')); // 输入框价钱
		$end_price = trim(input('post.end_price', '0')); // 输入框价钱
		if ($start_price && $end_price) {
			$price = $start_price . '-' . $end_price;
		}
		// 如果输入框有价钱 则使用输入框的价钱

		$filter_param['id'] = $id; //加入帅选条件中
		$brand_id && ($filter_param['brand_id'] = $brand_id); //加入帅选条件中
		$spec && ($filter_param['spec'] = $spec); //加入帅选条件中
		$attr && ($filter_param['attr'] = $attr); //加入帅选条件中
		$price && ($filter_param['price'] = $price); //加入帅选条件中

		$goodsLogic = new GoodsLogic(); // 前台商品操作逻辑类

		// 分类菜单显示
		$goodsCate = Db::name("good_type")->where("id", $id)->find(); // 当前分类
		// $cateArr = $goodsLogic->get_goods_cate($goodsCate);

		// 帅选 品牌 规格 属性 价格
		$cat_id_arr = self::getCatGrandson($id);
		$goods_where = ['is_on_sale' => 1, 'type_id' => ['in', $cat_id_arr]];
		$filter_goods_id = Db::name('good')->where($goods_where)->column("id", true);

		// 过滤帅选的结果集里面找商品
		if ($brand_id || $price) // 品牌或者价格
		{
			$goods_id_1 = $goodsLogic->getGoodsIdByBrandPrice($brand_id, $price); // 根据 品牌 或者 价格范围 查找所有商品id
			$filter_goods_id = array_intersect($filter_goods_id, $goods_id_1); // 获取多个帅选条件的结果 的交集
		}
		if ($spec) // 规格
		{
			$goods_id_2 = $goodsLogic->getGoodsIdBySpec($spec); // 根据 规格 查找当所有商品id
			$filter_goods_id = array_intersect($filter_goods_id, $goods_id_2); // 获取多个帅选条件的结果 的交集
		}
		if ($attr) // 属性
		{
			$goods_id_3 = $goodsLogic->getGoodsIdByAttr($attr); // 根据 规格 查找当所有商品id
			$filter_goods_id = array_intersect($filter_goods_id, $goods_id_3); // 获取多个帅选条件的结果 的交集
		}

		$filter_menu = $goodsLogic->get_filter_menu($filter_param, 'goodsList'); // 获取显示的帅选菜单
		var_dump($filter_param);die;
		$filter_price = $goodsLogic->get_filter_price($filter_goods_id, $filter_param, 'goodsList'); // 帅选的价格期间

		$filter_brand = $goodsLogic->get_filter_brand($filter_goods_id, $filter_param, 'goodsList'); // 获取指定分类下的帅选品牌

		$filter_spec = $goodsLogic->get_filter_spec($filter_goods_id, $filter_param, 'goodsList', 1); // 获取指定分类下的帅选规格
		$filter_attr = $goodsLogic->get_filter_attr($filter_goods_id, $filter_param, 'goodsList', 1); // 获取指定分类下的帅选属性

		$count = count($filter_goods_id);
		$page = new Page($count, 20);
		if ($count > 0) {
			$goods_list = model('good')->where("id", "in", implode(',', $filter_goods_id))->limit($page->firstRow . ',' . $page->listRows)->select();
			$filter_goods_id2 = get_arr_column($goods_list, 'id');
			if ($filter_goods_id2) {
				$goods_images = Db::name('good_images')->where("good_id", "in", implode(',', $filter_goods_id2))->select();
			}

		}
		// print_r($filter_menu);
		$goods_category = Db::name("good_type")->where('is_show=1')->column('id,type_name,pid'); // 键值分类数组

		$navigate_cat = navigate_goods($id); // 面包屑导航
		$this->assign('goods_list', $goods_list);
		// var_dump($goods_list);die;
		$this->assign('navigate_cat', $navigate_cat);
		$this->assign('goods_category', $goods_category);
		$this->assign('goods_images', $goods_images); // 相册图片
		// $this->assign('filter_menu', $filter_menu); // 帅选菜单
		// $this->assign('filter_spec', $filter_spec); // 帅选规格
		// $this->assign('filter_attr', $filter_attr); // 帅选属性
		// $this->assign('filter_brand', $filter_brand); // 列表页帅选属性 - 商品品牌
		// $this->assign('filter_price', $filter_price); // 帅选的价格期间
		$this->assign('goodsCate', $goodsCate);
		// $this->assign('cateArr', $cateArr);
		$this->assign('filter_param', $filter_param); // 帅选条件
		$this->assign('cat_id', $id);
		$this->assign('page', $page); // 赋值分页输出
		$html = $this->fetch();
		// S($key, $html);
		return $html;
	}

	public function getCatGrandson($cat_id) {
		$GLOBALS['catGrandson'] = array();
		$GLOBALS['category_id_arr'] = array();
		// 先把自己的id 保存起来
		$GLOBALS['catGrandson'][] = $cat_id;
		// 把整张表找出来
		$GLOBALS['category_id_arr'] = Db::name("good_type")->column('id,pid');
		// 先把所有儿子找出来
		$son_id_arr = model('good_type')->where("pid", $cat_id)->column('id', true);
		foreach ($son_id_arr as $k => $v) {
			getCatGrandson2($v);
		}
		return $GLOBALS['catGrandson'];
	}

}