<?php
namespace Server\Model;
use Think\Model;
class DgcMemberModel extends Model{
	protected $tableName = "member";
	protected $_validate = array(
		array('dgcpass','require','原密码必须！'),
		array('user_pass','require','新密码必须！'),
		array('user_repass','require','确认密码必须！'), // 验证确认密码是否和密码一致
		array('user_pass','user_repass','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
		array('code','require','认证号必须！'),
	);
	protected $_auto = array(
		array("id","getId",3,"callback"),
		array('dgcpass','getUserPass',3,'callback') ,
	);
	function getId(){
		$Member = session("DgcMember");
		return $Member['id'];
	}
	function getUserPass(){
		return I("user_pass");
	}
}
?>