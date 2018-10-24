<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Good extends Model
{
	
	protected $field = true;
	
	public function brand()
	{
		return $this->belongsTo('brand');
	}
	
	public function goodType()
	{
		return $this->belongsTo('goodType');
	}
}
?>