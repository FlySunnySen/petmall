<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class User extends Controller {
	//登录
	public function login() {
		$redirst = input("redirst") ? input("redirst") : url("index/index/index");
		if (request()->isPost()) {
			$data = request()->param();
			$map['user_email'] = strtolower($data['email']);
			$map['user_pwd'] = md5(md5($data['password']));
			$rst = Db::name('user')
				->alias('a')
				->join('user_details b', 'a.Uid = b.user_Uid')
				->where($map)
				->find();

			if ($rst['is_delete'] == 1) {
				$this->error('该用户已被禁用');
			} elseif ($rst['is_delete'] == 0) {
				if (isset($data['holdStatus']) && $data['holdStatus'] == 1) {
					//使用cookie实现自动登录
					setcookie("user_email", $map['user_email'], time() + 3600, '/');
					setcookie("user_pwd", md5($data['password']), time() + 3600, '/');

				}
				session_start();
				$_SESSION['alias'] = $rst['user_alias'];
				$_SESSION['user'] = $rst['user_email'];
				$_SESSION['uid'] = $rst['Uid'];
				$this->success('登录成功', $data['redirst']);

			} else {
				$this->error('账号或密码错误');
			}
		}
		$this->assign('redirst', $redirst);
		return $this->fetch();
	}

	public function logout() {
		session_start();
		setcookie("PHPSESSION", "", time() - 1, "/");
		setcookie("user_email", "", time() - 1, "/");
		session_destroy();
		$this->success("注销成功", 'index/index');
	}

	//注册
	public function reg() {
		return $this->fetch('register');
	}

	//注册时检测邮箱
	public function checkEmail() {
		$email = strtolower(input('email'));
		$result = Db::name('user')->where('user_email', '=', $email)->find();
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	//生成验证码
	public function captcha() {
		$config = [
			'codeSet' => '0123456789',
			'fontSize' => 50,
			'length' => 4,

		];
		$captcha = new \think\captcha\Captcha($config);
		return $captcha->entry();
	}

	//检测验证码
	public function checkCaptcha() {
		$captcha = input('code');
		$status = captcha_check($captcha);
		return $status;
	}

	//用户进行注册
	public function register() {
		$userData['user_email'] = strtolower(input('email'));
		$userData['user_pwd'] = md5(md5(input('post.pwd')));
		$userData['is_delete'] = 0;
		$userData['reg_time'] = date('Y-m-d H:i:s', time());

		$rst = Db::name('user')->where('user_email', '=', $userData['user_email'])->find();
		//判断是否重复注册
		if ($rst) {
			$this->error('该用户已经注册过了');
		}

		$status = false;

		Db::startTrans();
		try {
			Db::name('user')->insert($userData);
			$userId = Db::name('user')->getLastInsID();
			Db::name('user_details')->data(['user_Uid' => $userId, 'user_alias' => '用户' . time() . rand(0, 10000)])->insert();
			Db::commit();
			$status = true;

		} catch (\Exception $e) {

			Db::rollback();

		}

		if ($status) {
			$this->success('注册成功', url('index/user/login'));
		} else {
			$this->error('注册失败', url('index/user/reg'));
		}

	}

}
?>