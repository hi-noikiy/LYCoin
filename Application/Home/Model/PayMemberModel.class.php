<?php
namespace Home\Model;
use Think\Model;
class PayMemberModel extends Model{
	protected $tableName = "member";
	protected $_validate = array(
		array('paypass','require','原交易密码必须！'),
		array('newpaypass','require','新交易密码必须！'),
		array('repaypass','require','确认密码必须！'), // 验证确认密码是否和密码一致
		array('newpaypass','repaypass','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
	);
	protected $_auto = array(
		array("id","getId",3,"callback"),
		array('paypass','getPayPass',3,'callback') ,
	);
	function getId(){
		$Member = session("MemberID");
		return $Member['id'];
	}
	function getPayPass(){
//		return I("newpaypass","");
		return I("newpaypass","","md5");
	}
}
?>