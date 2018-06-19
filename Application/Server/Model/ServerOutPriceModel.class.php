<?php
namespace Server\Model;
use Think\Model;
class ServerOutPriceModel extends Model{
	protected $tableName = "server_out_price_info";
	protected $_validate = array(
		array('price','require','申请金额必须！'),
		array('price','currency','申请金额必须是金额！'),
	);
	protected $_auto = array(
		array("adddate","getDate",3,"callback"),
		array("addtime","getTime",3,"callback"),
		array("member_id","getMemberId",3,"callback"),
		array("member_username","getMemberUsername",3,"callback"),
	);
	function getTime(){
		return date("Y-m-d H:i:s",time());
	}
	function getDate(){
		return date("Y-m-d",time());
	}
	function getMemberUsername(){
		$DgcMember = Session("DgcMember");
		return $DgcMember['username'];
	}
	function getMemberId(){
		$DgcMember = Session("DgcMember");
		return $DgcMember['id'];
	}
}
?>