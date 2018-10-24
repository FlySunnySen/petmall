<?php
namespace app\admin\controller;
class Ad extends Common
{
	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList',$request->module().'-'.$request->module());
		$this->assign('menu','Ad');
	}
	
	 public function index()
	 {
	 	$this->assign('vo',123);
		$this->assign('page',123);
	 	return $this->fetch();
	 }
	 
	 public function add()
	 {
	 	return $this->fetch();
	 }
}
?>