<?php
namespace app\admin\controller;
use think\Controller;
class Index extends Common
{
	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList',$request->controller().'-'.$request->action());
		$this->assign('menu','index');
	}
	
    public function index()
    {
  //   	$this->assign('vo',13);
		// $this->assign('page',111);
        return $this->fetch();
	}
}
