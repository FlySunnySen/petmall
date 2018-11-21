<?php
namespace app\admin\controller;
use think\Db;
use app\admin\model\GoodImages as GoodImageModel;
class GoodImages extends Common
{
	
	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList',$request->module().'-'.$request->action());
		$this->assign('menu','Goods');
	}
	
	// 相册列表
	public function index(){
		if(!empty(request()->isGet('id'))) {
			
			$map['good_id'] = request()->param('id');
			$data = Db::name('good_images') ->where($map)->select();
			$this->assign('goods',$data);	
				
			return $this->fetch();
			
		}else{			
			$this->error('非法操作 ');			
		}
	}

	// 添加页面
	public function add() {
		if(!empty(request()->isGet('id'))){

			//查询所属商品名称		
			$good = Db::name('good')->find(request()->param('id'));
			$data['goods_name'] = $good['goods_name'];
			$data['goods_id'] = request()->param('id');
			$this->assign('goods',$data);
			return $this->fetch();
		}
	}

	// 执行添加
	public function insert() {	
		if(request()->isPost()) {	
			
			$data = $_POST['image_url'] = trim($_POST['image_url']);
            $id = request()->param('good_id');
			//判断是否多个图像一起上传
			if(substr_count($data,' ') != 0) { 
				$img = explode(' ',$_POST['image_url']);	
										
				foreach($img as $v){
					$GoodImageModel = new GoodImageModel();
					$GoodImageModel->good_id = $id;
					$GoodImageModel->image_url = $v;				
				    $info = $GoodImageModel->save();														
				}        
			}else{
				$GoodImageModel = new GoodImageModel();
				$GoodImageModel->good_id = $id;
				$GoodImageModel->image_url = $data;				
			    $GoodImageModel->save();		
			}
			$this->success('添加成功',url('Admin/GoodImages/index',array('id'=>$id)));
		}	
	}

	// 删除
	public function delete() {		
		$map['img_id'] = array('eq',$_POST['id']);
		
		if(Db::name('good_images')-> where($map) -> delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
}
?>