<?php
namespace app\index\controller;

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
}
?>