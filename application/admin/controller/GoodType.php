<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\admin\model\GoodType as category;
class GoodType extends Controller
{
	
	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList',$request->controller().'-'.$request->action());
		$this->assign('menu','Goods');
	}
	
	
	public function index()
	{
		$category = new category();
		$data = $category->select();
		$data = collection($data)->toArray();
		//获取父id的名称		
		foreach($data as $k=>$v){			
			if($v['pid']==0){			
				$data[$k]['p_name']="顶级分类";
			}else{
				$data[$k]['p_name'] = $category->where('id',$v['pid'])->value('type_name');
			}
		}	
		$this->assign('type',$data);
		$this->assign('page',111);
		return $this->fetch();
	}
	
	
	public function add(){
		
	}
	
	
	public function addEditCategory()
	{
		$request = Request::instance();
		
	    if($request->isPost()){
	    	$category = new category();
			$category->data($request->param());
			$count =$category->allowField(true)->save();		
			if($count){				
				$this->success('添加成功','index');
			}else{
				$this->error('添加失败');
			}	
			exit;
	    }	
		
		if($request->isGet()){
			echo 'hhh';
			exit;
		}
			
		$data = model('good_type')->getCategory();			
		$this->assign('type',$data);
		return $this->fetch("add");
	}
}
?>