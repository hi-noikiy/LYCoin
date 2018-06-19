<?php
namespace Home\Controller;
use Think\Controller;
class LockController extends Controller {
	
	public function lock(){
		$language = I("language");
		if($language==""){
			$language = C("DEFAULT_LANG");
		}
		$this->language = $language;
		if(SESSION("MemberID")){
			$Member = SESSION("MemberID");
			if(IS_POST){
				$SetMember = D("SetMember");
				$_POST["id"] = $Member['id'];
				if (!$SetMember->create()){
					$this->error($SetMember->getError(),U('Home/Lock/lock',array("language"=>$language)));
				}else{
					$result = $SetMember->save();
					if($result||$result==0){
						M("Member")->where(array("id"=>$Member['id']))->data(array("status_login"=>0))->save();
						$this->redirect('Home/Main/index',array("language"=>$language), 0, '页面跳转中...');
					}else{
						$this->error("保存错误");
					}

				}
			}else{
				$this->Member = $Member;
				$this->display();
			}

		}else{
			$this->error("系统错误!",U("Home/Index/home",array("language"=>$language)));
		}
	}
}