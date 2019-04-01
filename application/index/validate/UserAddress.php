<?php
namespace app\index\validate;

use think\Validate;

class UserAddress extends Validate {
	// 验证规则
	protected $rule = [
		'name' => 'require|max:60',
		'address_province' => 'require|gt:0',
		'address_city' => 'require|gt:0',
		'address_county' => 'require|gt:0',
		'address_info' => 'require|max:255',
		'phone' => 'require|checkMobile',
	];
	//错误信息
	protected $message = [
		'name.require' => '收货人不能为空',
		'name.max' => '收货人长度不得超过60字符',
		'address_province.require' => '省份必须选择',
		'address_city.require' => '市必须选择',
		'address_county.require' => '镇/区必须选择',
		'address_province.gt' => '请选择省',
		'address_city.gt' => '请选择市',
		'address_county.gt' => '请选择镇/区',
		'address_info.require' => '地址不能为空',
		'address_info.max' => '地址名称最多不能超过255个字符',
		'phone.require' => '手机号不能为空',
	];

	/**
	 * 检查活动时间
	 * @param $value |验证数据
	 * @param $rule |验证规则
	 * @param $data |全部数据
	 * @return bool|string
	 */
	protected function checkMobile($value, $rule, $data) {
		if (!check_mobile($data['phone']) && !check_telephone($data['phone'])) {
			return '手机号码格式有误';
		}
		return true;
	}

}