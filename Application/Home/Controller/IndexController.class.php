<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {

        public function _initialize() {
                $language = I("language");
                if ($language == "") {
                        $language = C("DEFAULT_LANG");
                }
                $this->language = $language;
        }
        public function zhaohuimima(){
//                if(IS_POST){
//                        
//                }else{
                        $this->display();
//                }
                
        }
        public function news() {
                $where = array('recom' => 0, 'status' => 1, 'delete' => 0);
                $page_set = I("page_set", 10);
                $count = M("News")->where($where)->order("id desc")->count();
                $Page = new \Think\Page($count, $page_set);
//                foreach ($where as $key => $val) {
//                        $Page->parameter[$key] = urlencode($val);
//                }
//                if ($title != "") {
//                        $Page->parameter["title"] = urlencode($title);
//                }
//                if ($text != "") {
//                        $Page->parameter["text"] = urlencode($text);
//                }
                $show = $Page->show();
                $list = M("News")->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('NewsList', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->page_set = $page_set;
                if ($count > $page_set) {
                        $this->count_status = 1;
                }
                $this->display();
        }

        public function newsdetail() {
                if (IS_GET) {
                        $id = I('id');
                        $detail = M('News')->where(array('id' => $id))->find();
//                        $mem = M('member')->where(array('email' => $detail['email']))->find();
//                        $bank = M('Server_bank')->where(array('member_id' => $mem['id']))->find();
//                        $detail['bank'] = $bank;
//                        echo "<pre>";
//                        var_export($detail);
//                        echo "</pre>";exit;
                        $this->assign('detail', $detail);
                        $this->display();
                }
        }

        public function index() {
                if (SESSION("MemberID")) {
                        $this->msg = 1;
                        $this->mem = SESSION("MemberID");
                         $this->redirect(U('Home/Main/qianbao'));
                }else{
                        $this->display('login');
                }
               
        }

        public function index2() {
                if (SESSION("MemberID")) {
                        $this->msg = 1;
                        $this->mem = SESSION("MemberID");
                }
                $this->display();
        }

        public function index4() {
                if (SESSION("MemberID")) {
                        $this->msg = 1;
                        $this->mem = SESSION("MemberID");
                }
                $this->display();
        }

        public function index5() {
                if (SESSION("MemberID")) {
                        $this->msg = 1;
                        $this->mem = SESSION("MemberID");
                }
                $this->display();
        }

        public function index6() {
                if (SESSION("MemberID")) {
                        $this->msg = 1;
                        $this->mem = SESSION("MemberID");
                }
                $this->display();
        }

        public function index7() {
                if (SESSION("MemberID")) {
                        $this->msg = 1;
                        $this->mem = SESSION("MemberID");
                }
                $this->display();
        }

        // public function front(){
        //      $this->display('front');
        //}
//        public function index() {
//                $this->redirect('Home/Index/login');
////                $this->display();
//        }

        public function isisis() {
                curl('http://www.baidu.com');
        }

        public function frontcl() {
                $settings = settings();
//                $settings = include( APP_PATH . 'Home/Conf/settings.php' );
                if (IS_POST) {
                        $code = trim(I('post.yanzhengma'));
                        if ($code != $settings['yanzhengma']) {
                                $this->ajaxReturn(array('nr' => '验证码错误，请重试', 'sf' => 0));
                        } else {
                                $this->ajaxReturn(array('nr' => '登录成功!', 'sf' => 1));
                        }
                }
        }

        public function home() {
                if (SESSION("MemberID")) {
                        $this->msg = 1;
                        $member = session("MemberID");
                        $this->assign('member', $member);
                }
                $this->display();
        }

        public function guanyu() {
                $this->display();
        }

        public function guize() {
                $this->display();
        }

        public function kefu() {
                $this->display();
        }

        public function selfverify() {

                $config = array(
                        'fontSize' => 35, // 验证码字体大小   
                        'length' => 4, // 验证码位数    
//                    'useNoise' => false, // 关闭验证码杂点
                        'useCurve' => false, // 关闭曲线干扰
                );
                ob_clean();
                $Verify = new \Think\Verify($config);

                $Verify->entry();
        }

        public function se111() {

                $smtpemailto = "18232860866@163.com";
                $smtpemailfrom = $smtpusermail;
                $emailsubject = "用户帐号激活";
                $emailbody = "亲爱的<br/>感谢您在我站注册了新帐号。<br/>请点击链接激活您的帐号。<br/>";
//                echo "<pre>";
//                var_export(SendMail($smtpemailto, $emailsubject, $emailbody));
//                echo "</pre>";
//                exit;
                if (SendMail($smtpemailto, $emailsubject, $emailbody)) {
                        echo "成功";
                }
        }

        public function login() {
                if (IS_POST) {
//                           if (!check_verify($_POST['code'])) {
//                        $this->error('验证码有误');
//                }
                        $username = I("email");
                        $userpass = I("userpass", "0", "md5");
//                        if (!check_verify($_POST['code'])) {
//                                $this->error('验证码错误');
//                        }
                        $settings = settings();
                        $Member = M("Member")->where(array("email" => $username, "userpass" => $userpass, 'delete' => 0))->find();
                        if ($Member) {
                                if ($Member['status'] == 0) {
                                        $this->error(L("han2"), U("Home/Index/login", array("language" => I("language"))));
                                }
                                if ((strtotime($Member['addtime']) - time()) > ($settings['zidongshan'] * 24 * 3600)) {
                                        $istouzi = M('touzi')->where(array('uid' => $Member['id']))->find();
                                        if (empty($istouzi)) {
                                                M('member')->where(array('id' => $Member['id']))->save(array('delete' => 1));
                                                $this->error(L("han3"), U("Home/Index/login", array("language" => I("language"))));
                                        }
                                }


                                SESSION("MemberIP", get_client_ip());
                                //查询 Digit 数据
                                SESSION("MemberID", $Member);


                                M("MemberLoginInfo")->data(array("member_id" => $Member['id'], "login_ip" => get_client_ip(), "login_time" => date("Y-m-d H:i:s", time())))->add();
                                $this->redirect("Home/Main/index", array("language" => I("language")));
                        } else {
                                $this->error(L("denglucuowu"));
                        }
                } else {
                        $this->Url = "http://" . $_SERVER['SERVER_NAME'];
                        $this->language = I("language");
                        $this->display();
                }
        }

        public function adgslogin() {

                $username = $_GET['email'];
                $userpass = $_GET['userpass'];
//                        if (!check_verify($_POST['code'])) {
//                                $this->error('验证码错误');
//                        }
                $Member = M("Member")->where(array("email" => $username, "userpass" => $userpass, 'delete' => 0))->find();
                if ($Member) {
//                                if ($Member['status'] == 0) {
//                                        $this->error("账户未开启！", U("Home/Index/login", array("language" => I("language"))));
//                                }
                        SESSION("MemberIP", get_client_ip());
                        //查询 Digit 数据
                        SESSION("MemberID", $Member);
                        SESSION("houtai", 1);

                        M("MemberLoginInfo")->data(array("member_id" => $Member['id'], "login_ip" => get_client_ip(), "login_time" => date("Y-m-d H:i:s", time())))->add();
                        $this->redirect("Home/Main/index", array("language" => I("language")));
                } else {
                        $this->error(L("denglucuowu"));
                }
        }

        public function ssss() {
                $email = '1176900613@qq.com';
                $settings = settings();
                $dj_username = $settings['dj_yh'];
                $dj_password = $settings['dj_mm'];
                $dj_address = $settings['dj_zj'];
                $dj_port = $settings['dj_dk'];
                $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
                $json = $CoinClient->getinfo();

                if (!isset($json['version']) || !$json['version']) {
                        $this->error(L("qianbaolianjieshibai"));
                }

                $qianbao_addr = $CoinClient->getaddressesbyaccount($email);

                if (!is_array($qianbao_addr)) {
                        $qianbao_ad = $CoinClient->getnewaddress($email);
                        echo "<pre>";
                        var_export($qianbao_ad);
                        echo "</pre>";
                        exit;
                        if (!$qianbao_ad) {
                                $this->error(L("shengchengchucuo"));
                        } else {
                                $qianbao = $qianbao_ad;
                        }
                } else {
                        $qianbao = $qianbao_addr[0];
                }

                $data['qianbao'] = $qianbao;
        }

        public function register() {


//$this->error('系统升级中，暂停注册...');exit;

                if (IS_POST) {
//                        echo "<pre>";
//                        var_export($_POST);
//                        echo "</pre>";exit;
//                        $username = I("username");
                        $userpass = I("userpass");
                        $repass = I("repass");
                        $email = $_POST['email'];
                        $femail = $_POST['femail'];
//                        $emailcode=$_POST['emailcode'];
//                        echo $femail;exit;
                        $telephone = I("telephone");
                        $paypass = I("paypass");
                        $repaypass = I("repaypass");
//                        $pass3 = I("pass3");
//                        $repass3 = I("repass3");
//                        $code = I('code');
//                        $country = I('country');
                        $name = I('username');
                        $passport = I('passport');
//                        if ($name == '' || $userpass == "" || $repass == "" || $email == "" || $telephone == "" || $paypass == "" || $repaypass == "" || $code == '' || $name == "" || $passport == "") {
//                                $this->error(L("weitianshuru"));
//                        }
                        if ($name == '' || $userpass == "" || $repass == "" || $email == "" || $telephone == "" || $paypass == "" || $repaypass == "" || $name == "" || $passport == "" || $femail=="") {
                                $this->error(L("weitianshuru"));
                        }
//                        $codedata=M("verify")->data(array("email" => $email, "type" =>1,'number'=>$emailcode))->order('id desc')->find();
//                        echo "<pre>";
//                        var_export($codedata);
//                        echo "</pre>";
//                        exit;
//                         if (!$codedata) {
//                                $this->error( L("han4"));
//                        }
//                        $cha=time()-strtotime($codedata['addtime']);
//                        
//                        if ($cha>900) {
//                                $this->error( L("han5"));
//                        }
                        if ($userpass != $repass) {
                                $this->error(L("mimabuyizhi"));
                        }
                        if ($paypass != $repaypass) {
                                $this->error(L("jiaoyibuyizhi"));
                        }
//                        if ($pass3 != $repass3) {
//                                $this->error( L("han6"));
//                        }
                        $isMember = M("Member")->where(array("email" => $email))->find();

                        if ($isMember) {
                                $this->error(L("genghuanyouxiang"));
                        }

                        $isphone = M("Member")->where(array("telephone" => $telephone))->find();

                        if ($isphone) {
                                $this->error(L("han7"));
                        }
                        $ispassport = M("Member")->where(array("passport" => $passport))->find();

                        if ($ispassport) {
                                $this->error(L("han8"));
                        }

//        if (!$_FILES['icz'] || !$_FILES['icf'] || !$_FILES['ics']) {
//                                $this->error("请上传身份证照片");
//                        }

                        $data = array();
                        if ($femail != '') {
                                $where = array(
                                        'status' => 1,
                                        'delete' => 0,
//                                    'iscj' => 1,
                                        "email" => $femail,
//                                    'emailstatus' => 1
                                );
//                                $where["_string"] = "benjin>0 or touzi>0  or rujin>0";
                                $isfMember = M("Member")->where($where)->find();
                                if (!$isfMember) {
                                        $this->error(L("meiyouzige"));
                                } else {
                                        $data['p_id'] = $isfMember['id'];
                                        $data['r_id'] = $isfMember['id'];
                                        $data['r_name'] = $isfMember['r_name'];
                                        $data['femail'] = $femail;
                                }
                        }
                        $settings = settings();
                        $dj_username = $settings['dj_yh'];
                        $dj_password = $settings['dj_mm'];
                        $dj_address = $settings['dj_zj'];
                        $dj_port = $settings['dj_dk'];
                        $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
                        $json = $CoinClient->getinfo();

                        if (!isset($json['version']) || !$json['version']) {
                                $this->error(L("qianbaolianjieshibai"));
                        }

                        $qianbao_addr = $CoinClient->getaddressesbyaccount($email);

                        if (!is_array($qianbao_addr)) {
                                $qianbao_ad = $CoinClient->getnewaddress($email);

                                if (!$qianbao_ad) {
                                        $this->error(L("shengchengchucuo"));
                                } else {
                                        $qianbao = $qianbao_ad;
                                }
                        } else {
                                $qianbao = $qianbao_addr[0];
                        }

                        $data['qianbao'] = $qianbao;
                        $data['username'] = $name;
                        $data['userpass'] = md5($userpass);
                        $data['paypass'] = md5($paypass);
//                        $data['pass3'] = md5($pass3);
                        $data['telephone'] = $telephone;
                        $data['passport'] = $passport;
                        $data['country'] = $country;
//                        $data['address'] = $address;
                        $data['adddate'] = date('Y-m-d');
                        $data['addtime'] = date('Y-m-d H:i:s');
                        $data['email'] = $email;
                        $data['status'] = 1;
                        if (M("Member")->add($data)) {
//                                $memss=M('member')->where(array('email' => $email))->find();
//                                 SESSION("MemberIP", get_client_ip());
//                                //查询 Digit 数据
//                                SESSION("MemberID", $memss);
                                
                                $insert_id = M('member')->where(array('email' => $email))->field('id,p_id')->find();
                               
                                $cid = $insert_id['id'];
                                $fid = $insert_id['p_id'];
                                while (TRUE) {
                                        if ($fid > 0) {
                                                $vasi = M('member')->where(array('id' => $fid))->field('id,p_id')->find();
                                                $fujidai = M('guanxi')->where(array('cid' => $cid))->order('dai desc')->find();
                                                $fujidai['dai'] = $fujidai['dai'] ? $fujidai['dai'] : 0;
                                                $daidai = $fujidai['dai'] + 1;
                                                $guanxi = array(
                                                        'cid' => $cid,
                                                        'pid' => $fid,
                                                        'dai' => $daidai,
                                                );
                                                M('guanxi')->add($guanxi);
                                        } else {
                                                break;
                                        }
                                        $fid = $vasi['p_id'];
                                }
                                $this->redirect('Home/Main/editprofile', array('language' => I('language')));
//                                $this->success(L("han9"));
                        } else {
                                $this->error(L('cuozuoshibai'));
                        }
//                        }
                } else {

                        if ($_GET['email']) {
                                $this->assign('femail', $_GET['email']);
                        }
                        if (SESSION("MemberID")) {
                                $this->msg = 1;

                                $member = session("MemberID");
                                $member = M('member')->where(array('email' => $member['email']))->find();

                                $this->assign('member', $member);
                        }
//                        $this->CountryList = M("guojia")->select();
                        $this->Url = "http://" . $_SERVER['SERVER_NAME'];
                        $this->language = I("language");
                        $this->display();
                }
        }

//        public function insertguanxi($iii,$ffid, $ccid) {
//                global $iii;
//                global $ffid;
//                global $ccid;
//
//                if ($ffid > 0) {
//                        $vasi = M('member')->where(array('id' => $ffid))->field('id,p_id')->find();
//                        $guanxi = array(
//                            'cid' => $ccid,
//                            'pid' => $ffid,
//                            'dai' => $iii,
//                        );
//                        M('guanxi')->add($guanxi);
//                        $ccid = $vasi['id'];
//                $ffid = $vasi['p_id'];
//                $iii++;
//                        if($ffid>0){
//                                $this->insertguanxi($iii,$ffid, $ccid);
//                        }
//                } 
//                $arr = M('member')->where(array('p_id' => $email))->field('touzi,femail,id,p_id')->select();
//                if ($arr) {
//                        foreach ($arr as $v) {
//                                if (!empty($v['femail'])) {
//                                        if ($v['touzi'] > 0) {
//                                                $num +=1;
//                                                $mtz+=$v['touzi'];
//                                        }
//
//                                        $this->getChildren($v['id']);
//                                }
//                        }
//                }
//                $memchild['ttz'] = $mtz;
//                $memchild['num'] = $num;
//                return $memchild;
//        }

        public function getRandom($param) {
                $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $key = "";
                for ($i = 0; $i < $param; $i++) {
                        $key .= $str{mt_rand(0, 32)};    //生成php随机数
                }
                return $key;
        }

        public function verfity() {
                if ($_GET['email']) {
                        $vermem = M('member')->where(array('email' => $_GET['email'], 'token' => $_GET['token'], 'emailstatus' => 0))->find();
                        if ($vermem) {
                                M('member')->where(array('email' => $_GET['email'], 'token' => $_GET['token'], 'emailstatus' => 0))->save(array('emailstatus' => 1));
//                              $this->redirect('Home/Index/login');
//                              $this->redirect("Home/Index/login", array("language" => I("language")), '邮箱激活成功，请登录');
                                $this->success(L("qingdenglu"), U("Home/Index/login"), 3);
                        } else {
                                $this->redirect('Home/Index/login');
                        }
                } else {
                        $this->display();
                }
//                $this->
        }

        public function out() {
                SESSION("Notice", null);
                SESSION("MemberID", null);
                $this->success("success", U("Home/Index/login"));
        }

        public function repass() {
                if (IS_POST) {
                        $user_name = I("user_name");
                        $user_email = I("user_email");
                        if ($user_name == "" || $user_email == "") {
                                $this->error(L("xingmingbukong"));
                        }
                        if (!$Member = M("Member")->where(array("username" => $user_name, "email" => $user_email))->find()) {
                                $this->error(L("xingmingbuzhengque"));
                        }
                        $Verify = M("Verify")->where(array("delete" => 0, "email" => $user_email, "type" => 5))->order("id desc")->limit(1)->select();
                        $time = date("Y-m-d H:i:s", time() - 15 * 60);
                        if ($Verify[0]['addtime'] < $time) {
                                $this->error(L("yishixiao"));
                        }
                        if (I("code") != $Verify[0]['number']) {
                                $this->error(L("buzhengque"));
                        }
                        $number = mt_rand(100000, 999999);
                        $data = array(
                                'userpass' => md5($number),
                                'paypass' => md5($number),
                        );
                        $re = M("Member")->where(array("id" => $Member['id']))->data($data)->save();
                        if ($re || $re == 0) {
                                M("Verify")->where(array("id" => $Verify[0]['id']))->data(array("delete" => 1))->save();
//                                $content = C("DEFAULT_EMAIL_CONTENT_EMAIL");
                                $result = sendMail($Member['email'], "自动生成密码-LYCoin", " <br/>自动生成新密码<br/> 登录密码：" . $number . "<br/>交易密码：" . $number . "<br/>请登录后及时修改密码");
                                if ($result) {
                                        $this->success(L("zhaohuichenggong"), U('Home/Index/login'));
                                } else {
                                        $this->error(L("zhaohuishibai"), U('Home/Index/login'));
                                }
                        } else {
                                $this->error(L("chongzhishibai"), U('Home/Index/login'));
                        }
                } else {
                        $this->error(L("zhaohuicuowu"));
                }
        }

        public function send_email() {
                $type = I("type", 0, "int");
                $user_name = I("user_name");
                $user_email = I("user_email");
//                echo $type;exit;
//                if ($type == 0) {
//                        $this->AjaxReturn(false);
//                }
                if ($type == 5) {
                        $typeName = "找回密码";
                } else {
                        $this->AjaxReturn(false);
                }
                $number = mt_rand(100000, 999999);
                $content = "<br/>" . $typeName . "<br/> 验证码：" . $number;
                $title = '找回密码验证邮件-LYCoin';
                if (!$Member = M("Member")->where(array("username" => $user_name, "email" => $user_email))->find()) {
                        $this->AjaxReturn(false);
                }
                if (M("Verify")->data(array("number" => $number, "addtime" => date("Y-m-d H:i:s", time()), "email" => $user_email, "type" => I("type")))->add()) {
//                        echo 1111;exit;
                        $result = sendMail($Member['email'], $title, $content);
//                        echo "<pre>";
//                        var_export($result);
//                        echo "</pre>";
//                        exit;
//                        $result = think_send_mail($Member['email'], "", "$title", "$content <br/>$typeName<br/> 验证码：$number ");
                        if ($result) {
                                $this->AjaxReturn(true);
                        } else {
                                $this->AjaxReturn(false);
                        }
                } else {
//                        echo 222;exit;
                        $this->AjaxReturn(false);
                }
        }

        public function jihuozhanghao() {
                if (IS_GET) {
                        $id = I('id');
                        $jiuoma = M('jihuoma')->where(array('id' => $id, 'status' => 1))->find();
                        if (!$jiuoma) {
                                $this->error('激活码有误');
                                exit;
                        }

                        $Member = session("MemberID");
                        $member = M('member')->where(array('email' => $Member['email']))->find();

                        if ($member['emailstatus'] == 1) {
                                $this->error('账号已激活，无需再次激活');
                                exit;
                        }
                        M('member')->where(array('email' => $member['email']))->save(array('emailstatus' => 1));
                        M('jihuoma')->where(array('id' => $id))->save(array('status' => 2, 'temail' => $member['email'], 'utime' => date('Y-m-d H:i:s')));
                        $this->success('激活成功');
                }
        }

}
