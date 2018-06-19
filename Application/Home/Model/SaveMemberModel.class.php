<?php
namespace Home\Model;
use Think\Model;
class SaveMemberModel extends Model{
	protected $tableName = "member";
	protected $_validate = array(
		array('surname','require','姓必须！'),
		array('name','require','名字必须！'),
		array('country','require','国家必须！'), // 验证确认密码是否和密码一致
		array('email','require','邮箱必须！'), // 在新增的时候验证name字段是否唯一
		array('email','email','邮箱格式不正确！'),
		array('code','require','认证号必须！'),
	);
	protected $_auto = array(
		array("id","getId",3,"callback"),
	);
	function getId(){
		$Member = session("MemberID");
		return $Member['id'];
	}
}
?>