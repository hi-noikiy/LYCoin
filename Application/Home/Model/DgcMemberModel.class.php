<?php
namespace Home\Model;
use Think\Model;
class DgcMemberModel extends Model{
	protected $tableName = "member";
	protected $_validate = array(
		array('password','require','交易中心密码必须！'),
		array('repassword','require','确认密码必须！'), // 验证确认密码是否和密码一致
		array('password','repassword','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
		array('code','require','认证号必须！'),
	);
	protected $_auto = array(
		array("id","getId",3,"callback"),
		array('dgcpass','getPayPass',3,'callback') ,
	);
	function getId(){
		$Member = session("MemberID");
		return $Member['id'];
	}
	function getPayPass(){
		return I("password","");
		//return I("newpaypass","","md5");
	}
}
?>