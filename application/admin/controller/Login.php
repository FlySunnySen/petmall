<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;

class Login extends Controller {
	public function index() {
		return $this->fetch();
	}

	// 检测管理员是否可登陆
	public function checkLogin() {
		if (request()->isPost()) {
			$admin_name = input('admin_name');
			$admin_pwd = input('admin_pwd');
			$where = array(
				'admin_name' => $admin_name,
				'pwd' => md5(md5($admin_pwd)),
			);
			$r = Db::name('admin')->where($where)->find();
			if (!empty($r)) {
				$user = array(
					'id' => $r['id'],
					'admin_name' => $r['admin_name'],
					'admin_level' => $r['admin_level'],
					'logtime' => time(),
				);
				session_start();
				// session('admin',$admin_user);//把用户信息存到session
				Session::set('admin', $user);
				$lastlogin['admin_login'] = time();
				Db::name('admin')->where('id', '=', $r['id'])->update($lastlogin);
				exit(json_encode(1));
			} else {
				exit(json_encode(0));
			}
		}
	}

	// 管理员登出
	public function logout() {
		session('admin', null);
		$this->redirect('Login/index');
	}
}