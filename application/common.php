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
	$categoryList = model('GoodType')->column("id,type_name,pid");
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
