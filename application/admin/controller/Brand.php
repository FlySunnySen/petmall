<?php
namespace app\admin\controller;

use app\admin\model\Brand as BrandModel;
use think\Controller;
use think\Db;
use think\Request;

class Brand extends Common {

	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList', $request->controller() . '-' . $request->action());
		$this->assign('menu', 'Goods');
	}

	public function index() {
		$brand = Db::name('brand')->paginate(10);
		$this->assign('brand', $brand);

		return $this->fetch();
	}

	public function addEditBrand() {
		$request = Request::instance();

		//添加或修改品牌操作
		if ($request->isPost()) {
			$BrandModel = new BrandModel();
			$BrandModel->data($request->param());
			//判断是插入还是修改
			if ($request->param('id')) {
				$count = $BrandModel->isUpdate(true)->allowField(true)->save();
				$info = "修改成功";
			} else {
				$count = $BrandModel->isUpdate(false)->allowField(true)->save();
				$info = "添加成功";
			}
			if ($count) {
				$this->success($info, 'index');
			} else {
				$this->error('失败');
			}
			exit;
		}

		//获取品牌的修改页面
		if ($request->param('id')) {
			$info = Db::name('brand')->where('id', $request->param('id'))->find();
			$this->assign('brand', $info);
			return $this->fetch('edit');
			exit;
		}

		$this->assign('vo', 123);

		return $this->fetch('add');
	}

	//删除品牌
	public function delBrand($id) {

		if (request()->isGet('id')) {
			$id = request()->param('id');
		}
		$count = model('good')->where('brand_id', $id)->select();
		if ($count) {

			return $this->error('该品牌存在商品');
		} else {
			$destroy = model('brand')->where('id', $id)->delete();

			$this->success('删除成功');
		}
	}

	public function delMore() {
		if (request()->isAjax()) {

			$good_id = $_POST['id'];
			$data = "";
			foreach ($good_id as $v) {

				$count = model('good')->where('brand_id', $v)->select();
				if ($count) {
					$data .= model('brand')->where('id', $v)->value('brand_name') . ' ';
				} else {
					$del = model('brand')->where('id', $v)->delete();
				}
			}
			if (empty($data)) {
				$data = '删除成功';
			} else {
				$data .= '下有商品，无法删除，其他选中的品牌已删除';
			}
			return $data;
		}
	}

}

?>