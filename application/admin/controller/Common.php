<?php
namespace app\admin\controller;
use think\Controller;
use think\Page;
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

	public function ajaxReturn($data, $type = 'json') {
		exit(json_encode($data));
	}

	/*
		    * 分页方法直接调用
		    * $count 总页数  $pagenumber每页的分页数
		    * 返回分页对象
	*/
	Public function mypage($count, $pagenumber) {
		$Page = new Page($count, $pagenumber); //实例化分页类记录数和每页显示的记录数

		// 修改分页样式
		$Page->setConfig('header', '共%TOTAL_ROW%条数据，当前第%NOW_PAGE%/%TOTAL_PAGE%页');
		$Page->setConfig('prev', '上一页');
		$Page->setConfig('first', '首页');
		$Page->setConfig('last', '尾页');
		$Page->setConfig('next', '下一页');
		$Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE%  %LINK_PAGE%  %DOWN_PAGE% %END%');

		// 返回分页对象
		return $Page;
	}

}
?>