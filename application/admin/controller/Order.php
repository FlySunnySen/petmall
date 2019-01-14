<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
// use app\admin\model\GoodType as category;
class Order extends Common
{

	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList',$request->controller().'-'.$request->action());
		$this->assign('menu','Order');
	}

	public function index(){
		$order_data = Db::name('order')->select();
		// var_dump($order_data);die;
		$this->assign('order_data',$order_data);
		return $this->fetch('index');
	}
}