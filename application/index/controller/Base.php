<?php

namespace app\index\controller;
use think\Controller;
use think\Db;

class Base extends Controller {
	public $session_id;

	public $cateTrre = array();
	/*
		     * 初始化操作
	*/
	public function _initialize() {
		session_start();
		self::checkLogin();
		$this->public_assign();
	}
	/**
	 * 保存公告变量到 smarty中 比如 导航
	 */
	public function public_assign() {
		$hot_goods = $hot_cate = $cateList = $recommend_goods = array();
		$sql = "select a.goods_name,a.id,a.goods_price,a.goods_img,a.type_id,b.parent_id_path,b.type_name from " . config('database.prefix') . "good as a left join ";
		$sql .= config('database.prefix') . "good_type as b on a.type_id=b.id where  a.is_on_sale=1 order by a.good_click"; //二级分类下热卖商品
		$index_hot_goods = cache('index_hot_goods');
		if (empty($index_hot_goods)) {
			$index_hot_goods = Db::query($sql); //首页热卖商品

		}

		if ($index_hot_goods) {
			foreach ($index_hot_goods as $val) {
				$cat_path = explode('_', $val['parent_id_path']);
				$hot_goods[$cat_path[1]][] = $val;
			}
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

		$this->assign('cateList', $cateList);
		$this->assign('goods_category_tree', $data);
		// return $this->fetch();
	}

	//无限级分类
	public function always_category($path = 0, $level = 1) {
		$data = Db::name('good_type')->where(['pid' => $path])->where(['is_show' => 1])->select();

		foreach ($data as $key => $val) {
			$data[$key]['level'] = $level;

			$son = $this->always_category($val['id'], $level + 1);

			if (!empty($son)) {

				$data[$key]['tmenu'] = $son;
			}
		}
		return $data;
	}

	/*
		     *
	*/
	public function ajaxReturn($data) {
		exit(json_encode($data));
	}

	public function checkLogin() {

		$controller = request()->controller();
		if (empty($_SESSION['user'])) {
			$allowController = ['Index', 'User', 'Good'];
			// die;
			if (!in_array($controller, $allowController)) {
				if (empty($_COOKIE['user_email']) || empty($_COOKIE['user_pwd'])) {
					header("location:login.php?req_url=" . $_SERVER['REQUEST_URI']); //转到登录页面，记录请求的url，登录后跳转过去，用户体验好。
					$url = $_SERVER['REQUEST_URI'];

				} else {
					$map['user_email'] = $_COOKIE['user_email'];
					$map['user_pwd'] = md5($_COOKIE['user_pwd']);
					$rst = Db::name('user')
						->alias('a')
						->join('user_details b', 'a.Uid = b.user_Uid')
						->where($map)
						->find();
					if ($rst) {
						$_SESSION['alias'] = $rst['user_alias'];
						$_SESSION['user'] = $rst['user_email'];
						$_SESSION['uid'] = $rst['Uid'];
						$this->success('登录成功', 'index/index');
					} else {
						$this->error('请先登录', 'user/login');
					}
				}
			}

		}

	}
}