<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;

class Admin extends Common {

	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList', $request->controller() . '-' . $request->action());
		$this->assign('menu', 'admin');
	}
	public function index() {
		$maps = null;
		if (input('keywords')) {
			$maps['admin_name'] = array('like', '%' . input('keywords') . '%');
		}
		$count = Db::name('admin')->where($maps)->count();

		// 调用自定义的分页
		$mypage = $this->mypage($count, 8);

		$list = Db::name('admin')->where($maps)->limit($mypage->firstRow . ',' . $mypage->listRows)->select();

		$show = $mypage->show(); // 分页显示输出

		$i = 0;
		foreach ($list as &$v) {
			$group = Db::name('auth_group')->where('id=' . $v['admin_level'])->field('id,title')->find();
			$list[$i]['level_name'] = $group['title'];
			$i++;
		}

		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出

		$this->assign('admin', 'AdminUser');
		$this->assign('menuList', 'AdminUserIndex');
		return $this->fetch();
	}

	// 添加管理员
	public function add() {
		// 分配左侧菜单样式变量
		$this->assign('admin', 'AdminUser');
		$this->assign('menuList', 'AdminUserAdd');
		//遍历出管理员组
		// $g = M('auth_group');
		$group = Db::name('auth_group')->field('id,title')->select();
		$this->assign('group', $group);
		return $this->fetch();
	}

	// 执行添加管理员
	public function insert() {
		if (request()->isPost()) {
			$post['pwd'] = md5(md5(input('admin_pwd')));
			$post['admin_addtime'] = time();
			$post['admin_name'] = input('admin_name');
			$level = input('admin_level');
			$post['admin_level'] = $level;
			$uid = Db::name('admin')->insertGetId($post);
			$gid = Db::name('auth_group_access')->insert(['uid' => $uid, 'group_id' => $level]);
			if ($uid && $gid) {
				$this->ajaxReturn(2);
			} else {
				$this->ajaxReturn(1);
			}

		}

	}

	// 检查管理员是否已存在
	public function checkName() {
		$where['admin_name'] = input('adname');
		$admins = Db::name('admin')->where($where)->find();
		if ($admins) {
			$this->ajaxReturn(1);
		} else {
			$this->ajaxReturn(0);
		}

	}

	// 编辑管理员
	public function save() {

		$map['id'] = array('eq', input('id'));
		$data = Db::name('admin')->where($map)->find();
		$this->assign('user', $data);
		//遍历出管理员组
		$group = Db::name('auth_group')->field('id,title')->select();
		$this->assign('group', $group);
		$this->assign('admin', 'AdminUser');
		$this->assign('menuList', 'AdminUserIndex');
		return $this->fetch();
	}

	// 执行修改
	public function save_doit() {
		// 分配左侧菜单样式变量
		$this->assign('admin', 'AdminUser');
		Db::startTrans();
		try {
			Db::name('admin')->where('id', '=', input('id'))->update(['admin_level' => input('admin_level')]);
			Db::name('auth_group_access')->where('uid', '=', input('id'))->update(['group_id' => input('admin_level')]);
			// 提交事务
			Db::commit();
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			// var_dump(Db::getlastsql());die;
			$this->success('修改失败');
		}
		$this->success('修改成功');

	}
	/**
	 * [editPwd 修改密码]
	 * @return [type] [description]
	 */
	public function editPwd() {
		if (request()->isAjax()) {
			$rst = Db::name('admin')->where('id', '=', input('id'))->update(['pwd' => md5(md5(input('pwd')))]);
			if ($rst) {
				$this->ajaxReturn(['status' => 1, 'msg' => '修改成功']);
			} else {
				$this->ajaxReturn(['status' => 0, 'msg' => '修改失败']);
			}
		}

	}

	// 执行删除
	public function del() {
		$map = 'id=' . input('id');
		$admin = Db::name('admin')->where($map)->delete();
		$group = Db::name('auth_group_access')->where('uid', '=', input('id'))->delete();
		if ($admin) {
			echo 1;
		} else {
			echo 2;
		}
	}

}