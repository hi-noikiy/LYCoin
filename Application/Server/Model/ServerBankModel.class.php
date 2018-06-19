<?php
namespace Server\Model;
use Think\Model;
class ServerBankModel extends Model{
	protected $_validate = array(
		array('bank_number','require','银行账号必须！'),
		array('bank_username','require','银行户名必须！'),
//		array('country','require','居住国家必须！'),
		array('bank_name','require','银行名称必须！'),
		array('bank_child_name','require','银行分行名称必须！'),
//		array('bank_country','require','支付宝用户名必须！'),
//		array('bank_address','require','支付宝账号必须！'),
//		array('price_type','require','接收货币必须！'),

	);
	protected $_auto = array(
		array("adddate","getDate",3,"callback"),
		array("addtime","getTime",3,"callback"),
	);
	function getTime(){
		return date("Y-m-d H:i:s",time());
	}
	function getDate(){
		return date("Y-m-d",time());
	}
}
?>