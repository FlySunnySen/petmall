<?php
namespace app\admin\controller;
use think\Controller;
class Index extends Controller
{
	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList',$request->module().'-'.$request->module());
		$this->assign('menu','index');
	}
	
    public function index()
    {
    	$this->assign('vo',13);
		$this->assign('page',111);
        return $this->fetch();
	}
}
