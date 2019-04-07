<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Loader;

class User extends Base {
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

	/**
	 * [addresssave 保存收货地址]
	 * @return [type] [description]
	 */
	public function addresssave() {

		$uid = $_SESSION['uid'];
		$data['user_Uid'] = $uid;
		$data['name'] = input('consignee');
		$data['address_province'] = input('province');
		$data['address_city'] = input('city');
		$data['address_county'] = input('district');
		$data['address_info'] = input('address');
		$data['address_zip'] = input('zipcode');
		$data['phone'] = input('mobile');
		$userAddressValidate = Loader::validate('UserAddress');
		if (!$userAddressValidate->batch()->check($data)) {
			$msg = $userAddressValidate->getError();
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => $msg]);
		}

		if (input('address_id') > 0) {
			$rst = Db::name('user_address')->where('id', 'eq', input('address_id'))->update($data);
		} else {
			$rst = Db::name('user_address')->strict(false)->data($data)->insert();
		}
		if ($rst !== false) {
			$this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败']);
		}
	}

	/**
	 * [address 获取收货地址列表]
	 * @return [type] [description]
	 */
	public function address() {
		$uid = $_SESSION['uid'];
		$address_id = input('address_id');
		$userAddress = Db::name('user_address')->where(['id' => $address_id, 'user_Uid' => $uid])->find();
		if (empty($userAddress)) {
			$this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
		}
		$city_list = Db::name('region')->where('parent_id', $userAddress['address_province'])->select();
		$district_list = Db::name('region')->where('parent_id', $userAddress['address_city'])->select();
		$twon_list = Db::name('region')->where('parent_id', $userAddress['address_county'])->select();
		$this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => ['user_address' => $userAddress, 'city_list' => $city_list, 'district_list' => $district_list, 'twon_list' => $twon_list]]);

	}
	/**
	 * [addressDelete 删除收货地址]
	 * @return [type] [description]
	 */
	public function addressDelete() {

		$address_id = input('address_id');
		$uid = $_SESSION['uid'];
		$deleteAddress = Db::name('user_address')->where(['id' => $address_id])->find();
		if (empty($deleteAddress)) {
			$this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
		}
		$row = Db::name('user_address')->where('id', $deleteAddress['id'])->delete();
		// 如果删除的是默认收货地址 则要把第一个地址设置为默认收货地址
		if ($deleteAddress['is_default'] == 1) {
			Db::name('user_address')
				->where('user_Uid', $uid)
				->data(['is_default' => 1])
				->limit(1)
				->update();
		}
		if ($row !== false) {
			$this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败']);
		}
	}

	/**
	 * [addresssetdefault 设置默认地址]
	 * @return [type] [description]
	 */
	public function addresssetdefault() {

		$uid = $_SESSION['uid'];
		$address_id = input('address_id');
		Db::name('user_address')->where('user_Uid', $uid)->update(['is_default' => 0]);
		$row = Db::name('user_address')->where('id', '=', $address_id)->update(['is_default' => 1]);
		if ($row !== false) {
			$this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败']);
		}
	}

	/**
	 * [setPwd 设置支付密码]
	 */
	public function setPwd() {
		$uid = $_SESSION['uid'];
		$pwd = input('pwd');
		$rst = Db::name('user_details')->where('user_Uid', '=', $uid)->update(['user_pay_pwd' => $pwd]);
		if ($rst) {
			$this->ajaxReturn(['status' => 1, 'msg' => '操作成功！']);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败！']);
		}
	}

}
?>