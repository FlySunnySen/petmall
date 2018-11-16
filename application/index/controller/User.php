<?php
namespace app\index\controller;
use think\Db;


class User extends Common
{
	//登录
	public function login(){
		return $this->fetch();
	}
	
	
	
	//注册
	public function reg(){
		return $this->fetch('register');
	}
	
	
	//注册时检测邮箱
	public function checkEmail(){
		$email = input('email');
		
		$result = Db::name('user')->where('user_email','=',$email)->find();
		if($result){
			return true;
		}else{
			return false;
		}
	}
	
	//生成验证码
	public function captcha(){
		$config = [
		     'codeSet' =>'0123456789',
		     'fontSize'  => 50,
		     'length'    => 4,
		     		
		];
		$captcha = new \think\captcha\Captcha($config);
		return $captcha->entry();
	}
	
	//检测验证码
	public function checkCaptcha(){
		$captcha = input('code');
		$status = captcha_check($captcha);
		return $status;
	}
	
	//用户进行注册
	public function register(){
	     $userData['user_email'] = input('post.email');
		 $userData['user_pwd']   = md5(md5(input('post.pwd')));
		 $userData['is_delete']  = 1;
		 Db::startTrans();
		 try{
              Db::name('user')->insert($userData);
              $userId = Db::name('user')->getLastInsID();
              Db::name('user_details')->data(['user_Uid' => $userId,'user_alias' => '用户'.time().rand(0,10000)])->insert();            
              Db::commit();
              $status = true;
		 } catch(\Exception $e){
		 		
             Db::rollback();
		 }
		 if($status){
		 	$this->success('注册成功',url('index/user/login'));
		 }
		 
	}
	
}
?>