<?php
namespace app\admin\controller;
use think\Db;
/**
* 
*/
class User extends Common
{
	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList',$request->controller().'-'.$request->action());
		$this->assign('menu','user');
	}
	
	public function index(){
		$user_data  = Db::name('user')
				       ->alias('a')
				       ->join('user_details b','a.Uid = b.user_Uid')
				       ->paginate(10);
		
	    $this->assign('user_data',$user_data);
		return $this->fetch();
	}


}