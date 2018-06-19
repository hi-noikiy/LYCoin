<?php
namespace Home\Model;
use Think\Model;
class SetMemberModel extends Model{
	protected $tableName = "member";
	protected $_validate = array(
		array('paypass','repaypass','支付密码不一致',0,'confirm'), // 验证确认密码是否和密码一致
	);
	protected $_auto = array(
		array('paypass','getPayPass',3,'callback') ,
	);
	function getPayPass(){
		return I("paypass");
		//return I("paypass","","md5");
	}
}
?>