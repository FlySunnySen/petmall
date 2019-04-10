<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Loader;

class User extends Base {

	/**
	 * [goods_collect 个人收藏]
	 * @return [type] [description]
	 */
	public function goods_collect() {
		$collectGood = Db::view('goods_collect', 'add_time')
			->view('good', 'id,goods_name,goods_price,goods_img', 'good.id=goods_collect.goods_id')
			->where('user_id', '=', $_SESSION['uid'])
			->select();
		$this->assign('collectGood', $collectGood);
		return $this->fetch();
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