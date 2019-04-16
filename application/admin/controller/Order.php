<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Page;
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
		// $where['is_delete'] = 0;
		if (input('type') == 'noPay') {
			$where['pay_status'] = 0;
			$where['is_delete'] = 0;
		}
		if (input('type') == 'noFollow') {
			$where['shipping_status'] = 0;
			$where['is_delete'] = 0;
		}
		if (input('type') == 'follow') {
			$where['shipping_status'] = 1;
			$where['is_delete'] = 0;
		}
		if (input('type') == 'del') {
			$where['is_delete'] = 1;
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
	/**
	 * [comment 评论列表]
	 */
	public function comment() {
		$where = null;
		if (input('type') == 'noReply') {
			$where['is_reply'] = 0;
		}
		if (input('type') == 'good') {
			$where['comment_rank'] = ['in', '4,5'];
		}
		if (input('type') == 'bad') {
			$where['comment_rank'] = ['in', '1,2,3'];
		}
		$count = Db::name('comment')->where($where)->count();
		$Page = new Page($count, 10);
		$show = $Page->show();
		$order_str = "comment_time DESC";
		$comment_list = Db::view('comment')->view('good', 'goods_name,goods_price,goods_img', 'good.id=comment.good_id')->order($order_str)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('comment_list', $comment_list);
		$this->assign('page', $show);
		// var_dump($comment_list);die;
		return $this->fetch();
	}
	/**
	 * [reply 回复评论]
	 * @return [type] [description]
	 */
	public function reply() {

		$data['reply_content'] = input('num');
		$data['reply_time'] = time();
		$data['is_reply'] = 1;
		$rst = Db::name('comment')->where('id', '=', input('id'))->update($data);
		if ($rst) {
			$this->ajaxReturn(['status' => 1, 'msg' => '回复成功']);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '回复失败']);
		}
	}

	/**
	 * [delComment 删除评论]
	 * @return [type] [description]
	 */
	public function delComment() {

		$rst = Db::name('comment')->where('id', '=', input('id'))->delete();
		if ($rst) {
			$this->ajaxReturn(['status' => 1, 'msg' => '删除成功']);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '删除失败']);
		}
	}

	/**
	 * [delOrder 删除订单]
	 * @return [type] [description]
	 */
	public function delOrder() {
		$id = input('id');
		$data['comeback_reason'] = input('num');
		$data['is_delete'] = 1;
		$rst = Db::name('order')->where('id', '=', $id)->update($data);
		if ($rst) {
			$this->ajaxReturn(['status' => 1, 'msg' => '删除成功']);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '删除失败']);
		}
	}

}