<?php
/*
 *	权限管理
 *
 */
namespace app\Admin\Controller;
use think\Db;

class Rule extends Common {
	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList', $request->controller() . '-' . $request->action());
		$this->assign('menu', 'admin');
	}
	/*
		* 角色管理
	*/
	public function rule() {
		// 分配左侧菜单样式变量
		$this->assign('menuList', 'AdminUserrule');
		// 搜索条件
		if (input('get.keywords')) {
			$keyword = input('get.keywords');
			$keyword = "title like '%" . $keyword . "%'";
		} else {
			$keyword = '';
		}

		$count = Db::name('auth_rule')->where($keyword)->count(); // 查询满足要求的总记录数

		// 调用自定义的分页
		$mypage = $this->mypage($count, 8);
		//读取规则表 auth_rule

		$rule = Db::name('auth_rule')->order('id desc')->where($keyword)->limit($mypage->firstRow . ',' . $mypage->listRows)->select();
		$this->assign('list', $rule);
		// var_dump($rule);die;
		$show = $mypage->show(); // 分页显示输出
		$this->assign('page', $show); // 赋值分页输出
		return $this->fetch();
	}

	/*
		*	添加规则
	*/
	Public function insert() {
		$data['name'] = input('controller') . '/' . input('action');
		$data['title'] = input('title');
		if (!empty($data)) {
			$rst = Db::name('auth_rule')->insert($data);
			if ($rst) {
				$this->ajaxReturn(1);
			} else {
				$this->ajaxReturn(0);
			}
		} else {
			$this->ajaxReturn(0);
		}
	}

	/*
		*	编辑用户组
	*/
	public function group() {
		// 分配左侧菜单样式变量
		$this->assign('admin', 'AdminUser');
		$this->assign('menuList', 'AdminUsergroup');

		//用户管理员组
		$group = Db::name('auth_group')->select();

		$this->assign('list', $group);

		return $this->fetch();
	}
	/*
		* 插入管理员组 insertGroup
	*/
	public function insertGroup() {
		// $g = M('auth_group');
		$data['title'] = input('title');
		if (!empty($data)) {
			$rst = Db::name('auth_group')->insert($data);
			if ($rst) {
				$this->ajaxReturn(1);
			} else {
				$this->ajaxReturn(0);
			}
		} else {
			$this->ajaxReturn(0);
		}
	}
	/*
		*	删除管理员组 delGroup
	*/
	public function delGroup() {
		$id = input('id');
		if ($id == 1) {
			echo 0;
		}
		if ($id) {
			// $g = M('auth_group');
			$rst = Db::name('auth_group')->where('id', '=', $id)->delete();
			if ($rst) {
				echo 1;
			} else {
				echo 0;
			}
		} else {
			echo 0;
		}
	}
	/*
		*	分配权限
	*/
	Public function role() {
		// 分配左侧菜单样式变量
		$this->assign('admin', 'AdminUser');
		//查询该用户的权限
		$id = input('gid');
		$rs = Db::name('auth_group')->field('rules')->find($id);
		$rs = explode(',', $rs['rules']);
		//查询规则表，将规则遍历出来
		$role = Db::name('auth_rule')->order('name asc')->select();

		if ($role) {
			foreach ($role as &$v) {
				//判断是否有该权限，有的话加一个选中状态
				if (in_array($v['id'], $rs)) {
					$v['chk'] = 'checked';
				} else {
					$v['chk'] = '';
				}
			}
			$this->assign('list', $role);
		}
		return $this->fetch();
	}

	/*
		*	管理员组编辑权限
	*/
	Public function edit() {
		$this->assign('admin', 'AdminUser');
		$data['id'] = input('id');
		$data['rules'] = implode(',', input('ids/a'));

		if (!$data['id'] && !$data['rules']) {
			$this->error('参数错误！');
		}
		$rst = Db::name('auth_group')->where('id', '=', $data['id'])->update($data);
		if ($rst) {
			$this->success('编辑成功！');
		} else {
			$this->error('编辑失败！');
		}
	}

	/*
		*	删除规则
	*/
	public function delRule() {

		$rst = Db::name('auth_rule')->where('id', '=', input('id'))->delete();
		if ($rst) {
			echo 1;
		} else {
			echo 0;
		}
	}

	/*
		*	删除多个规则
	*/
	public function delRuleAll() {
		$ids = input('id/a');
		$rst = Db::name('auth_rule')->where('id', 'in', $ids)->delete();
		if ($rst) {
			echo 1;
		} else {
			echo 0;
		}

	}

	/*
		*	编辑规则
	*/
	public function editRule() {
		$this->assign('menuList', 'AdminUserrule');
		$id = input('id');
		if ($id) {
			$rule = Db::name('auth_rule')->where('id=' . $id)->find();
			$name = explode('/', $rule['name']);
			$rule['controller'] = $name[0];
			$rule['action'] = $name[1];
			$this->assign('rule', $rule);
		} else {
			$this->error('非法操作！');
		}
		return $this->fetch();
	}
	/*
		*	编辑规则
	*/
	public function saveRule() {
		$data = input('post.');
		$data['name'] = input('controller') . '/' . input('action');
		$rst = Db::name('auth_rule')->where('id', '=', $data['id'])->strict(false)->data($data)->update();
		if ($rst) {

			echo 1;
		} else {
			echo 0;
		}

	}
}