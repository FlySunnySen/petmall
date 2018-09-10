<?php

namespace app\admin\controller;
use think\Controller;

class Goods extends Controller{
	
	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList',$request->module().'-'.$request->module());
		$this->assign('menu','Goods');
	}
	
	
	public function index()
	{
		$this->assign('vo',123);
		$this->assign('page',123);
         return $this->fetch();
    }
	
	//添加商品分类
	public function addEditCategory()
	{
		
		
	}
}
?>