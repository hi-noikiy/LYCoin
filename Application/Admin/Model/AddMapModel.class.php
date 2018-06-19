<?php
namespace Admin\Model;
use Think\Model;
class AddMapModel extends Model{
	protected $tableName = "server_map";
	protected $_validate = array(
		array('Open','require','Open必须！'),
		array('High','require','High必须！'),
		array('Low','require','Low必须！'),
		array('Close','require','Close必须！'),
		array('Volume','require','Volume必须！'),
		array('status',array(1,0),'是否开启范围不正确！',2,'in'),		
	);
	protected $_auto = array(
		array('addtime','getTime',3,'callback'),
		array('adddate','getDate',3,'callback'),
	);
	function getTime(){
		return date("Y-m-d H:s:i",time());
	}
	function getDate(){
		return date("Y-m-d",time());
	}
}
?>