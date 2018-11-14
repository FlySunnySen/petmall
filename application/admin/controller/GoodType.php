<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\admin\model\GoodType as category;
class GoodType extends Common
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
		//获取商品分类
		$data = Db::name('good_type')->paginate(10,false,['query'=>request()->param()])->each(function($item){
			   //修改结果集
               $pid = $item['pid']; 
               if($item['pid'] == 0){
               	 $item['p_name'] = "顶级分类";
               }else{
               	 $category = new category();
               	 $item['p_name'] = $category->where('id',$item['pid'])->value('type_name');

               }
           
            return $item;
        
          
		});
		
	
		$this->assign('type',$data);	
		return $this->fetch();
	}
	
	//删除分类
	public function deleteCategory($id)
	{
		
		if(request()->isGet('id')){
			$id = request()->param('id');
		}	
		$count = model('good_type')->where('pid',$id)->select();
		if($count){
			
			return $this->error('该分类存在子类');
		}else{
			$destroy = model('good_type')->where('id',$id)->delete();
			
			$this->success('删除成功');
		}		
	}
	
	public function delMore()
	{
		 if(request()->isAjax()){
		 	
            $good_id = $_POST['id'];
			$data = "";
			foreach($good_id as $v){				
				
				$count = model('good_type')->where('pid',$v)->select();
				if($count){
					$data.= model('good_type')->where('id',$v)->value('type_name').' ';
				}else{
					$del = model('good_type')->where('id',$v)->delete();
				}
			}
			if(empty($data)){
				$data = '删除成功';
			}else{
				$data.='下有子分类，无法删除，其他选中的分类已删除';
			}
			return $data;
		}
	}
	
	public function addEditCategory()
	{
		$request = Request::instance();
		
	    if($request->isPost()){
	    	$category = new category();
			$category->data($request->param());
			
			
			
			$pid = $request->param('pid');
					
			
			//判断是插入还是修改
			if($request->param('id')){				
				$category->parent_id_path = self::makePath($request->param('id'),$pid);					
				$count =$category->isUpdate(true)->allowField(true)->save();
				$info ="修改成功";
				
			}else{
				
				$id = $category->max('id')+1;   //获取分类主键的最大值
				$category->id = $id;
				$category->parent_id_path = self::makePath($id,$pid);
				$count =$category->isUpdate(false)->allowField(true)->save();
				$info = "添加成功";
				
			}
			if($count){				
				$this->success($info,'index');
			}else{
				$this->error('失败');
			}	
			exit;
	    }	
		
		$data = model('good_type')->getCategory();			
		$this->assign('type',$data);
		
		if($request->param('id')){
		
			$iddata = Db::name('good_type')->find($request->param('id'));
			
			//获取父id的名称		
			foreach($iddata as $v){			
				if($iddata['pid']==0){			
					$iddata['pname']="顶级分类";
				}else{
					$iddata['pname'] = model('good_type')->where('id',$iddata['pid'])->value('type_name');
				}
			}			
			$this->assign('iddata',$iddata);
			return $this->fetch("edit");
			exit;
		}
			
		
		return $this->fetch("add");
	}
    //制作家族族谱
    public function makePath($id,$pid){
    			$category = new category();	
			   
				if($pid == 0){
				   
				$parent_id_path = "0_".$id;
							 			  
			    }else{
			    			    	
					$parent_id_path = $category->where('id',$pid)->value('parent_id_path');
					$parent_id_path = $parent_id_path.'_'.$id;
							
			    }
	    return $parent_id_path;
    }
	
}
?>