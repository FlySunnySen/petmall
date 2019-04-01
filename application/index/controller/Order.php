<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

// use app\admin\model\GoodType as category;

class Order extends Base {
	/**
	 * ajax 获取订单商品价格 或者提交 订单
	 */
	public function submitOrder() {
		/* 生成acti订单号 */
		$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
		$data['acti'] = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
		$data['user_Uid'] = $_SESSION['uid'];
		$data['order_message'] = input('user_note'); //买家留言
		$data['address_id'] = input("address_id"); //  收货地址id
		$data['create_time'] = time();
		$action = input("action"); // 立即购买
		$address = Db::name('user_address')->where("id", $data['address_id'])->find();
		$cartList = Db::view('good', 'id')
			->view('spec_goods_price', 'item_id', 'spec_goods_price.goods_id=good.id')
			->view('cart', 'num', 'cart.item_id=spec_goods_price.item_id')
			->where(['user_Uid' => $data['user_Uid'], 'selected' => 1])
			->select();
		try {
			$id = Db::name('order')->insertGetId($data); //订单写入数据库
			/* 订单商品详情 */
			foreach ($cartList as $key => $value) {
				# code...
				Db::name('order_good')->data(['order_id' => $id, 'goods_id' => $value['id'], 'item_id' => $value['item_id'], 'goods_number' => $value['num']])->insert();
			}
			/* 删除购物车商品 */
			Db::name('cart')->where(['user_Uid' => $data['user_Uid'], 'selected' => 1])->delete();
			$this->ajaxReturn(['status' => 1, 'msg' => '提交订单成功', 'result' => $id]);
		} catch (TpshopException $t) {
			$error = $t->getErrorArr();
			$this->ajaxReturn($error);
		}
	}

	/**
	 * [pay 支付页面]
	 * @return [type] [description]
	 */
	public function pay() {
		$id = input('id');
		return $this->fetch();
	}

	/**
	 * [payPwd 检查是否设置了支付密码]
	 * @return [type] [description]
	 */
	public function checkPwdSet() {
		$uid = $_SESSION['uid'];
		$isSet = Db::name('user_details')->where('user_Uid', '=', $uid)->value('user_pwd');
		if (empty($isSet)) {
			$this->ajaxReturn(['status' => 0, 'msg' => '请设置支付密码']);
		} else {
			$this->ajaxReturn(['status' => 1, 'msg' => '已有支付密码']);
		}

	}

}