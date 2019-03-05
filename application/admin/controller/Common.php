<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Common extends Controller {

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