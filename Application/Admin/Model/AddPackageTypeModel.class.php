<?php
namespace Admin\Model;
use Think\Model;
class AddPackageTypeModel extends Model{
	protected $tableName = "package_type";
	protected $_validate = array(
		array('title','require','标题必须！'),
		array('price_low','currency','价格应为金额！'),
		array('price_low','currency','价格应为金额！'),
		array('status',array(1,0),'是否开启范围不正确！',2,'in'),		
	);
}
?>