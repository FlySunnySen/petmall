<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Page;

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
		$data['order_count'] = 0;
		$action = input("action"); // 立即购买
		$address = Db::name('user_address')->where("id", $data['address_id'])->find();
		$cartList = Db::view('good', 'id,goods_price')
			->view('spec_goods_price', 'price', 'spec_goods_price.goods_id=good.id')
			->view('cart', 'num,item_id', 'cart.item_id=spec_goods_price.item_id')
			->where(['user_Uid' => $data['user_Uid'], 'selected' => 1])
			->select();

		/* 计算订单总金额 */
		foreach ($cartList as $key => $value) {
			# code...
			if ($cartList[0]['item_id'] == 0) {
				$data['order_count'] += $value['goods_price'] * $value['num'];
			} else {
				$data['order_count'] += $value['price'] * $value['num'];
			}
		}
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
	 * [cancel_order 取消订单]
	 * @return [type] [description]
	 */
	public function cancel_order() {
		$data['is_delete'] = 1;
		$data['comeback_reason'] = "用户自己取消";
		$rst = Db::name('order')->where('id', '=', input('id'))->update($data);
		if ($rst) {
			$this->ajaxReturn(['status' => 1, 'msg' => '取消成功']);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '系统繁忙，请稍后再试']);
		}
	}
	/**
	 * [wuliu 物流信息]
	 * @return [type] [description]
	 */
	public function wuliu() {
		$id = input('id');
		$this->assign('id', $id);
		return $this->fetch();
	}
	public function wuliuInfo() {
		$id = input('id');
		$host = "https://wuliu.market.alicloudapi.com"; //api访问链接
		$path = "/kdi"; //API访问后缀
		$method = "GET";
		$appcode = "229439ea1896413b831e4c26c32b3f28"; //替换成自己的阿里云appcode
		$headers = array();
		array_push($headers, "Authorization:APPCODE " . $appcode);
		$querys = "no=" . $id; //参数写在这里
		$bodys = "";
		$url = $host . $path . "?" . $querys; //url拼接
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		// curl_setopt($curl, CURLOPT_HEADER, true);
		// 如不输出json, 请打开这行代码，打印调试头部状态码。
		//状态码: 200 正常；400 URL无效；401 appCode错误； 403 次数用完； 500 API网管错误
		if (1 == strpos("$" . $host, "https://")) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		$data = json_decode(curl_exec($curl));
		$this->ajaxReturn($data);
	}
	/**
	 * [info 订单详情]
	 * @return [type] [description]
	 */
	public function info() {
		$id = input('id');
		$address_id = Db::name('order')->where('id', '=', $id)->value('address_id');
		$express_number = Db::name('order')->where('id', '=', $id)->value('express_number');
		$address = Db::name('user_address')->where('id', '=', $address_id)->find();
		$address['address_province'] = Db::name('region')->where('id', '=', $address['address_province'])->value('name');
		$address['address_city'] = Db::name('region')->where('id', '=', $address['address_city'])->value('name');
		$address['address_county'] = Db::name('region')->where('id', '=', $address['address_county'])->value('name');
		$this->assign('address', $address);
		$this->assign('express_number', $express_number);
		// var_dump($address);die;
		return $this->fetch();
	}
	/**
	 * [pay 支付页面]
	 * @return [type] [description]
	 */
	public function pay() {
		$id = input('id');
		$this->assign('orderID', $id);
		return $this->fetch();
	}

	/**
	 * [payPwd 检查是否设置了支付密码]
	 * @return [type] [description]
	 */
	public function checkPwdSet() {
		$uid = $_SESSION['uid'];
		$isSet = Db::name('user_details')->where('user_Uid', '=', $uid)->value('user_pay_pwd');
		if (empty($isSet)) {
			$this->ajaxReturn(['status' => 0, 'msg' => '请设置支付密码']);
		} else {
			$this->ajaxReturn(['status' => 1, 'msg' => '已有支付密码']);
		}

	}

	/**
	 * [payOrder 支付订单]
	 * @return [type] [description]
	 */
	public function payOrder() {
		$uid = $_SESSION['uid'];
		$pwd = input('pwd');
		$orderID = input('orderID');
		$checkPwd = Db::name('user_details')->where('user_Uid', '=', $uid)->value('user_pay_pwd');
		/* 检查支付密码 */
		if ($checkPwd !== $pwd) {
			$this->ajaxReturn(['status' => 0, 'msg' => '支付密码错误']);
			die;
		}
		/* 检查余额是否充足 */
		$userMoney = Db::name('user_details')->where('user_Uid', '=', $uid)->value('user_money');
		$orderMoney = Db::name('order')->where('id', '=', $orderID)->value('order_count');
		if ($orderMoney > $userMoney) {
			$this->ajaxReturn(['status' => 0, 'msg' => '用户余额不足']);
			die;
		}
		/* 获取订单的商品 */
		$goodList = Db::name('order_good')->where('order_id', '=', $orderID)->select();
		/* 开启事务（减掉用户余额->减掉商品库存->修改订单状态） */
		Db::startTrans();
		try {
			/*  减掉用户余额 */
			Db::name('user_details')->where('user_Uid', '=', $uid)->setDec('user_money', $orderMoney);
			/* 减掉商品库存 */
			foreach ($goodList as $key => $value) {
				Db::name('good')->where('id', '=', $value['goods_id'])->setInc('sales_sum', $value['goods_number']);
				Db::name('good')->where('id', '=', $value['goods_id'])->setDec('goods_number', $value['goods_number']);
				/* 如果item_id不为0 ，则需再减规格表的库存 */
				if ($value['item_id'] !== 0) {
					Db::name('spec_goods_price')->where('item_id', '=', $value['item_id'])->setDec('store_count', $value['goods_number']);
				}
			}
			/* 修改订单状态 */
			Db::name('order')->where('id', '=', $orderID)->update(['pay_status' => 1]);
			// 提交事务
			Db::commit();
			$this->ajaxReturn(['status' => 1, "msg" => '支付成功']);
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			$this->ajaxReturn(['status' => 0, "msg" => '支付失败']);
		}
	}

	/**
	 * [order_list 订单列表]
	 * @return [type] [description]
	 */
	public function order_list() {
		$where['user_Uid'] = $_SESSION['uid'];
		if (input('type') == 'noPay') {
			$where['pay_status'] = 0;
		}
		if (input('type') == 'noFollow') {
			$where['shipping_status'] = 0;
			$where['pay_status'] = 1;
		}
		if (input('type') == 'follow') {
			$where['shipping_status'] = 1;
		}
		if (input('type') == 'comment') {
			$where['is_comment'] = 0;
			$where['shipping_status'] = 2;
		}
		if (input('type') == 'del') {
			$where['is_delete'] = 1;
		}

		$count = Db::name('order')->where($where)->count();
		$Page = new Page($count, 10);
		$show = $Page->show();
		$order_str = "id DESC";
		$order_list = Db::name('order')->order($order_str)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
		// var_dump(Db::getlastsql());die;
		$good = [];
		//获取订单商品
		foreach ($order_list as $k => $v) {
			# code...
			$goods_list = Db::name('order_good')->where('order_id', '=', $v['id'])->select();
			// var_dump($goods_list);die;
			foreach ($goods_list as $key => $value) {
				$good[$key]['goods_name'] = Db::name('good')->where('id', '=', $value['goods_id'])->value('goods_name');
				$good[$key]['img'] = Db::name('good')->where('id', '=', $value['goods_id'])->value('goods_img');
				$good[$key]['goods_id'] = $value['goods_id'];
				$good[$key]['goods_number'] = $value['goods_number'];
				if ($value['item_id'] == 0) {
					$good[$key]['goods_price'] = Db::name('good')->where('id', '=', $value['goods_id'])->value('goods_price');
					$good[$key]['spec_key_name'] = null;
				} else {
					$good[$key]['goods_price'] = Db::name('spec_goods_price')->where('item_id', '=', $value['item_id'])->value('price');
					$good[$key]['spec_key_name'] = Db::name('spec_goods_price')->where('item_id', '=', $value['item_id'])->value('key_name');
				}
			}
			// var_dump($order_list[$k]);die;
			$order_list[$k]['goods_list'] = $good;
		}

		// var_dump($order_list[0]);die;
		// $this->assign('order_status', C('ORDER_STATUS'));
		$this->assign('shipping_status', config('shipping_status'));
		$this->assign('pay_status', config('pay_status'));
		$this->assign('page', $show);
		$this->assign('lists', $order_list);
		$this->assign('active', 'order_list');
		$this->assign('active_status', input('type'));
		return $this->fetch();
	}

	/**
	 * [order_confirm 确认收货]
	 * @return [type] [description]
	 */
	public function order_confirm() {
		$orderID = input('order_id');
		$rst = Db::name('order')->where('id', '=', $orderID)->update(['shipping_status' => 2]);
		if ($rst) {
			$this->ajaxReturn(['status' => 1, 'msg' => '收货成功', 'url' => 'http://www.petmall.com/index/order/order_list.html']);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '网络失败，请刷新页面后重试', 'url' => '']);
		}

	}

	/**
	 * [comment 评论]
	 * @return [type] [description]
	 */
	public function comment() {
		$id = input('id');
		$commentList = Db::view('order_good', 'order_id,is_comment,goods_id')
			->view('order', 'acti,user_Uid,create_time,id', 'order_good.order_id=order.id')
			->view('spec_goods_price', 'key_name', 'spec_goods_price.item_id=order_good.item_id')
			->view('good', 'goods_name,goods_img', 'good.id=order_good.goods_id')
			->where(['order.id' => $id, 'shipping_status' => 2])
			->distinct(true)
			->select();
		$this->assign('comment_list', $commentList);
		return $this->fetch();
	}

	public function addcomment() {
		$data['order_id'] = input('id');
		$data['comment_content'] = input('content');
		$data['user_id'] = $_SESSION['uid'];
		$data['comment_rank'] = input('num');
		$data['good_id'] = input('goodID');
		$data['comment_time'] = time();
		$data['acti'] = Db::name('order')->where('id', '=', $data['order_id'])->value('acti');
		// $rst = Db::name('comment')->insert($data);
		// var_dump(Db::getlastsql());die;
		Db::startTrans();
		try {
			/* 修改评论数 */
			Db::name('good')->where('id', '=', $data['good_id'])->setInc('comment_count', 1);
			/* 修改order_good 是否评论 */
			Db::name('order_good')->where(['order_id' => $data['order_id'], 'goods_id' => $data['good_id']])->update(['is_comment' => 1]);
			/* 如果订单内所有的商品都评论完了 就修改order表的is_comment */
			$check = Db::name('order_good')->where(['order_id' => $data['order_id'], 'is_comment' => 0])->find();
			if (!$check) {
				Db::name('order')->where('id', '=', $data['order_id'])->update(['is_comment' => 1]);
			}
			/* 添加评论 */
			$rst = Db::name('comment')->insert($data);
			// 提交事务
			Db::commit();
			$this->ajaxReturn(['status' => 1, 'msg' => '评论成功']);
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			// var_dump(Db::getlastsql());die;
			$this->ajaxReturn(['status' => 0, 'msg' => '评论失败']);
		}

	}

}