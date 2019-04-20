<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Index extends Common {
	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList', $request->controller() . '-' . $request->action());
		$this->assign('menu', 'index');
	}

	public function index() {
		//实例化
		// $user = M('user'); //用户表
		// $c = M('comment'); //用户评论表
		// $o = M('order_action'); //订单
		// $g = M('goods'); //商品数
		// $s = M('sale'); //秒杀
		//订单统计
		$orderCount = Db::name('order')->count("id");
		$orderPay = Db::name('order')->where('shipping_status=0 and is_delete = 0 and pay_status=1')->count();
		$this->assign('orderCount', $orderCount);
		$this->assign('orderPay', $orderPay);
		//商品统计
		$goodsCount = Db::name('good')->count();
		$this->assign('goodsCount', $goodsCount);
		$goodsVolume = Db::name('good')->sum('sales_sum');
		$this->assign('goodsVolume', $goodsVolume);
		//访问统计
		$clickCount = Db::name('good')->sum('good_click');
		$this->assign('clickCount', $clickCount);
		$commentCount = Db::name('comment')->count();
		$this->assign('commentCount', $commentCount);
		//服务器信息
		$info = array(
			'php' => php_sapi_name(),
			'tp' => THINK_VERSION,
			// 'mysql' => mysql_get_server_info(),
			'apache' => $_SERVER["SERVER_SOFTWARE"],
			'ip' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
			'time' => date('Y-m-d H:i:s', time()),
		);
		$this->assign('info', $info);
		//数据库名称
		$this->assign('dbname', config('database.database'));
		$this->assign('prefix', config('database.prefix'));
		$this->assign('charset', config('database.charset'));
		//查出最新注册的十个用户
		$u = Db::name('user')->order('reg_time desc')->limit(10)->select();
		if ($u) {
			$this->assign('user', $u);
		}
		// var_dump($u);die;
		//查出最新的5条评论
		$com = Db::name('comment')->order('comment_time desc')->limit(5)->column('user_id,comment_content,comment_time');
		if ($com) {
			foreach ($com as &$v) {
				$cu = Db::name('user')->where('Uid=' . $v['user_id'])->value('user_email');
				$v['email'] = $cu;
			}
			$this->assign('com', $com);
		}
		// var_dump($com);die;
		return $this->fetch();
	}
}
