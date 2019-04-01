<?php

namespace app\index\controller;
use app\common\logic\CartLogic;
use think\Db;

class Cart extends Base {

	public function index() {
		if (isset($_SESSION['uid'])) {
			$user_uid = $_SESSION['uid'];
			$cartList = Db::view('good', 'sn,goods_name,goods_price,goods_number,goods_img,is_on_sale,type_id')
				->view('spec_goods_price', '*', 'spec_goods_price.goods_id=good.id')
				->view('cart', '*', 'cart.item_id=spec_goods_price.item_id')
				->where('user_Uid', '=', $user_uid)
				->select();
			$userCartGoodsTypeNum = Db::name('cart')->where('user_uid', $user_uid)->count();
			// var_dump($cartList);die;
			$this->assign('userCartGoodsTypeNum', $userCartGoodsTypeNum);
			$this->assign('cartList', $cartList); //普通购物车列表
			// var_dump($cartList);die;
			return $this->fetch();
		} else {
			$this->error('请先登录', url('User/login'));
		}
	}

	/**
	 * 更新购物车，并返回计算结果
	 */
	public function AsyncUpdateCart() {
		$cart = input('cart/a', []);
		$cartLogic = new CartLogic();
		$user_id = $_SESSION['uid'];
		$cartLogic->setUserId($user_id);
		$result = $cartLogic->AsyncUpdateCart($cart, $user_id);
		$this->ajaxReturn($result);
	}

	/**
	 *  购物车加减
	 */
	public function changeNum() {
		$cart = input('cart/a', []);
		if (empty($cart)) {
			$this->ajaxReturn(['status' => 0, 'msg' => '请选择要更改的商品', 'result' => '']);
		}
		$cartLogic = new CartLogic();
		$map['user_Uid'] = $_SESSION['uid'];
		$map['id'] = $cart['id'];
		$item_id = Db::name('cart')->where($map)->value('item_id');
		/* 获取商品库存 */
		if ($item_id !== 0) {
			$good_num = Db::name('spec_goods_price')->where('item_id', '=', $item_id)->value('store_count');
		} else {
			$good_id = Db::name('cart')->where($map)->value('good_id');
			$good_num = Db::name('good')->where($good_id)->value('goods_number');
		}
		if ($cart['goods_num'] > $good_num) {
			$this->ajaxReturn(['status' => 0, 'msg' => '超出库存', 'result' => ['limit_num' => $good_num]]);
		}
		$result = Db::name('cart')->where($map)->update(['num' => $cart['goods_num']]);
		$this->ajaxReturn(['status' => 1, 'msg' => '修改成功', 'result' => '']);
	}

	/**
	 * 删除购物车商品
	 */
	public function delete() {
		$cart_ids = input('cart_ids/a', []);
		foreach ($cart_ids as $key => $value) {
			# code...
			$result = Db::name('cart')->where('id', $value)->delete();
		}
		if ($result !== false) {
			$this->ajaxReturn(['status' => 1, 'msg' => '删除成功', 'result' => $result]);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '删除失败', 'result' => $result]);
		}
	}

	/**
	 * 购物车优惠券领取列表
	 */
	public function getStoreCoupon() {
		$goods_ids = input('goods_ids/a', []);
		$goods_category_ids = input('goods_category_ids/a', []);
		if (empty($goods_ids) && empty($goods_category_ids)) {
			$this->ajaxReturn(['status' => 0, 'msg' => '获取失败', 'result' => '']);
		}
		$CouponLogic = new CouponLogic();
		$newStoreCoupon = $CouponLogic->getStoreGoodsCoupon($goods_ids, $goods_category_ids);
		if ($newStoreCoupon) {
			$user_coupon = Db::name('coupon_list')->where('uid', $this->user_id)->getField('cid', true);
			foreach ($newStoreCoupon as $key => $val) {
				if (in_array($newStoreCoupon[$key]['id'], $user_coupon)) {
					$newStoreCoupon[$key]['is_get'] = 1; //已领取
				} else {
					$newStoreCoupon[$key]['is_get'] = 0; //未领取
				}
			}
		}
		$this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $newStoreCoupon]);
	}

	/**
	 * ajax 将商品加入购物车
	 */
	function ajaxAddCart() {
		$data["good_id"] = input("goods_id/d"); // 商品id
		$data["num"] = input("goods_num/d"); // 商品数量
		$data["item_id"] = input("item_id/d") ? input("item_id/d") : 0; // 商品规格id
		$data["user_Uid"] = $_SESSION['uid'];
		$num["good_id"] = input("goods_id/d"); // 商品id
		$num["item_id"] = input("item_id/d") ? input("item_id/d") : 0; // 商品规格id
		$num["user_Uid"] = $_SESSION['uid'];
		if (empty($data["good_id"])) {
			$this->ajaxReturn(['status' => 0, 'msg' => '请选择要购买的商品', 'result' => '']);
		}
		if (empty($data["num"])) {
			$this->ajaxReturn(['status' => 0, 'msg' => '购买商品数量不能为0', 'result' => '']);
		}
		if ($data["num"] > 200) {
			$this->ajaxReturn(['status' => 0, 'msg' => '购买商品数量大于200', 'result' => '']);
		}
		/* 获得该商品的库存 */
		if ($data["item_id"] != 0) {
			$store_count = Db::name("spec_goods_price")->where("item_id", $data["item_id"])->value('store_count');
		} else {
			$store_count = Db::name("good")->where("id", $data["good_id"])->value('goods_number');
		}
		$map = Db::name("cart")->where($num)->value('num');
		try {
			if (!$map) {
				if ($data['num'] > $store_count) {
					$this->ajaxReturn(['status' => 0, 'msg' => '已大于库存，加入购物车失败']);
				}
				$data['create_time'] = date('Y-m-d H:i:s', time());
				// var_dump($data['create_time']);die;
				$rst = Db::name("cart")->data($data)->insert();

			} else {
				if (($data['num'] + $map) > $store_count) {
					$this->ajaxReturn(['status' => 0, 'msg' => '已大于库存，加入购物车失败']);
				}
				$rst = Db::name("cart")->where($num)->setField('num', $data['num'] + $map);
			}
			if ($rst) {
				$this->ajaxReturn(['status' => 1, 'msg' => '加入购物车成功']);
			} else {
				$this->ajaxReturn(['status' => 0, 'msg' => '加入购物车失败']);
			}
		} catch (TpshopException $t) {
			$error = $t->getErrorArr();
			$this->ajaxReturn($error);
		}
	}

	/**
	 * ajax 将搭配购商品加入购物车
	 */
	public function addCombination() {
		$combination_id = input('combination_id/d'); //搭配购id
		$num = input('num/d'); //套餐数量
		$combination_goods = input('combination_goods/a'); //套餐里的商品
		if (empty($combination_id)) {
			$this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
		}
		$cartLogic = new CartLogic();
		$combination = Combination::get(['combination_id' => $combination_id]);
		$cartLogic->setUserId($this->user_id);
		$cartLogic->setCombination($combination);
		$cartLogic->setGoodsBuyNum($num);
		try {
			$cartLogic->addCombinationToCart($combination_goods);
			$this->ajaxReturn(['status' => 1, 'msg' => '成功加入购物车']);
		} catch (TpshopException $t) {
			$error = $t->getErrorArr();
			$this->ajaxReturn($error);
		}
	}

	/**
	 * 购物车第二步确定页面
	 */
	public function cart2() {
		$user_Uid = $_SESSION['uid'];
		$address = Db::name('user_address')->where('user_Uid', '=', $user_Uid)->select();
		$cartList = Db::view('good', 'sn,goods_name,goods_price,goods_number,goods_img,is_on_sale,type_id')
			->view('spec_goods_price', '*', 'spec_goods_price.goods_id=good.id')
			->view('cart', '*', 'cart.item_id=spec_goods_price.item_id')
			->where(['user_Uid' => $user_Uid, 'selected' => 1])
			->select();
		$sum = 0;
		foreach ($cartList as $key => $value) {
			# code...
			$sum += $value['price'] * $value['num'];
		}
		$this->assign('count', $sum); //订单总价
		$this->assign('user_address', $address);
		// var_dump($cartList);die;
		$this->assign('cartList', $cartList);
		return $this->fetch();
	}

	//ajax 请求购物车列表
	public function header_cart_list() {
		if (isset($_SESSION['uid'])) {
			$user_uid = $_SESSION['uid'];
		} else {
			$user_uid = 0;
		}
		if ($user_uid) {
			$cartList = Db::view('good', 'sn,goods_name,goods_price,goods_number,goods_img')
				->view('spec_goods_price', '*', 'spec_goods_price.goods_id=good.id')
				->view('cart', '*', 'cart.item_id=spec_goods_price.item_id')
				->where('user_Uid', '=', $user_uid)
				->select();
			$this->assign('cartList', $cartList); // 购物车的商品
		}
		return $this->fetch('header_cart_list');
	}

	/**
	 * 删除购物车商品
	 */
	public function deleteCart() {
		$cart_ids = input('cart_ids/a', []);
		$result = Db::name('cart')->delete($cart_ids);
		if ($result !== false) {
			$this->ajaxReturn(['status' => 1, 'msg' => '删除成功', 'result' => $result]);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '删除失败', 'result' => $result]);
		}
	}

	/**
	 * [saveAddress 保存收获地址]
	 * @return [type] [description]
	 */
	public function saveAddress() {
		var_dump(input('post.'));
		var_dump('ok');die;

	}

	/**
	 * [ajaxAddress 获取用户收货地址 用于购物车确认订单页面]
	 * @return [type] [description]
	 */
	public function ajaxAddress() {
		$user_Uid = $_SESSION['uid'];
		$address_list = Db::name('user_address')->where($user_Uid)->order('is_default desc')->select();
		if ($address_list) {
			$area_id = array();
			foreach ($address_list as $val) {
				$area_id[] = $val['address_province'];
				$area_id[] = $val['address_city'];
				$area_id[] = $val['address_county'];
			}
			$area_id = array_filter($area_id);
			$area_id = implode(',', $area_id);
			// var_dump($area_id);die;
			$regionList = Db::name('region')->where("id", "in", $area_id)->column('id,name');
			$this->assign('regionList', $regionList);
		}
		$address_where['is_default'] = 1;
		$c = Db::name('UserAddress')->where(['user_Uid' => $user_Uid, 'is_default' => 1])->count(); // 看看有没默认收货地址
		if ((count($address_list) > 0) && ($c == 0)) // 如果没有设置默认收货地址, 则第一条设置为默认收货地址
		{
			$address_list[0]['is_default'] = 1;
		}
		$this->assign('address_list', $address_list);
		return $this->fetch('ajax_address');
	}

}
