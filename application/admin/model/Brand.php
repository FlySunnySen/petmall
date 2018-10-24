<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Brand extends Model
{
	
	protected $field = true;
	
	public function good()
	{
		return $this->hasMany('good');
	}
	
}
?>