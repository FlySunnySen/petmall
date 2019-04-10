<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Common extends Controller {

	public function _initialize() {
		session_start();
		self::checkLogin();
	}

	public function checkLogin() {

		$controller = request()->controller();
		if (empty($_SESSION['user'])) {
			$allowController = ['Index', 'Login', 'Good'];
			// die;
			if (!in_array($controller, $allowController)) {
				if (isset($_COOKIE['user_email']) && isset($_COOKIE['user_pwd'])) {
					$map['user_email'] = $_COOKIE['user_email'];
					$map['user_pwd'] = md5($_COOKIE['user_pwd']);
					$rst = Db::name('user')
						->alias('a')
						->join('user_details b', 'a.Uid = b.user_Uid')
						->where($map)
						->find();
					if ($rst) {
						$_SESSION['alias'] = $rst['user_alias'];
						$_SESSION['user'] = $rst['user_email'];
						$_SESSION['uid'] = $rst['Uid'];
						$this->success('登录成功', 'index/index');
					}
				}
				$this->error('请先登录', 'login/login');

			}
		}

	}

	public function myUpload(Request $request) {
		//获取当前控制器的名字
		$file_Catalog = strtolower($request->controller());
		//获取表单上传文件
		$file = $request->file('file');
		if (empty($file)) {
			$this->error('请选择上传文件');
		}

		$info = $file->validate(['ext' => 'jpg,png'])->move(ROOT_PATH . 'public' . DS . 'upload' . DS . $file_Catalog);

		$path = '/upload/' . $file_Catalog . DS . $info->getSaveName();
		$path = str_replace('\\', '/', $path);
		if ($info) {
			return $path;
		} else {
			exit;
		}
	}

}
?>