<?php
namespace Home\Controller;
use Think\Controller;
class MailController extends CommonController {
	
	public function index(){
		$type = I("type",0,"int");
		if($type==0){
			$this->error("验证类型不能未空");
		}
		if(!session("MemberID")){
			$this->error("用户未处于登陆状态！");
		}
		$number = mt_rand(100000,999999);
		$content = C("DEFAULT_EMAIL_CONTENT_EMAIL");
		$title = C("DEFAULT_EMAIL_TITLE_EMAIL");
		$Member = session("MemberID");
		if($type==1){
			$typeName="更改交易密码";
		}else if($type==2){
			$typeName="更改登陆密码";
		}else if($type==3){
			$typeName="修改基本信息";
		}else if($type==4){
			$typeName="查询交易密码";
		}else if($type==5){
			$typeName="设置交易中心密码";
		}
		if(M("Verify")->data(array("number"=>$number,"addtime"=>date("Y-m-d H:i:s",time()),"member_id"=>$Member['id'],"type"=>I("type")))->add()){
			$content=$content." ".$number;
			$result = think_send_mail($Member['email'],$typeName,$title,$content);
			if($result){
				$this->AjaxReturn(true);
			}else{
				$this->AjaxReturn(false);
			}
		}else{
			$this->AjaxReturn(false);
		}
	}
	public function findPass(){
		if(IS_POST){
			$Member = session("MemberID");
			$Verify = M("Verify")->where(array("delete"=>0,"member_id"=>$Member['id'],"type"=>4))->order("id desc")->limit(1)->select();
			$time = date("Y-m-d H:i:s",time()-15*60);
			if($Verify[0]['addtime']<$time){
				$this->error("认证码已过期 15分钟有效");
			}
			if(I("code")!=$Verify[0]['number']){
				$this->error("认证码不正确");
			}
			if(I("username")!=$Member['username']){
				$this->error("登陆账号验证不正确");
			}
			if(I("email")!=$Member['email']){
				$this->error("邮箱验证不正确");
			}
			$content = C("DEFAULT_EMAIL_CONTENT_EMAIL");
			$title = C("DEFAULT_EMAIL_TITLE_EMAIL");
			$result = think_send_mail($Member['email'],"","$title","$content <br/>交易密码<br/> 您的交易密码为：".$Member['paypass'] );
			if($result){
				$this->success("交易密码已发送邮箱",U("Home/Main/editprofile"),5);
			}else{
				$this->error("交易密码查询失败",U("Home/Main/editprofile"));
			}
		}else{
			$this->error("信息错误",U("Home/Main/editprofile"));
		}

	}
}