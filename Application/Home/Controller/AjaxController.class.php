<?php

namespace Home\Controller;

use Think\Controller;

class AjaxController extends Controller {

        public function getUsername() {
                $username = I("username");
                $msg = array();
                if (strlen($username) < 4) {
                        $msg["result"] = false;
                        $msg["message"] = L('user-check-length');
                        $this->AjaxReturn($msg);
                }
                $where = array("delete" => 0, "username" => $username);
                $number = M("Member")->Where($where)->count();
                if ($number > 0) {
                        $msg["result"] = false;
                        $msg["message"] = L('user-check-error');
                        $this->AjaxReturn($msg);
                } else {
                        $msg["result"] = true;
                        $msg["message"] = L('user-check-success');
                        $this->AjaxReturn($msg);
                }
        }

        public function getCheckname() {
                $username = I("username");
                $msg = array();
                $where = array("delete" => 0, "username" => $username);
                $number = M("Member")->Where($where)->count();
                if ($number > 0) {
                        $msg["result"] = true;
                        $msg["message"] = "找到用户";
                        $this->AjaxReturn($msg);
                } else {
                        $msg["result"] = false;
                        $msg["message"] = "没有输入的帐号或错误的信息.";
                        $this->AjaxReturn($msg);
                }
        }

        public function getUser() {
                $username = I("username");
                $data = array();
                $where = array("delete" => 0, "username" => $username);
                $result = M("Member")->Where($where)->find();
                if ($result) {
                        $data["result"] = true;
                        $data["data"] = $result;
                } else {
                        $data["result"] = false;
                }
                $this->AjaxReturn($data);
        }

        public function login() {
                $username = I("email");
                $userpass = I("userpass", "0", "md5");
                $settings = settings();
                if (!check_verify($_POST['code'])) {
                        echo json_encode(array("result" => false, "message" => L('yanzhengmacuowu')));
                        //  $this->AjaxReturn(array("result" => false, "message" =>L('yanzhengmacuowu')));
                }
                $result = M("Member")->where(array("email" => $username, "userpass" => $userpass, 'delete' => 0))->find();
                if ($result) {
                        if ($result['emailstatus'] == 0) {
                                echo json_encode(array("result" => false, "message" => L("han2")));
                        }
                        if ((strtotime($result['addtime']) - time()) > ($settings['zidongshan'] * 24 * 3600)) {
                                $istouzi = M('touzi')->where(array('uid' => $result['id']))->find();
                                if (empty($istouzi)) {
                                        M('member')->where(array('id' => $result['id']))->save(array('delete' => 1));
                                        echo json_encode(array("result" => false, "message" => L("han3")));
                                }
                        }
                        echo json_encode(array("result" => true, "message" => L('dengluchenggong')));
                        //$this->AjaxReturn(array("result" => true, "message" => L('dengluchenggong')));
                } else {
                        echo json_encode(array("result" => false, "message" => L('denglushibai')));
//                        $this->AjaxReturn(array("result" => false, "message" => L('denglushibai')));
//			$this->AjaxReturn(array("result"=>false,"message"=>L('login-error')));
                }
        }

        public function getHeight() {
                $data = array();
                $CoinInfo = M("CoinInfo")->where(array("delete" => 0, "status" => 1))->select();
                foreach ($CoinInfo as $val) {
                        $data[] = array($val['title'], (int) $val['number']);
                }
                $this->AjaxReturn($data);
        }

        public function getWidth() {
                $info = (int) C("DEFAULT_WEB_SET_COIN_INFO");
                if ($info >= 100 || $info <= 0) {
                        $data = array(0);
                } else {
                        $data = array($info);
                }
                $this->AjaxReturn($data);
        }

        public function getfenhong() {
                $info = M('fenlog')->where(array('type' => 1))->order('date asc')->select();
                $data = array();
                foreach ($info as $v) {
                        echo date('Y-m-d\TH:i:s.0000\Z');
                        exit;
//                        $str.=strtotime($v['date']).'000,'.$v['dian'].',';
                        $data[] = array(date('Y-m-d\TH:i:s.0000\Z', strtotime($v['date']), $v['dian']));
                }
                echo json_encode($data);
//                echo "<pre>";
//                var_export($data);
//                echo "</pre>";exit;
////                $str=trim($str,',');
//                $this->AjaxReturn($data,'JSON');
//                echo $str;
        }

        public function leglist_select() {
//                header('Content-Type:application/json; charset=utf-8');
                $id = I("id");
//                $id=356;
                $User = session("MemberID");
                $data = M('member')->field("id as d_id,username,package_type,email,daili,tuicount,emailstatus")->where(array("delete" => 0, "r_id" => $id))->select();
//                echo "<pre>";
//                var_export($data);
//                echo "</pre>";
//                exit;
                $isdaqu = M('member')->where(array('p_id' => $id))->order('tuicount  desc')->find();
                foreach ($data as $key => $val) {

                        if ($result = M("Member")->where(array("r_id" => $val['d_id']))->find()) {
                                $data[$key]['member_yn'] = "y";
                        } else {
                                $data[$key]['member_yn'] = "n";
                        }
                        $sss = M('guanxi')->where(array('pid' => $User['id'], 'cid' => $id))->find();
                        if ($sss['dai'] > 1 && session('houtai') != 1) {
                                $data[$key]['member_yn'] = "n";
                        }
                        if ($val['package_type'] > 0) {
                                $package_type = M('package_type')->where(array('id' => $val['package_type']))->find();
                                $data[$key]['dengji'] = $package_type['title'];
                        } else {
                                $data[$key]['dengji'] = L("han1");
                        }
                        $tuan = new \Think\Model();
                        $tuandui = $tuan->table('zyx_member m,zyx_guanxi g')->where('g.pid=' . $val['d_id'] . '  and g.cid=m.id and m.delete=0')->count();
                        $data[$key]['tuandui'] = $tuandui;
                        $zhitui = M('member')->where(array('p_id' => $val['d_id'], 'delete' => 0))->count();
                        $data[$key]['zhitui'] = $zhitui;
                        $daqu = M('member')->where(array('p_id' => $val['d_id'], 'delete' => 0))->order('tuicount desc')->find();
                        if ($daqu) {
                                $data[$key]['daqu'] = $daqu['tuicount'];
                        } else {
                                $data[$key]['daqu'] = 0;
                        }
                        if ($val['tuicount'] == $isdaqu['tuicount']) {
                                $data[$key]['isdaqu'] = 1;
                        }
                }
                $map["d_uid"] = $id ? $id : $User['id'];
                $map["result"] = 0;
                $map["appay"] = $data;
                echo json_encode($map);
//                $this->AjaxReturn($map,'JSON');
        }

        public function shanxi_send() {
                $email = $_POST['email'];
                $title = $_POST['title'];
                $content = $_POST['content'];
                $checkmail = "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/"; //定义正则表达式  
                //判断文本框中是否有值                              //将传过来的值赋给变量$mail  
                if (preg_match($checkmail, $email)) {                       //用正则表达式函数进行判断  
                        $result = sendMail($email, $title, $content);

                        if ($result) {
                                return true;
                        } else {
                                
                        }
                } else {
                        return FALSE;
                }
        }

        public function send_email() {
//                $email = I("email");
                $email = '1176900613@qq.com';
                $number = mt_rand(100000, 999999);
                $content = "<br/>" . $typeName . "<br/> 驗證碼：" . $number . '<br/>有效時間：15分鐘';
                $title = '帳號驗證郵件-YTCoin';
                if (M("Verify")->data(array("number" => $number, "addtime" => date("Y-m-d H:i:s", time()), "email" => $email))->add()) {
                        $result = sendMail($email, $title, $content);
//                        $result = think_send_mail($email, $title, $content);
                        if ($result) {
                                $data['status'] = 1;
                                $data['info'] = "发送成功";
                                echo json_encode($data);
                        } else {
                                $data['status'] = -3;
                                $data['info'] = "发送失败";
                                echo json_encode($data);
                        }
                } else {
                        $data['status'] = -1;
                        $data['info'] = "发送失败";
                        echo json_encode($data);
                }
        }

        /**
         * ajax验证手机验证码
         */
        public function ajaxSandPhone() {
//            echo   1111;exit;
                $phone = urldecode(I('phone'));
                if (empty($phone)) {
                        $data['status'] = 0;
                        $data['info'] = "手机号码不能为空";
                        echo json_encode($data);
//            $this->ajaxReturn($data);
                }
                if (!preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
                        $data['status'] = -1;
                        $data['info'] = "手机号码不正确";
                        echo json_encode($data);
//            $this->ajaxReturn($data);
                }
                $user_phone = M("Member")->field('telephone')->where("telephone='$phone'")->find();
                if (!empty($user_phone)) {
                        $data['status'] = -2;
                        $data['info'] = "手机号码已经存在";
                        echo json_encode($data);
                }
                $r = sandPhone1($phone, 'YTCoin', 'jiaisi', 'gs900819');
                if ($r[0]) {
                        $data['status'] = 1;
                        $data['info'] = "发送成功";
                        echo json_encode($data);
//        	$this->ajaxReturn($data);
                } else {
                        $data['status'] = -3;
                        $data['info'] = chuanglan_status($r[1]);
                        echo json_encode($data);
//        	$this->ajaxReturn($data);
                }
        }

        function ajaxCheckPhone($phone) {
                $phone = urldecode($phone);
                $data = array();
                if (!checkMobile($phone)) {
                        $data['msg'] = "手机号不正确！";
                        $data['status'] = 0;
                } else {
                        $M_member = M('Member');
                        $where['phone'] = $phone;
                        $r = $M_member->where($where)->find();
                        if ($r) {
                                $data['msg'] = "此手机已经绑定过！请更换手机号";
                                $data['status'] = 0;
                        } else {
                                $data['msg'] = "";
                                $data['status'] = 1;
                        }
                }
                echo json_encode($data);
//        $this->ajaxReturn($data);
        }

        public function ajax_b_calendar() {
                $date = array(
                        array(
                                "title" => "USD $ 0",
                                "start" => "2015-12-24",
                                "color" => "#92a2a8",
                                "textColor" => "white",
                        ),
                        array(
                                "title" => "USD $ 56",
                                "start" => "2015-12-24",
                                "color" => "#356e35",
                                "textColor" => "white",
                        ),
                        array(
                                "title" => "USD $ 8",
                                "start" => "2015-12-24",
                                "color" => "#356e35",
                                "textColor" => "white",
                        ),
                        array(
                                "title" => "USD $ 0",
                                "start" => "2015-12-26",
                                "color" => "#92a2a8",
                                "textColor" => "white",
                        ),
                );
                $this->AjaxReturn($date);
        }

}
