<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Index extends Common {

	public function index() {
		$hot_goods = $hot_cate = $cateList = $recommend_goods = array();
		$sql = "select a.goods_name,a.id,a.goods_price,a.goods_img,a.type_id,b.parent_id_path,b.type_name from " . config('database.prefix') . "good as a left join ";
		$sql .= config('database.prefix') . "good_type as b on a.type_id=b.id where  a.is_on_sale=1 order by a.good_click"; //二级分类下热卖商品
		$index_hot_goods = cache('index_hot_goods');
		if (empty($index_hot_goods)) {
			$index_hot_goods = Db::query($sql); //首页热卖商品
			// var_dump($index_hot_goods);die;

		}
		if ($index_hot_goods) {
			foreach ($index_hot_goods as $val) {
				$cat_path = explode('_', $val['parent_id_path']);
				$hot_goods[$cat_path[1]][] = $val;
			}
			// var_dump($hot_goods);die;
		}
		$recommend_goods = $hot_goods; //首页推荐商品
		$hot_category = Db::name('good_type')->where("is_show=1")->select(); //热门三级分类
		foreach ($hot_category as $v) {
			$cat_path = explode('_', $v['parent_id_path']);
			$hot_cate[$cat_path[1]][] = $v;
		}
		$data = self::always_category();
		foreach ($data as $k => $v) {
			$n = $k;
			$k = $v['id'];
			$data[$k] = $v;
			unset($data[$n]);
		}
		foreach ($data as $k => $v) {
			if ($v['is_show'] == 1) {
				$n = $v['id'];
				$v['hot_goods'] = empty($hot_goods[$k]) ? '' : $hot_goods[$n];
				$v['recommend_goods'] = empty($recommend_goods[$k]) ? '' : $recommend_goods[$k];
				$v['hot_cate'] = empty($hot_cate[$k]) ? array() : $hot_cate[$k];
				$cateList[] = $goods_category_tree[] = $v;
			} else {
				$goods_category_tree[] = $v;
			}
		}
		// var_dump($cateList);die;
		$this->assign('cateList', $cateList);
		$this->assign('goods_category_tree', $data);
		return $this->fetch();
	}

	//无限级分类
	public function always_category($path = 0, $level = 1) {
		$data = Db::name('good_type')->where(['pid' => $path])->where(['is_show' => 1])->select();
		// var_dump($data);die;
		foreach ($data as $key => $val) {
			$data[$key]['level'] = $level;

			$son = $this->always_category($val['id'], $level + 1);

			if (!empty($son)) {

				$data[$key]['tmenu'] = $son;
			}
		}
		return $data;
	}
}
