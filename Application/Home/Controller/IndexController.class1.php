<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function _initialize(){
		$language = I("language","");
		if($language==""){
			$language = C("DEFAULT_LANG");
		}
		$this->language = $language;
	}
	public function index(){
		if(SESSION("MemberID")){
			$this->msg = 1;
		}
		$this->display();
	}
	public function login(){
		if(IS_POST){
			$username = I("username");
			$userpass = I("userpass","0","md5");
			$Member = M("Member")->where(array("username"=>$username,"userpass"=>$userpass))->find();
			if($Member){
				if($Member['status']==0){
					$this->error("账户未开启！",U("Home/Index/login",array("language"=>I("language"))));
				}
				SESSION("MemberIP",get_client_ip());
				//查询 Digit 数据
				SESSION("MemberID",$Member);
				$find = array("member_id"=>$Member['id']);
				$find['_string'] = "type = 1 or type = 3";
				$Digit = M("DigitCoinInfo")->where($find)->limit(1)->order("id desc")->select();
				if(count($Digit)>0){
					//解冻百分比 和 解冻设置 获取
					$COLD_DIGIT_TO_FREE = (float)C("DEFAULT_WEB_COLD_DIGIT_TO_FREE");
					$COLD_DIGIT_DAY = C("DEFAULT_WEB_COLD_DIGIT_DAY");
					if($COLD_DIGIT_TO_FREE>=1||$COLD_DIGIT_TO_FREE<=0){
						$this->error("设置错误！非小数百分比");
					}
					if($Digit[0]['cold_digit_coin']<=0){
						$this->redirect("Home/Main/index", array("language"=>I("language")), 0, '页面跳转中...');
					}
					//每日解冻 金额
					$MinDigitCoin = $Digit[0]['cold_digit_coin']*$COLD_DIGIT_TO_FREE;
					//获取 最新解冻情况
					if($DigitCoinInfo = M("DigitCoinInfo")->where(array("type"=>2,"member_id"=>$Member['id']))->limit(1)->order("id desc")->select()){
						if($Digit['adddate']>=$DigitCoinInfo['adddate']){
							//剩余 待解冻金额
							$price = $Digit[0]['cold_digit_coin'];
							//剩余 已解冻金额
							$free_price = $DigitCoinInfo[0]['free_digit_coin'];
						}else{
							//剩余 待解冻金额
							$price = $DigitCoinInfo[0]['cold_digit_coin'];
							//剩余 已解冻金额
							$free_price = $DigitCoinInfo[0]['free_digit_coin'];
						}

					}else{
						$DigitCoinInfo = $Digit;
						//剩余 待解冻金额
						$price = $Digit[0]['cold_digit_coin'];
						//剩余 已解冻金额
						$free_price = $Digit[0]['free_digit_coin'];
					}
					for($i = strtotime($DigitCoinInfo[0]['adddate']); $i <= strtotime(date("y-m-d",time())); $i += 86400){
						$ThisDate=date("Y-m-d",$i);
						if($ThisDate>$DigitCoinInfo[0]['adddate']){
							if($COLD_DIGIT_DAY==1){
								if((date('w',strtotime($ThisDate))!=6)&&(date('w',strtotime($ThisDate))!=0)){
									$price = $price-$MinDigitCoin;
									if($price<=0){
										$price = 0;
									}
									$free_price = $free_price+$MinDigitCoin;
									$map = array(
											"type"=>2,
											"member_username"=>$Member['username'],
											"member_id"=>$Member['id'],
											"cold_digit_coin"=>$price,
											"cold_out_digit_coin"=>$MinDigitCoin,
											"free_digit_coin"=>$free_price,
											"free_in_digit_coin"=>$MinDigitCoin,
											"title"=>"Escrowcoin changed state freecoin",
											"adddate"=>$ThisDate,
											"addtime"=>$ThisDate,
									);
									if($resultMap = M("DigitCoinInfo")->data($map)->add()){
										$Split = M("Split")->where(array("adddate"=>$ThisDate))->find();
										if($Split){
											$data = array(
													'type'=>3,
													"member_username"=>$Member['username'],
													"member_id"=>$Member['id'],
													'cold_digit_coin'=>$price*2,
													'cold_out_digit_coin'=>0,
													'cold_in_digit_coin'=>$price,
													'free_digit_coin'=>$free_price*2,
													'free_in_digit_coin'=>$MinDigitCoin,
													'title' =>"Split Limincoin(Escrow)",
													'remark'=>$Split['remark'],
													'adddate' =>$ThisDate,
													'addtime' =>$ThisDate
											);
											if(M("DigitCoinInfo")->data($data)->add()){
												$Digit = M("DigitCoinInfo")->where(array("type"=>2,"member_id"=>$Member['id']))->limit(1)->order("id desc")->select();
												if(count($Digit)>0){
													$Digit[0]["cold_digit_coin"] = $Digit[0]['cold_digit_coin']*2;
													M("DigitCoinInfo")->data($Digit)->save();
												}
												$MinDigitCoin = $price*2*$COLD_DIGIT_TO_FREE;
												$price = $price*2;
												$free_price = $free_price*2;
											}
										}										
									}
									if($price<=0){
										break;
									}
								}
							}else{
								$price = $price-$MinDigitCoin;
								if($price<0){
									$price = 0;
								}
								$free_price = $free_price+$MinDigitCoin;
								$map = array(
										"type"=>2,
										"member_username"=>$Member['username'],
										"member_id"=>$Member['id'],
										"cold_digit_coin"=>$price,
										"cold_out_digit_coin"=>$MinDigitCoin,
										"free_digit_coin"=>$free_price,
										"free_in_digit_coin"=>$MinDigitCoin,
										"title"=>"Escrowcoin changed state freecoin",
										"adddate"=>$ThisDate,
										"addtime"=>$ThisDate,
								);
								if($resultMap = M("DigitCoinInfo")->data($map)->add()){
									$Split = M("Split")->where(array("adddate"=>$ThisDate))->find();
									if($Split){
										$data = array(
												'type'=>3,
												"member_username"=>$Member['username'],
												"member_id"=>$Member['id'],
												'cold_digit_coin'=>$price*2,
												'cold_out_digit_coin'=>0,
												'cold_in_digit_coin'=>$price,
												'free_digit_coin'=>$free_price*2,
												'free_in_digit_coin'=>$MinDigitCoin,
												'title' =>"Split Limincoin(Escrow)",
												'remark'=>$Split['remark'],
												'adddate' =>$ThisDate,
												'addtime' =>$ThisDate
										);
										if(M("DigitCoinInfo")->data($data)->add()){
											$Digit = M("DigitCoinInfo")->where(array("type"=>2,"member_id"=>$Member['id']))->limit(1)->order("id desc")->select();
											if(count($Digit)>0){
												$Digit[0]["cold_digit_coin"] = $Digit[0]['cold_digit_coin']*2;
												M("DigitCoinInfo")->data($Digit)->save();
											}
											$MinDigitCoin = $price*2*$COLD_DIGIT_TO_FREE;
											$price = $price*2;
											$free_price = $free_price*2;
										}
									}									
								}
								if($price<=0){
									break;
								}
							}
						}
					}

				}
				M("MemberLoginInfo")->data(array("member_id"=>$Member['id'],"login_ip"=>get_client_ip(),"login_time"=>date("Y-m-d H:i:s",time())))->add();
				$this->redirect("Home/Main/index", array("language"=>I("language")), 0, '页面跳转中...');
			}else{
				$this->error("登陆错误");
			}
		}else{
			$this->Url = "http://".$_SERVER['SERVER_NAME'];
			$this->language = I("language");
			$this->display();
		}

	}
	public function out(){
		SESSION("Notice",null);
		SESSION("MemberID",null);
		$this->success("success",U("Home/Index/index"));
	}
	public function repass(){
		if(IS_POST){
			$user_name = I("user_name");
			$user_email = I("user_email");
			if($user_name==""||$user_email==""){
				$this->error("用户名或密码不能为空！");
			}
			if(!$Member = M("Member")->where(array("username"=>$user_name,"email"=>$user_email))->find()){
				$this->error("用户名密码不正确");
			}
			$Verify = M("Verify")->where(array("delete"=>0,"member_id"=>$Member['id'],"type"=>5))->order("id desc")->limit(1)->select();
			$time = date("Y-m-d H:i:s",time()-15*60);
			if($Verify[0]['addtime']<$time){
				$this->error("认证码已过期 15分钟有效");
			}
			if(I("code")!=$Verify[0]['number']){
				$this->error("认证码不正确");
			}
			$number = mt_rand(100000,999999);
			$data = array(
					'userpass' => md5($number),
			);
			$re = M("Member")->where(array("id"=>$Member['id']))->data($data)->save();
			if($re||$re==0){
				M("Verify")->where(array("id"=>$Verify[0]['id']))->data(array("delete"=>1))->save();
				$content = C("DEFAULT_EMAIL_CONTENT_EMAIL");
				$result = think_send_mail($Member['email'],"","自动生成新密码","$content <br/>自动生成新密码<br/> 密码：$number ");
				if($result){
					$this->success("找回成功",U('Home/Index/login'));
				}else{
					$this->error("找回失败",U('Home/Index/login'));
				}
			}else{
				$this->error("重置失败",U('Home/Index/login'));
			}

		}else{
			$this->error("找回错误");
		}
	}
	public function send_email(){
		$type = I("type",0,"int");
		$user_name = I("user_name");
		$user_email = I("user_email");
		if($type==0){
			$this->AjaxReturn(false);
		}
		if($type==5){
			$typeName="更改登陆密码";
		}else{
			$this->AjaxReturn(false);
		}
		$number = mt_rand(100000,999999);
		$content = C("DEFAULT_EMAIL_CONTENT_EMAIL");
		$title = C("DEFAULT_EMAIL_TITLE_EMAIL");
		if(!$Member = M("Member")->where(array("username"=>$user_name,"email"=>$user_email))->find()){
			$this->AjaxReturn(false);
		}
		if(M("Verify")->data(array("number"=>$number,"addtime"=>date("Y-m-d H:i:s",time()),"member_id"=>$Member['id'],"type"=>I("type")))->add()){
			$result = think_send_mail($Member['email'],"","$title","$content <br/>$typeName<br/> 验证码：$number ");
			if($result){
				$this->AjaxReturn(true);
			}else{
				$this->AjaxReturn(false);
			}
		}else{
			$this->AjaxReturn(false);
		}
	}
}