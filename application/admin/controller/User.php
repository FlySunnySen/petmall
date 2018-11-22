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
        $map['Uid'] = input('Uid');
        $old_pwd = Db::name('user')->where($map)->value('user_pwd');
        $new_pwd = md5(md5(input('pwd')));
        //如果新密码与旧密码相同
        if($old_pwd == $new_pwd){
             $arr = array(
                   'code' => 304,
                   'tip'  => '与旧密码相同'
             );
             return json_encode($arr);
        }
        $rst = Db::name('user')->where($map)->setField('user_pwd',$new_pwd);
        if($rst){
        	 $arr = array(
                   'code' => 200,
                   'tip'  => '修改成功'
             );       	
        }else{
        	 $arr = array(
                   'code' => 500,
                   'tip'  => '修改失败'
             );	
        }
        return json_encode($arr);
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
            return json_encode(1);
        }else{
             return json_encode(0);
        }

	}


}