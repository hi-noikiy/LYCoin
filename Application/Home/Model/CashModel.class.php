<?php
namespace Home\Model;
use Think\Model;
class CashModel extends Model{

	protected $_validate = array(
			array('username','require','用户名必须！'),
			array('type','require','货币类型必须！'),
			array('price','require','货币必须！'),
			array('price','currency','货币必须是小数！'),
			array('bank','require','银行必须！'),
	);
	protected $_auto = array(
			array('addtime','getTime',3,'callback'),
			array('adddate','getDate',3,'callback'),
			array('member_id','getMember',3,'callback'),
			array('member_username','getMemberName',3,'callback'),
			array('title','Cash 存款'),
	);
	function getTime(){
		return date("Y-m-d H:s:i",time());
	}
	function getDate(){
		return date("Y-m-d",time());
	}
	function getMemberName(){
		$member = session("MemberID");
		return $member['username'];
	}
	function getMember(){
		$member = session("MemberID");
		return $member['id'];
	}
}
?>