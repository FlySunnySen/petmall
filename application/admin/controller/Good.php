<?php

namespace app\admin\controller;
use app\admin\logic\GoodsLogic;
use app\admin\model\Good as GoodModel;
use think\AjaxPage;
use think\Controller;
use think\Db;
use think\Page;

class Good extends Common {

	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList', $request->controller() . '-' . $request->action());
		$this->assign('menu', 'Goods');
	}

	public function index() {
		$status['is_delete'] = 0;
		$good_data = Db::view('good')
			->view('good_type', 'type_name', 'good.type_id = good_type.id')
			->view('brand', 'brand_name', 'brand.id=good.brand_id')
			->where($status)
			->paginate(10);

		$this->assign('good_data', $good_data);

		return $this->fetch();
	}

	//添加商品
	public function addEditGood() {

		if (request()->isPost()) {
			$GoodModel = new GoodModel();
			$GoodModel->data(request()->param());
			if (request()->param('id')) {
				$count = $GoodModel->isUpdate(true)->allowField(true)->save();
				$info = '修改成功';
			} else {
				$data = request()->param();
				$data['sn'] = time() . rand(1000, 10000);
				$data['goods_time'] = time();
				$map['id'] = $data['type_id'];
				$data['good_model'] = substr(Db::name('good_type')->where($map)->value('parent_id_path'), 2, 1);
				$count = $GoodModel->isUpdate(false)->allowField(true)->save($data);

				$info = '添加成功';
			}
			if ($count) {
				$this->success($info, 'index');
			} else {
				exit;
			}
		}

		$data = model('good_type')->getCategory();
		$brand = Db::name('brand')->select();
		$this->assign('brand', $brand);
		$this->assign('type', $data);

		if (request()->param('id')) {

			$good_data = Db::view('good')
				->view('good_type', 'type_name', 'good.type_id = good_type.id')
				->view('brand', 'brand_name', 'brand.id=good.brand_id')
				->where('good.id', request()->param('id'))
				->find();

			$this->assign('iddata', $good_data);
			return $this->fetch("edit");
			exit;
		}

		return $this->fetch('add');

	}

	//删除商品
	public function softDelGood() {
		if (request()->isGet('id')) {
			$id = request()->param('id');
		}
		$data = GoodModel::get($id);
		$data->is_delete = 1;

		if (false !== $data->save()) {

			return $this->success('删除成功');
		} else {

			$this->success('删除失败');
		}
	}

	public function softDelMore() {
		if (request()->isAjax()) {

			$good_id = $_POST['id'];
			$info = "";
			foreach ($good_id as $v) {

				$data = GoodModel::get($v);
				$data->is_delete = 1;

				if (false !== $data->save()) {

					continue;

				} else {

					$info .= $data->goods_name . ' ';

				}

			}
			if (empty($info)) {
				$info = '删除成功';
			} else {
				$info .= '无法删除，其他选中的商品已删除';
			}
			return $info;
		}
	}

	//商品回收站
	public function recycle() {
		$status['is_delete'] = 1;
		$good_data = Db::view('good')
			->view('good_type', 'type_name', 'good.type_id = good_type.id')
			->view('brand', 'brand_name', 'brand.id=good.brand_id')
			->where($status)
			->paginate(10);

		$this->assign('good_data', $good_data);

		return $this->fetch();
	}

	//商品还原
	public function recycleGood() {
		if (request()->isGet('id')) {
			$id = request()->param('id');
		}
		$data = GoodModel::get($id);
		$data->is_delete = 0;

		if (false !== $data->save()) {

			return $this->success('还原成功');
		} else {

			$this->success('还原失败');
		}
	}

	//批量还原
	public function recycleAll() {
		if (request()->isAjax()) {

			$good_id = $_POST['id'];
			$info = "";
			foreach ($good_id as $v) {

				$data = GoodModel::get($v);
				$data->is_delete = 0;

				if (false !== $data->save()) {

					continue;

				} else {

					$info .= $data->goods_name . ' ';

				}

			}
			if (empty($info)) {
				$info = '还原成功';
			} else {
				$info .= '无法还原，其他选中的商品已还原';
			}
			return $info;
		}
	}

	//商品硬删除
	public function delGood() {
		if (request()->isGet('id')) {
			$id = request()->param('id');
		}

		$count = model('good')->where('id', $id)->delete();

		if ($count) {

			return $this->success('删除成功');
		} else {

			$this->success('删除失败');
		}
	}

	/**
	 * 商品类型  用于设置商品的属性
	 */
	public function goodsTypeList() {
		$model = model("GoodsType");
		$count = $model->count();
		$Page = $pager = new Page($count, 14);
		$show = $Page->show();
		$goodsTypeList = $model->order("id desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('pager', $pager);
		$this->assign('show', $show);
		$this->assign('goodsTypeList', $goodsTypeList);
		// var_dump($goodsTypeList);die;
		return $this->fetch('goodsTypeList');
	}

	/**
	 * 商品规格列表
	 */
	public function specList() {
		$goodsTypeList = model("GoodsType")->select();
		var_dump($goodsTypeList);die;
		$this->assign('goodsTypeList', $goodsTypeList);
		return $this->fetch('specList');
	}

	/**
	 *  商品规格列表
	 */
	public function ajaxSpecList() {
		//ob_start('ob_gzhandler'); // 页面压缩输出
		$where = ' 1 = 1 '; // 搜索条件
		input('type_id') && $where = "$where and type_id = " . input('type_id');
		// 关键词搜索
		$model = model('spec');
		$count = $model->where($where)->count();
		$Page = new AjaxPage($count, 13);
		$show = $Page->show();
		$specList = $model->where($where)->order('type_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$GoodsLogic = new GoodsLogic();
		foreach ($specList as $k => $v) {
			// 获取规格项
			$arr = $GoodsLogic->getSpecItem($v['id']);
			$specList[$k]['spec_item'] = implode(' , ', $arr);
		}

		$this->assign('specList', $specList);
		$this->assign('page', $show); // 赋值分页输出
		$goodsTypeList = model("GoodsType")->select(); // 规格分类
		$goodsTypeList = self::convert_arr_key($goodsTypeList, 'id');
		$this->assign('goodsTypeList', $goodsTypeList);
		// var_dump($goodsTypeList);die;
		return $this->fetch('ajaxSpecList');
	}

	/**
	 * 动态获取商品规格选择框 根据不同的数据返回不同的选择框
	 */
	public function ajaxGetSpecSelect() {
		$goods_id = input('get.goods_id/d') ? input('get.goods_id/d') : 0;
		// var_dump($goods_id);die;
		$GoodsLogic = new GoodsLogic();
		//$_GET['spec_type'] =  13;
		$specList = model('Spec')->where("type_id = " . input('get.spec_type/d'))->order('order desc')->select();
		// var_dump($specList);die;
		foreach ($specList as $k => $v) {
			$specList[$k]['spec_item'] = model('SpecItem')->where("spec_id = " . $v['id'])->order('id')->column('id,item');
		}
		// 获取规格项

		$items_id = model('SpecGoodsPrice')->where('goods_id = ' . $goods_id)->column("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id");
		$items_ids = explode('_', $items_id[0]);

		$this->assign('items_ids', $items_ids);
		$this->assign('specList', $specList);
		// dump($specList);die;
		return $this->fetch('ajax_spec_select');
	}

	/**
	 * 动态获取商品规格输入框 根据不同的数据返回不同的输入框
	 */
	public function ajaxGetSpecInput() {
		$GoodsLogic = new GoodsLogic();
		$goods_id = input('goods_id') ? input('goods_id') : 7;
		$str = $GoodsLogic->getSpecInput($goods_id, input('post.spec_arr/a', [[]]));
		// var_dump($str);die;
		exit($str);
	}

	/**
	 * @param $arr
	 * @param $key_name
	 * @return array
	 * 将数据库中查出的列表以指定的 id 作为数组的键名
	 */
	public function convert_arr_key($arr, $key_name) {
		$arr2 = array();
		foreach ($arr as $key => $val) {
			// var_dump($val);die;
			$arr2[$val[$key_name]] = $val;
		}
		return $arr2;
		// var_dump($arr2);die;
	}

	public function goodModel() {
		$GoodsLogic = new GoodsLogic();
		$Goods = new \app\common\model\Good();
		$goods_id = input('id');
		if ($goods_id) {
			$goods = $Goods->where('id', $goods_id)->find();
			$this->assign('goods', $goods);
		}
		$goodsType = Db::name("GoodsType")->select();
		$this->assign('goodsType', $goodsType);
		// var_dump($goodsType);die;
		return $this->fetch('goodModel');
	}

	/*
		保存商品规格
	*/
	public function save() {
		$data = input('post.');
		$spec_item = input('item/a');
		$GoodsLogic = new GoodsLogic();
		$GoodsLogic->afterSave($data['goods_id']);
		// $GoodsLogic->saveGoodsAttr($goods['id'], $goods['goods_type']); // 处理商品 属性
		$return_arr = ['status' => 1, 'msg' => '操作成功'];
		$this->ajaxReturn($return_arr);
	}

	//批量硬删除
	public function delAll() {
		if (request()->isAjax()) {
			$good_id = $_POST['id'];
			$data = "";
			foreach ($good_id as $v) {

				$count = model('good')->where('id', $v)->delete();
				if ($count) {
					continue;
				} else {
					$data .= model('good')->where('id', $v)->value('good_name') . ' ';
				}
			}
			if (empty($data)) {
				$data = '删除成功';
			} else {
				$data .= '删除失败，其他选中的商品已删除';
			}
			return $data;
		}
	}

}
?>