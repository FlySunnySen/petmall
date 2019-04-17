<?php

namespace app\index\controller;

use app\common\model\Shop;
use think\Db;
use think\Session;

class Api extends Base {
	public $send_scene;

	public function _initialize() {
		parent::_initialize();
		session('user');
	}

	/*
		     * 获取地区
	*/
	public function getRegion() {
		$parent_id = input('parent_id');
		$selected = input('selected');
		$data = Db::name('region')->where("parent_id", $parent_id)->select();
		$html = '';
		if ($data) {
			foreach ($data as $h) {
				if ($h['id'] == $selected) {
					$html .= "<option value='{$h['id']}' selected>{$h['name']}</option>";
				}
				$html .= "<option value='{$h['id']}'>{$h['name']}</option>";
			}
		}
		echo $html;
	}

	public function getTwon() {
		$parent_id = input('parent_id');
		$data = Db::name('region')->where("parent_id", $parent_id)->select();
		$html = '';
		if ($data) {
			foreach ($data as $h) {
				$html .= "<option value='{$h['id']}'>{$h['name']}</option>";
			}
		}
		if (empty($html)) {
			echo '0';
		} else {
			echo $html;
		}
	}

	/**
	 * 获取省
	 */
	public function getProvince() {
		$province = Db::name('region')->field('id,name')->where(array('level' => 1))->cache(true)->select();
		$res = array('status' => 1, 'msg' => '获取成功', 'result' => $province);
		exit(json_encode($res));
	}

	public function area() {
		$province_id = input('province_id/d');
		$city_id = input('city_id/d');
		$district_id = input('district_id/d');
		$province_list = Db::name('region')->field('id,name')->where('level', 1)->cache(true)->select();
		$city_list = Db::name('region')->field('id,name')->where('parent_id', $province_id)->cache(true)->select();
		$district_list = Db::name('region')->field('id,name')->where('parent_id', $city_id)->cache(true)->select();
		$town_list = Db::name('region')->field('id,name')->where('parent_id', $district_id)->cache(true)->select();
		$this->ajaxReturn(['status' => 1, 'msg' => '获取成功',
			'result' => ['province_list' => $province_list, 'city_list' => $city_list, 'district_list' => $district_list, 'town_list' => $town_list]]);
	}

	/**
	 * 获取市或者区
	 */
	public function getRegionByParentId() {
		$parent_id = input('parent_id');
		$res = array('status' => 0, 'msg' => '获取失败，参数错误', 'result' => '');
		if ($parent_id) {
			$region_list = Db::name('region')->field('id,name')->where(['parent_id' => $parent_id])->select();
			$res = array('status' => 1, 'msg' => '获取成功', 'result' => $region_list);
		}
		exit(json_encode($res));
	}

	/*
		     * 获取下级分类
	*/
	public function get_category() {
		$parent_id = input('get.parent_id/d'); // 商品分类 父id
		$list = Db::name('goods_category')->where("parent_id", $parent_id)->select();
		if ($list) {
			$this->ajaxReturn(['status' => 1, 'msg' => '获取成功！', 'result' => $list]);
		}
		$this->ajaxReturn(['status' => -1, 'msg' => '获取失败！', 'result' => []]);
	}

	public function shop() {
		$province_id = input('province_id/d', 0);
		$city_id = input('city_id/d', 0);
		$district_id = input('district_id/d', 0);
		$shop_address = input('shop_address/s', '');
		$longitude = input('longitude/s', 0);
		$latitude = input('latitude/s', 0);
		if (empty($province_id) && empty($province_id) && empty($district_id)) {
			$this->ajaxReturn([]);
		}
		$where = ['deleted' => 0, 'shop_status' => 1, 'province_id' => $province_id, 'city_id' => $city_id, 'district_id' => $district_id];
		$field = '*';
		$order = 'shop_id desc';
		if ($longitude) {
			$field .= ',round(SQRT((POW(((' . $longitude . ' - longitude)* 111),2))+  (POW(((' . $latitude . ' - latitude)* 111),2))),2) AS distance';
			$order = 'distance ASC';
		}
		if ($shop_address) {
			$where['shop_name|shop_address'] = ['like', '%' . $shop_address . '%'];
		}
		$Shop = new Shop();
		$shop_list = $Shop->field($field)->where($where)->order($order)->select();
		$origins = $destinations = [];
		if ($shop_list) {
			$shop_list = collection($shop_list)->append(['phone', 'area_list', 'work_time', 'work_day'])->toArray();
			$shop_list_length = count($shop_list);
			for ($shop_cursor = 0; $shop_cursor < $shop_list_length; $shop_cursor++) {
				$origin = $latitude . ',' . $longitude;
				array_push($origins, $origin);
				$destination = $shop_list[$shop_cursor]['latitude'] . ',' . $shop_list[$shop_cursor]['longitude'];
				array_push($destinations, $destination);
			}
			$url = 'http://api.map.baidu.com/routematrix/v2/driving?output=json&origins=' . implode('|', $origins) . '&destinations=' . implode('|', $destinations) . '&ak=Sgg73Hgc2HizzMiL74TUj42o0j3vM5AL';
			$result = httpRequest($url, "get");
			$data = json_decode($result, true);
			if (!empty($data['result'])) {
				for ($shop_cursor = 0; $shop_cursor < $shop_list_length; $shop_cursor++) {
					$shop_list[$shop_cursor]['distance_text'] = $data['result'][$shop_cursor]['distance']['text'];
				}
			} else {
				for ($shop_cursor = 0; $shop_cursor < $shop_list_length; $shop_cursor++) {
					$shop_list[$shop_cursor]['distance_text'] = $data['message'];
				}
			}
		}
		$this->ajaxReturn($shop_list);
	}

}