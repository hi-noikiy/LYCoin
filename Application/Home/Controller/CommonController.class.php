<?php

namespace Home\Controller;

use Think\Controller;

class CommonController extends Controller {

        public function _initialize() {
                $language = I("language");
                if ($language == "") {
                        $language = C("DEFAULT_LANG");
                }
                if (SESSION("MemberID")) {
                        $Member = SESSION("MemberID");
                        $where = array("id" => $Member['id'], "username" => $Member['username'], "userpass" => $Member['userpass']);
                        $result = M("Member")->where($where)->find();
//                         echo "<pre>";
//                                var_export($result);
//                                echo "</pre>";exit;
                        if (!$result) {
                                SESSION("MemberID", null);
                                $this->error(L("qingchongxindenglu"), U("Home/Index/login", array("language" => $language)));
                        } else {
//                                echo 1111;exit;
                                SESSION("MemberID", $result);
//                                echo "<pre>";
//                                var_export(ACTION_NAME);
//                                echo "</pre>";exit;
                                if ($result['emailstatus']==0 && ACTION_NAME != 'editprofile' && ACTION_NAME != 'saveEditprofile') {
                                        $this->redirect('Home/Main/editprofile', array("language" => $language));
                                }
//				if($result['status_login']==1){
//					$this->redirect('Home/Lock/lock',array("language"=>$language),0,"");
//				}
                        }
//                        $MemberLoginInfo = M("MemberLoginInfo")->where(array("member_id" => $Member['id']))->order("id desc")->limit(1)->select();
//                        if (count($MemberLoginInfo) > 0) {
//                                if (SESSION("MemberIP") != $MemberLoginInfo[0]["login_ip"]) {
//                                        SESSION("MemberID", null);
//                                        $this->error(L("yididenglu"), U("Home/Index/login", array("language" => $language)));
//                                }
//                        } else {
//                                SESSION("MemberID", null);
//                                $this->error(L("qingchongxindenglu"), U("Home/Index/login", array("language" => $language)));
//                        }
                } else {
                        $this->error('请登录', U("Home/Index/login", array("language" => $language)));
                }
                $settings = settings();

                $this->assign('settings', $settings);
                $this->assign('member', $Member);
                $_POST["language"] = $language;
                $this->User = session("MemberID");
                $this->Action = ACTION_NAME;
                $this->Url = "http://" . $_SERVER['SERVER_NAME'];
                $this->language = $language;
                if (SESSION("Notice")) {
                        $this->Notice = SESSION("Notice");
                }
        }

}
