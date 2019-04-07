<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;

// use app\admin\model\GoodType as category;
class Order extends Common {

	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList', $request->controller() . '-' . $request->action());
		$this->assign('menu', 'Order');
	}

	public function index() {
		$where = [];
		if (input('type') == 'noPay') {
			$where['pay_status'] = 0;
		}
		if (input('type') == 'noFollow') {
			$where['shipping_status'] = 0;
		}
		if (input('type') == 'follow') {
			$where['shipping_status'] = 1;
		}
		$order_data = Db::name('order')->where($where)->select();
		// var_dump($order_data);die;
		$this->assign('order_data', $order_data);
		return $this->fetch('index');
	}
	/**
	 * [send 发货]
	 * @return [type] [description]
	 */
	public function send() {
		$id = input('id');
		$num = input('num');
		if (empty($num)) {
			$this->ajaxReturn(['status' => '0', 'msg' => '请填写单号']);
		}
		if ($id && $num) {
			$rst = Db::name('order')->where('id', '=', $id)->update(['shipping_status' => 1, 'express_number' => $num]);
			if ($rst) {
				$this->ajaxReturn(['status' => '1', 'msg' => '发货成功']);
			} else {
				$this->ajaxReturn(['status' => '0', 'msg' => '发货失败']);

			}
		}

	}

	public function goodList() {
		$id = input('id');
		$good = [];
		$goods_list = Db::name('order_good')->where('order_id', '=', $id)->select();
		// var_dump($goods_list);die;
		foreach ($goods_list as $key => $value) {
			$good[$key]['goods_name'] = Db::name('good')->where('id', '=', $value['goods_id'])->value('goods_name');
			$good[$key]['img'] = Db::name('good')->where('id', '=', $value['goods_id'])->value('goods_img');
			$good[$key]['goods_id'] = $value['goods_id'];
			$good[$key]['goods_number'] = $value['goods_number'];
			if ($value['item_id'] == 0) {
				$good[$key]['goods_price'] = Db::name('good')->where('id', '=', $value['goods_id'])->value('goods_price');
				$good[$key]['spec_key_name'] = null;
				$good[$key]['store_count'] = Db::name('good')->where('id', '=', $value['goods_id'])->value('goods_number');
			} else {
				$good[$key]['goods_price'] = Db::name('spec_goods_price')->where('item_id', '=', $value['item_id'])->value('price');
				$good[$key]['spec_key_name'] = Db::name('spec_goods_price')->where('item_id', '=', $value['item_id'])->value('key_name');
				$good[$key]['store_count'] = Db::name('spec_goods_price')->where('item_id', '=', $value['item_id'])->value('store_count');
			}
		}
		$this->assign('goodList', $good);
		return $this->fetch();
	}

}