<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件

use think\db;
/**
 * 面包屑导航  用于前台商品
 * @param $id |商品id  或者是 商品分类id
 * @param int $type|默认0是传递商品分类id  id 也可以传递 商品id type则为1
 * @return array
 */
function navigate_goods($id, $type = 0) {
	$cat_id = $id; //
	// 如果传递过来的是
	if ($type == 1) {
		$cat_id = model('good')->where("id", $id)->value('type_id');
	}
	$categoryList = Db::name('good_type')->column("id,type_name,pid");
	// var_dump($cat_id);
	// var_dump($categoryList);die;
	// 第一个先装起来
	$arr[$cat_id] = $categoryList[$cat_id]['type_name'];
	foreach ($categoryList as $category) {
		$cat_id = $categoryList[$cat_id]['pid'];
		if ($cat_id > 0 && array_key_exists($cat_id, $categoryList)) {
			$arr[$cat_id] = $categoryList[$cat_id]['type_name'];
		} else {
			break;
		}
	}
	$arr = array_reverse($arr, true);
	return $arr;
}
/**
 * 将model查询出来的对象转换为数组
 * @param array $array
 * @return array
 */
function Modeltoarray($array) {
	if (empty($array) || !count($array)) {
		return false;
	}
	foreach ($array as $value) {
		$datarray[] = $value->toArray();
	}
	return $datarray;
}

/**
 * 多个数组的笛卡尔积
 *
 * @param unknown_type $data
 */
function combineDika() {
	$data = func_get_args();
	$data = current($data);
	$cnt = count($data);
	$result = array();
	$arr1 = array_shift($data);
	foreach ($arr1 as $key => $item) {
		$result[] = array($item);
	}

	foreach ($data as $key => $item) {
		$result = combineArray($result, $item);
	}
	return $result;
}

/**
 * 两个数组的笛卡尔积
 * @param unknown_type $arr1
 * @param unknown_type $arr2
 */
function combineArray($arr1, $arr2) {
	$result = array();
	foreach ($arr1 as $item1) {
		foreach ($arr2 as $item2) {
			$temp = $item1;
			$temp[] = $item2;
			$result[] = $temp;
		}
	}
	return $result;
}

/**
 * 刷新商品库存, 如果商品有设置规格库存, 则商品总库存 等于 所有规格库存相加
 * @param type $goods_id  商品id
 */
function refresh_stock($goods_id) {
	$count = model("SpecGoodsPrice")->where("goods_id", $goods_id)->count();
	if ($count == 0) {
		return false;
	}
	// 没有使用规格方式 没必要更改总库存

	$store_count = model("SpecGoodsPrice")->where("goods_id", $goods_id)->sum('store_count');
	Db::name("Good")->where("id", $goods_id)->update(array('goods_number' => $store_count)); // 更新商品的总库存
}

function ajaxReturn($data) {
	exit(json_encode($data, JSON_UNESCAPED_UNICODE));
}

/**
 * 获取数组中的某一列
 * @param array $arr 数组
 * @param string $key_name  列名
 * @return array  返回那一列的数组
 */
function get_arr_column($arr, $key_name) {
	$arr2 = array();
	foreach ($arr as $key => $val) {
		$arr2[] = $val[$key_name];
	}
	return $arr2;
}

/**
 * 检查手机号码格式
 * @param $mobile 手机号码
 */
function check_mobile($mobile) {
	if (preg_match('/1[34578]\d{9}$/', $mobile)) {
		return true;
	}

	return false;
}

/**
 * 检查固定电话
 * @param $mobile
 * @return bool
 */
function check_telephone($mobile) {
	if (preg_match('/^([0-9]{3,4}-)?[0-9]{7,8}$/', $mobile)) {
		return true;
	}

	return false;
}