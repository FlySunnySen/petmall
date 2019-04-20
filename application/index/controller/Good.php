<?php
namespace app\index\controller;
use app\common\logic\GoodsLogic;
use think\AjaxPage;
use think\Controller;
use think\Db;
use think\Page;

// use app\admin\model\GoodType as category;

class Good extends Base {

	/**
	 * 商品详情页
	 */
	public function goodsInfo() {

		$goodsLogic = new GoodsLogic();
		$goods_id = input("id");
		$Goods = new \app\common\model\Good();
		$goods = $Goods::get($goods_id);
		if (empty($goods) || ($goods['is_on_sale'] == 0 || ($goods['is_delete'] == 1))) {
			$this->error('该商品已经下架', url('Index/index'));
		}
		$spec_goods_price = model('spec_goods_price')->where("goods_id", $goods_id)->column("key,item_id,price,store_count,market_price"); // 规格 对应 价格 库存表
		$goods_images_list = model('GoodImages')->where("good_id", $goods_id)->column("image_url"); // 商品 图册
		/* 获取规格 */
		$filter_spec = $goodsLogic->get_spec($goods_id);

		/* 评价百分比 */
		$map['good_id'] = ['=', $goods_id];
		$map['comment_rank'] = ['>', 3];
		$map1['good_id'] = ['=', $goods_id];
		$map1['comment_rank'] = ['<=', 3];
		$commentGoodComment = Db::name('comment')->where($map)->count();
		$commentBadComment = Db::name('comment')->where($map1)->count();
		$commentListSum = Db::name('comment')->where('good_id', '=', $goods_id)->count();
		if ($commentListSum) {
			$this->assign('commentGood', $commentGoodComment / $commentListSum);
			$this->assign('commentBad', $commentBadComment / $commentListSum);
		} else {
			$this->assign('commentGood', 0);
			$this->assign('commentBad', 0);
		}
		$this->assign('commentGoodComment', $commentGoodComment);
		$this->assign('commentBadComment', $commentBadComment);
		$this->assign('commentListSum', $commentListSum);
		/* 是否收藏 */
		$this->assign('collectStatus', 0);
		if (isset($_SESSION['uid'])) {
			$status = Db::name('goods_collect')->where(['user_id' => $_SESSION['uid'], 'goods_id' => $goods_id])->find();
			$this->assign('collectStatus', $status);
		}
		/* 推荐热卖 */
		$hotGoodList = Db::name('good')->order('sales_sum desc')->limit(5)->select();
		$this->assign('hotGoodList', $hotGoodList);
		$this->assign('filter_spec', $filter_spec); //规格参数
		$this->assign('spec_goods_price', json_encode($spec_goods_price, true)); // 规格 对应 价格 库存表
		$this->assign("good_images_list", $goods_images_list);
		$this->assign('navigate_goods', navigate_goods($goods_id, 1)); // 面包屑导航
		$this->assign("goods", $goods);
		return $this->fetch();
	}
	/**
	 * [collect_goods 收藏商品]
	 * @return [type] [description]
	 */
	public function collect_goods() {
		$data['goods_id'] = input('post.goods_ids/a')[0];
		$data['user_id'] = $_SESSION['uid'];
		$find = Db::name('goods_collect')->where($data)->find();
		if ($find) {
			$this->ajaxReturn(['status' => 1, 'msg' => '已收藏']);
		} else {
			$data['add_time'] = time();
			$rst = Db::name('goods_collect')->data($data)->insert();
			if ($rst) {
				$this->ajaxReturn(['status' => 1, 'msg' => '收藏成功']);
			} else {
				$this->ajaxReturn(['status' => 0, 'msg' => '系统繁忙，请稍后再试']);
			}
		}

	}

	/**
	 * [noCollectLink 取消收藏商品]
	 * @return [type] [description]
	 */
	public function noCollectLink() {
		$data['goods_id'] = input('post.goods_ids/a')[0];
		$data['user_id'] = $_SESSION['uid'];
		$find = Db::name('goods_collect')->where($data)->delete();
		if ($find) {
			$this->ajaxReturn(['status' => 1, 'msg' => '已取消']);
		} else {

			$this->ajaxReturn(['status' => 0, 'msg' => '系统繁忙，请稍后再试']);

		}

	}
	/**
	 * [ajaxComment 获取评论]
	 * @return [type] [description]
	 */
	public function ajaxComment() {
		$goods_id = input("goods_id");
		$commentType = input('commentType', '1'); // 1 全部 2好评 3差评
		$where = ['good_id' => $goods_id];

		$typeArr = array('1' => '0,1,2,3,4,5', '2' => '4,5', '3' => '0,1,2,3');
		$where['comment_rank'] = ['in', $typeArr[$commentType]];
		$count = Db::name('comment')->where($where)->count();
		$page = new AjaxPage($count, 10);
		$show = $page->show();

		$list = Db::name('comment')->order('comment_time desc')->where($where)->select();
		$this->assign('commentlist', $list); // 商品评论
		// var_dump($list);die;
		// $this->assign('replyList', $replyList); // 管理员回复
		$this->assign('page', $show); // 赋值分页输出
		return $this->fetch();
	}

	public function activity() {
		// var_dump('hello');die;
		$goods_id = input('goods_id/d'); //商品id
		$item_id = input('item_id/d'); //规格id
		$goods_num = input('goods_num/d'); //欲购买的商品数量
		$Goods = new \app\common\model\Good();
		$goods = $Goods::get($goods_id);
		// var_dump($goods);die;
		$this->ajaxReturn(['status' => 1, 'msg' => '该商品没有参与活动', 'result' => ['goods' => $goods]]);

	}

	/**
	 * 商品列表页
	 */
	public function goodsList() {

		/* 推荐热卖 */
		$hotGoodList = Db::name('good')->order('sales_sum desc')->limit(5)->select();
		$this->assign('hotGoodList', $hotGoodList);
		$filter_param = array(); // 筛选数组
		$id = input('id'); // 当前分类id
		$sort = input('sort'); // 排序
		$sort_asc = input('sort_asc'); // 排序
		$filter_param['id'] = $id; //加入筛选条件中
		$goodsLogic = new GoodsLogic(); // 前台商品操作逻辑类
		/*  获取显示的筛选菜单 */
		$goodsCate = Db::name('good_type')->where("id", $id)->find(); // 当前分类
		$goodsCate['level'] = count(explode('_', $goodsCate['parent_id_path'])) - 1;
		$cateArr = $goodsLogic->get_goods_cate($goodsCate);
		$cat_id_arr = self::getCatGrandson($id);
		$goods_where = ['is_on_sale' => 1, 'type_id' => ['in', $cat_id_arr]];
		$filter_goods_id = Db::name('good')->where($goods_where)->column("id");
		/* 获取显示的筛选菜单 */
		$filter_menu = $goodsLogic->get_filter_menu($filter_param, 'goodsList');
		/* 获取排序条件 */
		switch ($sort) {
		case 'sales_sum':$where["$sort"] = 'DESC';
			break;
		case 'comment_count':$where["$sort"] = 'DESC';
			break;
		case 'is_new':$where['goods_time'] = 'DESC';
			break;
		case 'shop_price':
			if ($sort_asc == 'asc') {
				$where['goods_price'] = 'ASC';
			} else {
				$where['goods_price'] = 'DESC';
			}
			break;
		default:
			$where = null;
			break;
		}

		$count = count($filter_goods_id);
		$page = new Page($count, 20);
		if ($count > 0) {
			$goods_list = model('good')->where("id", "in", implode(',', $filter_goods_id))->limit($page->firstRow . ',' . $page->listRows)->order($where)->select();
			$filter_goods_id2 = get_arr_column($goods_list, 'id');
			if ($filter_goods_id2) {
				$goods_images = Db::name('good_images')->where("good_id", "in", implode(',', $filter_goods_id2))->cache(true)->select();
			}

		} else {
			$goods_list = null;
			$goods_images = null;
		}
		// print_r($filter_menu);
		$goods_category = Db::name('good_type')->where('is_show=1')->cache(true)->column('id,type_name,pid'); // 键值分类数组
		$navigate_cat = navigate_goods($id); // 面包屑导航
		// var_dump($goods_list);die;
		$this->assign('goods_list', $goods_list);
		$this->assign('navigate_cat', $navigate_cat);
		$this->assign('goods_category', $goods_category);
		$this->assign('filter_menu', $filter_menu); // 筛选菜单
		$this->assign('goodsCate', $goodsCate);
		$this->assign('cateArr', $cateArr);
		$this->assign('filter_param', $filter_param); // 筛选条件
		$this->assign('cat_id', $id);
		$this->assign('page', $page); // 赋值分页输出
		$html = $this->fetch();
		return $html;
	}

	public function getCatGrandson($cat_id) {
		$GLOBALS['catGrandson'] = array();
		$GLOBALS['category_id_arr'] = array();
		// 先把自己的id 保存起来
		$GLOBALS['catGrandson'][] = $cat_id;
		// 把整张表找出来
		$GLOBALS['category_id_arr'] = Db::name("good_type")->column('id,pid');
		// 先把所有儿子找出来
		$son_id_arr = Db::name('good_type')->where("pid", $cat_id)->column('id');
		foreach ($son_id_arr as $k => $v) {
			self::getCatGrandson2($v);
		}
		return $GLOBALS['catGrandson'];
	}

	/**
	 * 递归调用找到 重子重孙
	 * @param type $cat_id
	 */
	public function getCatGrandson2($cat_id) {
		$GLOBALS['catGrandson'][] = $cat_id;
		foreach ($GLOBALS['category_id_arr'] as $k => $v) {
			// 找到孙子
			if ($v == $cat_id) {
				self::getCatGrandson2($k); // 继续找孙子
			}
		}
	}
	/**
	 * [search 查找商品]
	 * @return [type] [description]
	 */
	public function search() {
		$keyWord = input('q');
		if ($keyWord) {
			$this->assign('keyword', $keyWord);
		} else {
			$this->assign('keyword', '');
		}
		$sort = input('sort');
		$sort_asc = input('sort_asc');
		/* 获取排序条件 */
		switch ($sort) {
		case 'sales_sum':$where["$sort"] = 'DESC';
			break;
		case 'comment_count':$where["$sort"] = 'DESC';
			break;
		case 'is_new':$where['goods_time'] = 'DESC';
			break;
		case 'shop_price':
			if ($sort_asc == 'asc') {
				$where['goods_price'] = 'ASC';
			} else {
				$where['goods_price'] = 'DESC';
			}
			break;
		default:
			$where = null;
			break;
		}
		$filter_goods_id = Db::name('good')->where('goods_name', 'like', "%$keyWord%")->column('id');
		// var_dump(Db::getLastSql());die;
		$count = count($filter_goods_id);
		$page = new Page($count, 20);
		if ($count > 0) {
			$goods_list = model('good')->where("id", "in", implode(',', $filter_goods_id))->limit($page->firstRow . ',' . $page->listRows)->order($where)->select();
			$filter_goods_id2 = get_arr_column($goods_list, 'id');
			if ($filter_goods_id2) {
				$goods_images = Db::name('good_images')->where("good_id", "in", implode(',', $filter_goods_id2))->cache(true)->select();
			}

		} else {
			$goods_list = null;
			$goods_images = null;
		}
		$filter_param['key'] = $keyWord;
		/* 推荐热卖 */
		$hotGoodList = Db::name('good')->order('sales_sum desc')->limit(5)->select();
		$this->assign('hotGoodList', $hotGoodList);
		$this->assign('goods_list', $goods_list);
		$this->assign('page', $page); // 赋值分页输出
		$this->assign('filter_param', $filter_param);
		$html = $this->fetch();
		return $html;
	}

}