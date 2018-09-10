<?php
namespace app\admin\controller;
use think\Controller;
class Common extends Controller
{
	
	/**
	 * 无限极分类
	 * @param   $list array()
	 * return array
	 */
	//无限极分类，实现具有父子关系的数据分类
	function category($arr,$pid=0,$level=0){
	    //定义一个静态变量，存储一个空数组，用静态变量，是因为静态变量不会被销毁，会保存之前保留的值，普通变量在函数结束时，会死亡，生长周期函数开始到函数结束，再次调用重新开始生长
	    //保存一个空数组
	    static $list=array();
	    //通过遍历查找是否属于顶级父类，pid=0为顶级父类，
	    foreach($arr as $value){
	        //进行判断如果pid=0，那么为顶级父类，放入定义的空数组里
	        if($value['pid']==$pid){
	            //添加空格进行分层
	            $arr['level']=$level;
	            $list[]=$value;
	            //递归点，调用自身，把顶级父类的主键id作为父类进行再调用循环，空格+1
	            category($arr,$value['id'],$level+1);
	        }
	    }
	    return $list;//递归出口
	}
	
	//商品无限极分类
	function getTree($data,$pid=0,$level=0){
    static $arr = array();
	    foreach($data as $key => $v){
	        if($pid == $v['pid']){
	            $v['level'] = $level; //分级操作
	            $arr[] = $v;  //将满足的数据存入的空数组里面
	            getTree($v,$v['pid'],$level+1); 
	            //递归查找自己下面是否还有下一级
	        }
	    }
	    return $arr;
	}
	
	//实现递归删除
	function getDel($data,$id){
    static $arr = array();
	    foreach($data as $key => $v){
	        if($id == $v['pid']){
	            $arr[] = $v;
	            getDel($data,$v['id']);
	        }
	    }
	    return $arr;
	}
	
}
?>