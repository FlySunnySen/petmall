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
				       ->where('is_delete','=','0')
				       ->paginate(10);
		
	    $this->assign('user_data',$user_data);

		return $this->fetch();
	}
    
    //黑名单
	public function blackList(){

		$user_data  = Db::name('user')
				       ->alias('a')
				       ->join('user_details b','a.Uid = b.user_Uid')
				       ->where('is_delete','=','1')
				       ->paginate(10);
		
	    $this->assign('user_data',$user_data);
        return $this->fetch();
	}

	public function editUserPwd(){
         halt('hhh');
	}
    //禁用用户
	public function softDelUser(){
        $id = input('id');
        $action = input('action');
        if($action == 1){
        	$status = 0;
        }else{
        	$status = 1;
        }     
        $data['is_delete'] = $status; 
        $rst = Db::name('user')->where('Uid','=',$id)->setField($data);

        
        if($rst){
            $this->success('操作成功','user/index');
        }else{
            $this->error('操作失败');
        }
	}


}