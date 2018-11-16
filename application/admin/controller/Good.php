<?php

namespace app\admin\controller;
use think\Controller;
use think\Db;
use app\admin\model\Good as GoodModel;


class Good extends Common{
	
	public function _initialize() {
		parent::_initialize();
		$request = request();
		$this->assign('menuList',$request->module().'-'.$request->module());
		$this->assign('menu','Goods');
	}
	
	
	public function index()
	{
		$status['is_delete'] = 0; 
		$good_data = Db::view('good')
		                 ->view('good_type','type_name','good.type_id = good_type.id')
						 ->view('brand','brand_name','brand.id=good.brand_id')
						 ->where($status)
						 ->paginate(10); 
						
		$this->assign('good_data',$good_data);
		
         return $this->fetch();
    }
	
	//添加商品
	public function addEditGood()
	{
	    
		if(request()->isPost()){
			$GoodModel = new GoodModel();
			$GoodModel->data(request()->param());
			if(request()->param('id')){
				$count = $GoodModel->isUpdate(true)->allowField(true)->save();
				$info = '修改成功';
			}else{
				$data = request()->param();
				$data['sn'] = time().rand(1000, 10000);
				$data['goods_time'] = time();			
				$count = $GoodModel->isUpdate(false)->allowField(true)->save($data);
				$info = '添加成功';
			}
						
			
			if($count){
				$this->success($info,'index');
			}else{
				exit;
			}
		}	
		
		$data = model('good_type')->getCategory();			
		$brand = Db::name('brand')->select();		 	
		$this->assign('brand',$brand);
		$this->assign('type',$data);	
		
		
		if(request()->param('id')){
		
			$good_data = Db::view('good')
		                 ->view('good_type','type_name','good.type_id = good_type.id')
						 ->view('brand','brand_name','brand.id=good.brand_id')
						 ->where('good.id',request()->param('id'))
						 ->find(); 		
		   
			$this->assign('iddata',$good_data);
			return $this->fetch("edit");
			exit;
		}
	
	    
	
		
		
		
        return $this->fetch('add');
		
	}
    
	//删除商品
	public function softDelGood()
	{
		if(request()->isGet('id')){
			$id = request()->param('id');
		}	
		$data = GoodModel::get($id);
		$data->is_delete = 1;
		
		if(false !== $data->save()){
			
			return $this->success('删除成功');;
		}else{
			
			
			$this->success('删除失败');
		}		
	}
	
	
	
	public function softDelMore()
	{
		 if(request()->isAjax()){
		 	
            $good_id = $_POST['id'];
			$info = "";
			foreach($good_id as $v){				
				
				$data = GoodModel::get($v);
				$data->is_delete = 1;
				
				if(false !== $data->save()){
					
					continue;
					
				}else{
					
					$info.=$data->goods_name.' ';
					
				}						
				
			}
			if(empty($info)){
				$info = '删除成功';
			}else{
				$info.='无法删除，其他选中的商品已删除';
			}
			return $info;
		}
	}
   
    
	//商品回收站
	public function recycle()
	{
		$status['is_delete'] = 1; 
		$good_data = Db::view('good')
		                 ->view('good_type','type_name','good.type_id = good_type.id')
						 ->view('brand','brand_name','brand.id=good.brand_id')
						 ->where($status)
						 ->paginate(10); 
				
		$this->assign('good_data',$good_data);
		
         return $this->fetch();
	}
	
	//商品还原
	public function recycleGood()
	{
		if(request()->isGet('id')){
			$id = request()->param('id');
		}	
		$data = GoodModel::get($id);
		$data->is_delete = 0;
		
		if(false !== $data->save()){
			
			return $this->success('还原成功');;
		}else{
			
			
			$this->success('还原失败');
		}		
	}
	
	//批量还原
	public function recycleAll()
	{
		 if(request()->isAjax()){
		 	
            $good_id = $_POST['id'];
			$info = "";
			foreach($good_id as $v){				
				
				$data = GoodModel::get($v);
				$data->is_delete = 0;
				
				if(false !== $data->save()){
					
					continue;
					
				}else{
					
					$info.=$data->goods_name.' ';
					
				}						
				
			}
			if(empty($info)){
				$info = '还原成功';
			}else{
				$info.='无法还原，其他选中的商品已还原';
			}
			return $info;
		}
	}
	
	//商品硬删除
	public function delGood()
	{
		if(request()->isGet('id')){
			$id = request()->param('id');
		}	
		
		$count = model('good')->where('id',$id)->delete();
		
		if($count){
			
			return $this->success('删除成功');;
		}else{
			
			
			$this->success('删除失败');
		}		
	}
	
	//批量硬删除
	public function delAll()
	{
		if(request()->isAjax()){
			  $good_id = $_POST['id'];
			$data = "";
			foreach($good_id as $v){				
				
				$count = model('good')->where('id',$v)->delete();
				if($count){
					continue;
				}else{
					$data.= model('good')->where('id',$v)->value('good_name').' ';
				}
			}
			if(empty($data)){
				$data = '删除成功';
			}else{
				$data.='删除失败，其他选中的商品已删除';
			}
			return $data;
		}
	}

}
?>