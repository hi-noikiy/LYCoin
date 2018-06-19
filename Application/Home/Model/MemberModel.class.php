<?php
namespace Home\Model;
use Think\Model;
class MemberModel extends Model{
	protected $tableName = "member";
	protected $_validate = array(
		array('username','require','用户名必须！'),
		array('username','checkUserName','用户名长度请大于4个字符！',1,'function',0),
		array('userpass','require','密码必须！'),
		array('userpass','checkUserPass','密码长度请大于4个字符！',1,'function',0),
		array('repass','userpass','登陆密码不一致',0,'confirm'), // 验证确认密码是否和密码一致
		array('username','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一	
		array('surname','require','姓必须！'),
		array('name','require','名字必须！'),
		array('country','require','国家必须！'),
		array('p_id','require','推荐人用户名必须！'),
		array('p_name','require','推荐人姓名必须！'),
		array('package_type','require','套餐必须！'),
		array('package_type','number','套餐类型不正确！'),
		array('r_id','require','赞助人用户名必须！'),
		array('position','require','赞助人位置必须！'),
		array('position',array(1,0),'位置范围不正确！',2,'in'),
		array('sex',array(1,0),'性别范围不正确！',2,'in'),
		array('status',array(1,0),'是否开启范围不正确！',2,'in'),		
	);
	protected $_auto = array(
		array('userpass','md5',3,'function') ,
		array('paypass','getPayPass',3,'callback'),
		array('addtime','getTime',3,'callback'),
		array('adddate','getDate',3,'callback'),
		array('status_login',1),//第一次登陆验证  1表是 第一次
	);
	function getTime(){
		return date("Y-m-d H:s:i",time());
	}
	function getDate(){
		return date("Y-m-d",time());
	}
	function getPayPass(){
		return I("paypass");
		//return I("paypass","","md5");
	}
	function checkUserName(){
		if(strlen(I("username"))>4){
			return true;
		}else{
			return false;
		}
	}
	function checkUserPass(){
		if(strlen(I("userpass"))>4){
			return true;
		}else{
			return false;
		}
	}
}
?>