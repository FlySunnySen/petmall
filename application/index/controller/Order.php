<?php
namespace app\index\controller;
use think\Controller;

// use app\admin\model\GoodType as category;

class Order extends Base {
	public function add() {
		var_dump(input('post.'));
	}

}