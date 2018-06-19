<?php

namespace Admin\Controller;

use Think\Controller;

class MainController extends CommonController {

        public function index() {
                $result = M("Admin")->where($where)->find();
                $this->zonghuiyuan = M("Member")->where(array("delete" => 0))->count();
                $this->xinhuiyuan = M("Member")->where(array("adddate" => date('Y-m-d'), 'delete' => 0))->count();
                $this->zongshouru = M("chongzhi")->sum('money');
                $this->zongtixian = M("tixian")->sum('mum');
                $zongfenhong1 = M('touzi')->sum('chanliang');
                $zongfenhong = $zongfenhong1 * 136 * 0.7;
                $jingtai1 = M("fenhong")->sum('money');
                $jingtai2 = $zongfenhong - $jingtai1;
                $this->jingtai1 = $jingtai1;
                $this->jingtai2 = $jingtai2;
                $this->zhitui = M('jiangjin')->where(array('type' => 1))->sum('money');
                $this->dongtai = M('jiangjin')->where(array('type' => 2))->sum('money');


                $this->benjin = M("Member")->sum('benjin');
                $this->futou = M("Member")->sum('futou');
                $fengding = M('yichu')->sum('money');
                $xiaohui = M('xiaohui')->sum('money');
                $this->xiaohui = $xiaohui;
                $this->fengding = $fengding;
//                $this->NewsNumber = M("News")->where(array("delete" => 0))->count();
//                $this->PriceNumber = M("tixian")->where(array("delete" => 0))->count();
//                $wheres = array(
//                        'status' => 2,
//                );
////                $wheres['ctime']=array('lt','2017-05-04 14:40:00');
//                $maijin = M('chongzhi')->query('select sum(`money`) as aaa from zyx_rujin where status=3');
//                $maichu = M('chujin')->sum('yxb');
//                $bd = M('moneybd')->where(array('type' => 1))->sum('money');
//
//                $maijin = $maijin[0]['aaa'] + $bd;
//
//
//                $where2 = array(
//                        'ctime' => array('like', date('Y-m-d') . '%'),
//                );
//                $where3 = array(
//                        'type' => 1,
//                        'ctime' => array('like', date('Y-m-d') . '%'),
//                );
//                $maijin1 = M('rujin')->query('select sum(`yxb`) as aaa from zyx_rujin where status=3 and ctime like "' . date('Y-m-d') . '%"');
//                echo "<pre>";
//                var_export($maijin1);
//                echo "</pre>";exit;
//                $where['name']=array('like','jb51%');
//                $maijin1 = M('rujin')->where($where1)->sum('yxb');
//                $maichu1 = M('chujin')->where($where2)->sum('yxb');
//                $bd1 = M('moneybd')->where($where3)->sum('money');
//                $maijin1 = $maijin1[0]['aaa'] + $bd1;
//                $this->Maijin = $maijin;
//                $this->Maichu = $maichu;
//                $this->Maijina = $maijin1;
//                $this->Maichua = $maichu1;
                $this->display();
        }

        public function renwu() {
                $this->display();
        }

        public function zhengchangdengji() {
                $mem = M('member')->select();
                foreach ($mem as $v) {
                        $touzi = M('touzi')->where(array('_string' => 'tianshu>0', 'uid' => $v['id']))->order('money desc')->find();
                        if ($v['package_type'] != $touzi['type'] && !empty($touzi)) {
                                M('member')->where(array('id' => $v['id']))->save(array('package_type' => $touzi['type']));
                        }
                }
                $this->success('操作成功');
//                $this->display();
        }

        public function fenhong() {
                if (IS_POST) {
                        $date1 = $_POST['date1'];
                        $date2 = $_POST['date2'];
                        $memdata = M('member')->where(array('_string' => 'adddate>="' . $date1 . '" and adddate<="' . $date2 . '" and package_type>0', 'emailstatus' => 1))->select();
                        $count = count($memdata);
//                        echo "<pre>";
//                        var_export($memdata);
//                        echo "</pre>";
//                        exit;
                        foreach ($memdata as $v) {
                                $mtouzi = M('touzi')->where(array('_string' => 'tianshu>0', 'uid' => $v['id']))->select();

                                foreach ($mtouzi as $s) {
//判断封顶
                                        $s['chanliang'] = fengding($v, $s['chanliang']);

                                        $data = array(
                                                'money' => $s['chanliang'] * 0.9,
                                                'email' => $v['email'],
                                                'uid' => $v['id'],
                                                'ctime' => date('Y-m-d H:i:s'),
                                                'pan' => $s['type'],
                                        );
                                        $data2 = array(
                                                'money' => $s['chanliang'] * 0.1,
                                                'email' => $v['email'],
                                                'uid' => $v['id'],
                                                'ctime' => date('Y-m-d H:i:s'),
                                                'pan' => $s['type'],
                                                'type' => 2,
                                        );
                                        if (M('fenhong')->add($data) && M('xiaohui')->add($data2)) {
                                                M('member')->where(array('id' => $v['id']))->setInc('benjin', $s['chanliang'] * 0.9);
//                                                M('member')->where(array('id' => $v['id']))->setInc('futou', $s['chanliang'] * 0.2);
                                                M('member')->where(array('id' => $v['id']))->setInc('xiaohui', $s['chanliang'] * 0.1);
                                                M('touzi')->where(array('id' => $s['id']))->setDec('tianshu', 1);
                                        }


                                        if ($s['tianshu'] == 1) {
                                                M('member')->where(array('id' => $v['id']))->setDec('tuicount', $s['money']);
                                                M('member')->where(array('id' => $v['id']))->setDec('touzi', $s['money']);
//                                M('member')->where(array('id' => $v['id']))->setDec('pan' . $pan, $money);
                                                //给上级加金额
                                                $fudata = M('guanxi')->where(array('cid' => $v['id']))->select();
                                                foreach ($fudata as $v) {
                                                        M('member')->where(array('id' => $v['pid']))->setDec('tuicount', $s['money']);
                                                }
                                        }
                                }
                        }
                        $this->success('已经为' . $count . '位用户生成静态奖金');
                } else {
                        $this->success('无可用数据');
                }
        }

        public function dongtaijiang() {
                $jiangjinlist = M('fenhong')->field('uid,email,SUM(money) AS summoney')->where(array('isjiangjin' => 1))->group('uid')->select();


                foreach ($jiangjinlist as $k => $v) {
                        $guanxiarr = M('guanxi')->where(array('cid' => $v['uid']))->order('dai asc')->select();
                        foreach ($guanxiarr as $g) {
                                $sid = M('member')->where(array('id' => $g['pid'], '_string' => 'package_type>0'))->find();
                                if (!empty($sid)) {
                                        $sdengji = M('package_type')->where(array('id' => $sid['package_type']))->find();
                                        $datamoney = array();
                                        if ($sdengji['fenhongcs'] >= $g['dai']) {
                                                $smoney = $v['summoney'] * $sdengji['fenhongbfb'] / 90;
                                                $datamoney['bili'] = $sdengji['fenhongbfb'];
                                        } else {
                                                $smoney = $v['summoney'] * $sdengji['fenhongwx'] / 90;
                                                $datamoney['bili'] = $sdengji['fenhongwx'];
                                        }
                                        //判断封顶
                                        $fanmoney = fengding($sid, $smoney);
                                        $fanmoney = round($fanmoney, 4);
                                        if ($fanmoney > 0) {
                                                $datamoney['email'] = $sid['email'];
                                                $datamoney['uid'] = $sid['id'];
                                                $datamoney['ctime'] = date('Y-m-d H:i:s');

                                                $datamoney['dai'] = $g['dai'];
                                                $datamoney['temail'] = $v['email'];

//                                                $datamoney1 = $datamoney;
                                                $datamoney2 = $datamoney;
                                                $datamoney['type'] = 2;
                                                $datamoney['money'] = round($fanmoney * 0.9, 4);
                                                $datamoney2['type'] = 3;
                                                $datamoney2['money'] = round($fanmoney * 0.1, 4);
//                                                echo "<pre>";
//                                                var_export($datamoney);
//                                                echo "</pre>";
//                                                echo "<pre>";
//                                                var_export($datamoney2);
//                                                echo "</pre>";
//                                                exit;
                                                if (M('jiangjin')->add($datamoney) && M('xiaohui')->add($datamoney2)) {
//                                                        echo 111;exit;
                                                        M('member')->where(array('email' => $sid['email']))->setInc('benjin', round($fanmoney * 0.9, 4));
                                                        M('member')->where(array('email' => $sid['email']))->setInc('xiaohui', round($fanmoney * 0.1, 4));
//                                                M('member')->where(array('email' => $shangjimem['email']))->setInc('futou', round($fanmoney * 0.2, 4));
                                                }
//                                        unset($zijitui);
                                                unset($sdengji);
                                                unset($sid);
//                                                unset($ssdd);
                                                $fanmoney = 0;
                                        }
                                } else {
                                        unset($sdengji);
                                        unset($sid);
//                                        unset($ssdd);
                                }
                                M('fenhong')->where(array('uid' => $v['uid']))->save(array('isjiangjin' => 2));
                        }
                        $this->success('动态奖已经生成');
                }



//                
//                foreach ($jiangjinlist as $v) {
//                       
//                        $dengji = M('package_type')->order('id desc')->find();
//                        $cengshu = $dengji['fenhongcs'];
//                        $yuanshi=$v['summoney'];
//                        
//                        for ($i =1; $i <= $cengshu; $i++) {
//                                $zijitui = M('member')->where(array('id' => $v['uid']))->find();
//                                $shangji = M('guanxi')->where(array('cid' => $v['uid'], 'dai' => $i))->find();
//                                
//                                $shangjimem = M('member')->where(array('id' => $shangji['pid'], '_string' => 'package_type>0'))->find();
//                                $ssdd = M('package_type')->where(array('id' => $shangjimem['package_type']))->find();
//                                
//                                if (!empty($shangjimem)) {
////                                        echo $v['summoney'].'aaa';
//                                        $v['summoney'] = round($v['summoney'] / 0.7,4);
//                                        
//                                        //上级的大区数据
//                                        $zuida = M('member')->where(array('p_id' => $shangji['pid']))->order('tuicount desc')->find();
//                                        $datamoney = array();
//                                        if ($i == 1) {
//
//                                                if ($zuida['tuicount'] == $zijitui['tuicount']) {
////大区
//                                                        $fanmoney = $v['summoney'] * 0.01;
//                                                        $datamoney['bili'] = 1;
//                                                        $datamoney['daxiao'] = 1;
//                                                } else {
////小区
//
//                                                        $fanmoney = $v['summoney'] * $ssdd['xiaoqujiangli'] / 100;
//                                                        $datamoney['bili'] = $ssdd['xiaoqujiangli'];
//                                                        $datamoney['daxiao'] = 2;
//                                                }
//                                        } else {
//                                                $f = $i - 1;
//                                                $ciji = M('guanxi')->where(array('cid' => $v['uid'], 'dai' => $f))->find();
//                                                $cijitui = M('member')->where(array('id' => $ciji['pid']))->find();
//                                                if ($cijitui['tuicount'] == $zuida['tuicount']) {
////大区
//                                                        if ($ssdd['daqucengji'] >= $i) {
//                                                                $fanmoney = $v['summoney'] * 0.01;
//                                                                $datamoney['bili'] = 1;
//                                                                $datamoney['daxiao'] = 1;
//                                                        } else {
//                                                                $fanmoney = 0;
//                                                                $datamoney['bili'] = 1;
//                                                                $datamoney['daxiao'] = 1;
//                                                        }
//                                                } else {
////小区
//                                                        $fanmoney = $v['summoney'] * $ssdd['xiaoqujiangli'] / 100;
//                                                        $datamoney['bili'] = $ssdd['xiaoqujiangli'];
//                                                        $datamoney['daxiao'] = 2;
//                                                }
//                                        }
//                                        
//                                        //判断封顶
//                                        $fanmoney = fengding($shangjimem, $fanmoney);
//                                        $fanmoney= round($fanmoney,4);
//                                        
//                                        if ($fanmoney > 0) {
//                                                $datamoney['email'] = $shangjimem['email'];
//                                                $datamoney['uid'] = $shangjimem['id'];
//                                                $datamoney['ctime'] = date('Y-m-d H:i:s');
//
//                                                $datamoney['dai'] = $i;
//                                                $datamoney['temail'] = $v['email'];
//
//                                                $datamoney1 = $datamoney;
//                                                $datamoney2 = $datamoney;
//                                                $datamoney['type'] = 2;
//                                                $datamoney['money'] = round($fanmoney * 0.7, 4);
//                                                $datamoney1['type'] = 3;
//                                                $datamoney1['money'] = round($fanmoney * 0.2, 4);
//                                                $datamoney2['type'] = 3;
//                                                $datamoney2['money'] = round($fanmoney * 0.1, 4);
////                                                        echo "<pre>";
////                                                        var_export($datamoney);
////                                                        echo "</pre>";
////                                                        echo "<pre>";
////                                                        var_export($datamoney1);
////                                                        echo "</pre>";
////                                                        echo "<pre>";
////                                                        var_export($datamoney2);
////                                                        echo "</pre>";
////                                                        exit;
////                                                echo "<pre>";
////                                                var_export($datamoney);
////                                                echo "</pre>";
////                                                exit;
//                                                if (M('jiangjin')->add($datamoney) && M('futou')->add($datamoney1) && M('xiaohui')->add($datamoney2)) {
//                                                        M('member')->where(array('email' => $shangjimem['email']))->setInc('benjin', round($fanmoney * 0.7, 4));
//                                                        M('member')->where(array('email' => $shangjimem['email']))->setInc('xiaohui', round($fanmoney * 0.1, 4));
//                                                        M('member')->where(array('email' => $shangjimem['email']))->setInc('futou', round($fanmoney * 0.2, 4));
//                                                }
//                                                unset($zijitui);
//                                                unset($shangji);
//                                                unset($shangjimem);
//                                                unset($ssdd);
//                                               $fanmoney=0;
//                                        }
//                                } else {
////                                        $zijitui = M('member')->where(array('id' => $v['uid']))->find();
////                                $shangji = M('guanxi')->where(array('cid' => $v['uid'], 'dai' => $i))->find();
////                                $shangjimem = M('member')->where(array('id' => $shangji['pid'], '_string' => 'package_type>0'))->find();
////                                $ssdd = M('package_type')->where(array('id' => $shangjimem['package_type']))->find();
//                                        unset($zijitui);
//                                        unset($shangji);
//                                        unset($shangjimem);
//                                        unset($ssdd);
//                                }
//                                $v['summoney']=$yuanshi;
////                                echo $v['summoney'].'cccc';
//                        }
//                        M('fenhong')->where(array('uid'=>$v['uid']))->save(array('isjiangjin'=>2));
//                        
//                }
//                $this->success('动态奖已经生成');
        }

        public function zengsong() {
                if (IS_POST) {
                        /**
                         * 赠送会员   
                         * 1、判断会员是否存在
                         * 2、根据数量批量生成激活码
                         * 
                         */
                        $email = trim($_POST['email']);
                        $num = trim(intval($_POST['num']));
                        $fangshi = trim(intval($_POST['fangshi']));
                        $mem = M('member')->where(array('email' => $email))->find();

                        if ($mem) {
                                if ($fangshi == 1) {
                                        if ($num >= 1) {
                                                for ($i = 0; $i < $num; $i++) {
                                                        $str = md5(getRandom(32) . $i . time());
                                                        $data = array(
                                                                'email' => $email,
                                                                'ctime' => date('Y-m-d H:i:s'),
                                                                'jihuoma' => $str,
                                                        );
                                                        M('jihuoma')->add($data);
                                                }
                                                $this->success('赠送成功');
                                        } else {
                                                $this->error('激活码个数有误');
                                        }
                                } elseif ($fangshi == 2) {
                                        if ($num >= 1) {
                                                M('jihuoma')->where(array('email' => $email, 'status' => 1))->limit($num)->delete();

                                                $this->success('减少成功');
                                        } else {
                                                $this->error('激活码个数有误');
                                        }
                                }
                        } else {
                                $this->error('会员账号有误');
                        }
                } else {
                        $this->display();
                }
        }

        public function packageType() {
                if (session("packagetypeid") && session("model") == 2) {
                        $PackageType = M("PackageType")->where(array("id" => session("packagetypeid")))->find();
                        $this->title = $PackageType["title"];
                        $this->status = $PackageType["status"];
                        $this->en_title = $PackageType["en_title"];
                        $this->price = $PackageType["price"];
                        $this->chanliang = $PackageType["chanliang"];
                        $this->tianshu = $PackageType["tianshu"];
                        $this->fenhongbfb = $PackageType["fenhongbfb"];
                        $this->fenhongcs = $PackageType["fenhongcs"];
                        $this->fenhongwx = $PackageType["fenhongwx"];
//                        $this->xiaoqujiangli = $PackageType["xiaoqujiangli"];
                        $this->zhituijiangli = $PackageType["zhituijiangli"];
//                        $this->en_title = $PackageType["en_title"];
//                        $this->en_title = $PackageType["en_title"];
//                        $this->en_title = $PackageType["en_title"];
                } else {
                        $this->title = urldecode(I("title"));
                        $this->en_title = urldecode(I("en_title"));
                        $this->price = urldecode(I("price"));
                        $this->chanliang = urldecode(I("chanliang"));
                        $this->tianshu = urldecode(I("tianshu"));
                        $this->fenhongbfb = urldecode(I("fenhongbfb"));
                        $this->fenhongcs = urldecode(I("fenhongcs"));
                        $this->fenhongwx = urldecode(I("fenhongwx"));
                        $this->zhituijiangli = urldecode(I("zhituijiangli"));
                }
                $this->model = session("model");
                session("model", null);
                $count = M("PackageType")->where(array("delete" => 0))->count();
                $Page = new \Think\Page($count, 40);
                $show = $Page->show();
                $list = M("PackageType")->where(array("delete" => 0))->order('id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
//                echo "<pre>";
//                var_export($list);
//                echo "</pre>";
//                exit;
                $this->assign('PackageType', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->display();
        }

        public function addPackageType() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (IS_POST) {
                        $AddPackageType = D("AddPackageType");
                        if (session("packagetypeid")) {
                                $_POST["id"] = session("packagetypeid");
                        }
                        if (!$AddPackageType->create()) {
                                session("model", 1);
                                $this->error($AddPackageType->getError(), U('Admin/Main/packageType', array("title" => urlencode(I("title")), "en_title" => urlencode(I("en_title")), 'chanliang' => I("chanliang"), 'tianshu' => I("tianshu"), 'fenhongbfb' => I("fenhongbfb"), 'fenhongcs' => I("fenhongcs"), 'fenhongwz' => I("fenhongwz"), 'zhituijiangli' => I("zhituijiangli"))));
                        } else {
                                if (session("packagetypeid")) {
                                        session("packagetypeid", null);
                                        $result = $AddPackageType->save();
                                        if ($result || $result == 0) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                } else {
                                        if ($AddPackageType->add()) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                }
                                $this->redirect('Admin/Main/packageType', array("p" => I('p')), 0, '页面跳转中...');
                        }
                } else {
                        if (I("id") != "") {
//有id  进入修改
                                session("packagetypeid", I("id"));
                                session("model", 2);
                        } else {
                                session("packagetypeid", null);
                                session("model", 1);
                        }
                        $this->redirect('Admin/Main/packageType', array("p" => I('p')), 0, '页面跳转中...');
                }
        }

        public function statusPackageType() {
                if (changeStatus("PackageType", I("id"), I("status"))) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/packageType', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function deletePackageType() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $id = I("id");
                if (del("PackageType", $id)) {
                        $this->success("加入回收站 成功");
                } else {
                        $this->error("加入回收站 失败");
                }
        }

        /*         * 新闻* */

        public function news() {
                $this->model = session("model");
                session("model", null);
                $count = M("News")->where(array("delete" => 0))->count();
                $Page = new \Think\Page($count, 10);
                $show = $Page->show();
                $list = M("News")->where(array("delete" => 0))->order('id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('NewsList', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->display();
        }

        public function sz() {
                $this->model = session("model");
                session("model", null);
                $count = M("shaizi")->count();
                $Page = new \Think\Page($count, 10);
                $show = $Page->show();
                $list = M("shaizi")->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $sss = array();
                foreach ($list as $v) {
                        if (time() <= strtotime($v['etime']) && time() >= strtotime($v['stime'])) {
                                $v['status'] = 1;   //正在押注
                        } elseif (time() > strtotime($v['etime']) && $v['shai1'] == null && $v['shai2'] == null) {
                                $v['status'] = 2;  //已完事  未开奖
                        } elseif (time() > strtotime($v['etime']) && $v['shai1'] != null && $v['shai2'] != null) {
                                $v['status'] = 3;  //已完事  已开奖
                        } elseif (time() < strtotime($v['stime'])) {
                                $v['status'] = 4;  //已完事  已开奖
                        } else {
                                $v['status'] = 5;  //未启动
                        }
                        $sss[] = $v;
                }
                $this->assign('NewsList', $sss);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->display();
        }

        public function shaizilist() {
                $id = I('id');
                $this->model = session("model");
                session("model", null);
                $count = M("shaizilist")->where(array('sid' => $id))->count();
                $Page = new \Think\Page($count, 10);
                $show = $Page->show();
                $list = M("shaizilist")->where(array('sid' => $id))->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $sum = array();
                for ($i = 1; $i <= 21; $i++) {
                        $where = array(
                                'sid' => $id, 'touzhu' => $i
                        );
                        $where['qianbao'] = array('neq', 4);
                        $aaa = M("shaizilist")->where($where)->sum('money');
                        $sum[$i] = $aaa ? $aaa : 0;
                }


                $this->assign('id', $id);
                $this->assign('NewsList', $list);
                $this->assign('page', $show);
                $this->assign('sum', $sum);
                $this->p = I("p", 0);
                $this->display();
        }

        public function addNews() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (IS_POST) {
                        $AddNews = D("AddNews");
                        if (session("newsid")) {
                                $_POST["id"] = session("newsid");
                        }
                        if (!$AddNews->create()) {
                                if (session("newsid")) {
                                        $this->error($AddNews->getError(), U('Admin/Main/addNews', array("id" => session("newsid"))));
                                } else {
                                        $this->error($AddNews->getError(), U('Admin/Main/addNews'));
                                }
                        } else {
                                if (session("newsid")) {
                                        session("newsid", null);
                                        $result = $AddNews->save();
                                        if ($result || $result == 0) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                } else {
                                        if ($AddNews->add()) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                }
                                $this->redirect('Admin/Main/news', array("p" => I('p')), 0, '页面跳转中...');
                        }
                } else {
                        if (I("id") != "") {
//有id  进入修改
                                session("newsid", I("id"));
                                if ($News = M("News")->where(array("id" => I('id'), "delete" => 0))->find()) {
                                        $this->News = $News;
                                }
                        } else {
                                session("newsid", null);
                        }
                        $this->p = I("p");
                        $this->display();
                }
        }

        public function addSz() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (IS_POST) {
                        if ($_POST['id'] != "") {
                                $data = array(
                                        'stime' => $_POST['stime'],
                                        'etime' => $_POST['etime'],
                                );
                                if ($_POST['shai1'] != null && $_POST['shai2'] != null) {
                                        $data['shai1'] = $_POST['shai1'];
                                        $data['shai2'] = $_POST['shai2'];
                                        $jiahe = $data['shai1'] + $data['shai2'];
                                        if ($jiahe >= 7) {
                                                $daxiao = 1;
                                        } else {
                                                $daxiao = 2;
                                        }

                                        if ($jiahe % 2 == 0) {
                                                $danshuang = 4;
                                        } else {
                                                $danshuang = 3;
                                        }
                                        if ($data['shai1'] == $data['shai2'] && $data['shai1'] == 1) {
                                                $maidui = 5;
                                        } elseif ($data['shai1'] == $data['shai2'] && $data['shai1'] == 2) {
                                                $maidui = 6;
                                        } elseif ($data['shai1'] == $data['shai2'] && $data['shai1'] == 3) {
                                                $maidui = 7;
                                        } elseif ($data['shai1'] == $data['shai2'] && $data['shai1'] == 4) {
                                                $maidui = 8;
                                        } elseif ($data['shai1'] == $data['shai2'] && $data['shai1'] == 5) {
                                                $maidui = 9;
                                        } elseif ($data['shai1'] == $data['shai2'] && $data['shai1'] == 6) {
                                                $maidui = 10;
                                        }

                                        if ($jiahe == 2) {
                                                $qiuhe = 11;
                                        } elseif ($jiahe == 3) {
                                                $qiuhe = 12;
                                        } elseif ($jiahe == 4) {
                                                $qiuhe = 13;
                                        } elseif ($jiahe == 5) {
                                                $qiuhe = 14;
                                        } elseif ($jiahe == 6) {
                                                $qiuhe = 15;
                                        } elseif ($jiahe == 7) {
                                                $qiuhe = 16;
                                        } elseif ($jiahe == 8) {
                                                $qiuhe = 17;
                                        } elseif ($jiahe == 9) {
                                                $qiuhe = 18;
                                        } elseif ($jiahe == 10) {
                                                $qiuhe = 19;
                                        } elseif ($jiahe == 11) {
                                                $qiuhe = 20;
                                        } else {
                                                $qiuhe = 21;
                                        }


                                        M('shaizi')->where(array('id' => $_POST['id']))->save($data);
                                        $shaizilist = M('shaizilist')->where(array('sid' => $_POST['id'], 'status' => 1))->select();
                                        foreach ($shaizilist as $k => $v) {
                                                if ($v['touzhu'] == 1 || $v['touzhu'] == 2) {
                                                        if ($v['touzhu'] == $daxiao) {
//中奖
                                                                $zhongjiang = 1;
                                                        } else {
//不中奖
                                                                $zhongjiang = 2;
                                                        }
                                                } elseif ($v['touzhu'] == 3 || $v['touzhu'] == 4) {
                                                        if ($v['touzhu'] == $danshuang) {
//中奖
                                                                $zhongjiang = 1;
                                                        } else {
//不中奖
                                                                $zhongjiang = 2;
                                                        }
                                                } elseif ($v['touzhu'] >= 5 && $v['touzhu'] <= 10) {
                                                        if ($v['touzhu'] == $maidui) {
                                                                $zhongjiang = 1;
                                                        } else {
//不中奖
                                                                $zhongjiang = 2;
                                                        }
                                                } elseif ($v['touzhu'] >= 11 && $v['touzhu'] <= 21) {
                                                        if ($v['touzhu'] == $qiuhe) {
                                                                $zhongjiang = 1;
                                                        } else {
//不中奖
                                                                $zhongjiang = 2;
                                                        }
                                                }
                                                $save = array(
                                                        'utime' => date('Y-m-d H:i:s'),
                                                        'shai1' => $data['shai1'],
                                                        'shai2' => $data['shai2'],
                                                );
                                                if ($zhongjiang == 1) {
                                                        $jiang = $v['money'] * $v['pei'];
                                                        $save['status'] = 3;
                                                        if ($v['qianbao'] == 1) {
                                                                M('member')->where(array('email' => $v['email']))->setInc('benjin', $jiang);
                                                        } elseif ($v['qianbao'] == 2) {
                                                                M('member')->where(array('email' => $v['email']))->setInc('fenhong', $jiang);
                                                        } elseif ($v['qianbao'] == 3) {
                                                                M('member')->where(array('email' => $v['email']))->setInc('jiangjin', $jiang);
                                                        } elseif ($v['qianbao'] == 4) {
                                                                M('member')->where(array('email' => $v['email']))->setInc('tiyanjin', $jiang);
                                                        }
                                                } else {
                                                        $save['status'] = 2;
                                                }
                                                M('shaizilist')->where(array('id' => $v['id']))->save($save);
                                        }
                                } else {
                                        M('shaizi')->where(array('id' => $_POST['id']))->save($data);
                                }
                        } else {
                                $data = array(
                                        'stime' => $_POST['stime'],
                                        'etime' => $_POST['etime'],
                                );
                                if ($_POST['shai1'] != null && $_POST['shai2'] != null) {
                                        $data['shai1'] = $_POST['shai1'];
                                        $data['shai2'] = $_POST['shai2'];
                                }
                                M('shaizi')->add($data);
                        }
                        $this->redirect('Admin/Main/sz', array("p" => I('p')), 0, '页面跳转中...');
                } else {

                        if (I("id") != "") {
                                $shaizi = M('shaizi')->where(array('id' => I("id")))->find();
                                $this->assign('News', $shaizi);
                        }
                        $this->display();
                }
        }

        public function recomNews() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (changeRecom("News", I("id"), I("recom"))) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/news', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function statusNews() {
                if (changeStatus("News", I("id"), I("status"))) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/news', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function deleteNews() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $id = I("id");
                if (del("News", $id)) {
                        $this->success("加入回收站 成功");
                } else {
                        $this->error("加入回收站 失败");
                }
        }

        /*         * 信息中心* */

        public function information() {
                $this->model = session("model");
                session("model", null);
                $count = M("Information")->where(array("delete" => 0))->count();
                $Page = new \Think\Page($count, 10);
                $show = $Page->show();
                $list = M("Information")->where(array("delete" => 0))->order('id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('InformationList', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->display();
        }

        public function addInformation() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (IS_POST) {
                        $AddInformation = D("AddInformation");
                        if (session("informationid")) {
                                $_POST["id"] = session("informationid");
                        }
                        if (!$AddInformation->create()) {
                                if (session("informationid")) {
                                        $this->error($AddInformation->getError(), U('Admin/Main/addInformation', array("id" => session("informationid"))));
                                } else {
                                        $this->error($AddInformation->getError(), U('Admin/Main/addInformation'));
                                }
                        } else {
                                if (session("informationid")) {
                                        session("informationid", null);
                                        $result = $AddInformation->save();
                                        if ($result || $result == 0) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                } else {
                                        if ($AddInformation->add()) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                }
                                $this->redirect('Admin/Main/information', array("p" => I('p')), 0, '页面跳转中...');
                        }
                } else {
                        if (I("id") != "") {
//有id  进入修改
                                session("informationid", I("id"));
                                if ($Information = M("Information")->where(array("id" => I('id'), "delete" => 0))->find()) {
                                        $this->Information = $Information;
                                }
                        } else {
                                session("informationid", null);
                        }
                        $this->p = I("p");
                        $this->display();
                }
        }

        public function recomInformation() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (changeRecom("Information", I("id"), I("recom"))) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/information', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function statusInformation() {
                if (changeStatus("Information", I("id"), I("status"))) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/information', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function deleteInformation() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $id = I("id");
                if (del("Information", $id)) {
                        $this->success("加入回收站 成功");
                } else {
                        $this->error("加入回收站 失败");
                }
        }

//会员管理
        public function member() {

                if (session("setmemberid") && session("model") == 2) {
                        $member_id = session("setmemberid");
                        $PriceBInfo = M("PriceBInfo")->where(array("delete" => 0, "member_id" => $member_id))->limit(1)->order("id desc")->select();
                        $this->PriceBInfo = $PriceBInfo[0];
                        $PriceInfo = M("PriceInfo")->where(array("delete" => 0, "member_id" => $member_id))->limit(1)->order("id desc")->select();
                        $this->PriceInfo = $PriceInfo[0];
                        $DigitCoinInfo = M("DigitCoinInfo")->where(array("member_id" => $member_id))->limit(1)->order("id desc")->select();
                        $this->cold_digit_coin = number_format($DigitCoinInfo[0]['cold_digit_coin'], 2, '.', '');
                        $this->free_digit_coin = number_format($DigitCoinInfo[0]['free_digit_coin'], 2, '.', '');
                }
                $this->model = session("model");
                session("model", null);
                $where = array("delete" => 0);
                $status = I("status");
                $title = urldecode(I("text"));
                if ($status != "") {
                        $where['status'] = $status;
                        $this->status = $status;
                }
                if ($title != "") {
                        $where["email"] = array("like", "%" . $title . "%");
                        $this->text = $title;
                }
                $page = 20;
                $p = I("p", 1, "int");
                $list = D('MemberRelation')->where($where)->order('id desc')->page($p . ',' . $page)->relation(true)->select();
                $this->assign('Member', $list); // 赋值数据集
                $count = M('Member')->where($where)->count(); // 查询满足要求的总记录数
                $Page = new \Think\Page($count, $page); // 实例化分页类 传入总记录数和每页显示的记录数
                foreach ($where as $key => $val) {
                        $Page->parameter[$key] = urlencode($val);
                }
                $Page->parameter['text'] = $title;
                $show = $Page->show(); // 分页显示输出
                $this->assign('page', $show); // 赋值分页输出
                $this->assign("PackageType", M("PackageType")->where(array("delete" => 0, "status" => 1))->select());
                $this->p = I("p", 0);
                $this->display();
        }

        public function setMember() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (IS_POST) {
                        $SetMember = D("SetMember");
                        if (session("setmemberid")) {
                                $_POST["id"] = session("setmemberid");
                        }
                        $Member = M("Member")->where(array("id" => session("setmemberid")))->find();
                        $_POST['old_paypass'] = $Member["paypass"];
                        $_POST['old_dgcpass'] = $Member["dgcpass"];
                        if (!$SetMember->create()) {
                                if (session("setmemberid")) {
                                        $this->error($SetMember->getError(), U('Admin/Main/member'));
                                } else {
                                        $this->error($SetMember->getError(), U('Admin/Main/member'));
                                }
                        } else {
                                if (session("setmemberid")) {
                                        session("setmemberid", null);
                                        $result = $SetMember->save();
                                        if ($result || $result == 0) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                } else {
                                        $this->error("未找到该用户");
                                }
                                $this->redirect('Admin/Main/member', array("p" => I('p')), 0, '页面跳转中...');
                        }
                } else {
                        if (I("member_id") != "") {
//有id  进入修改
                                session("setmemberid", I("member_id"));
                                session("model", 2);
                        } else {
                                $this->error("没有找到该用户");
                        }
                        $this->redirect('Admin/Main/member', array("p" => I('p')), 0, '页面跳转中...');
                }
        }

//添加会员
        public function addMember() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $Admin = SESSION("AdminID");
                $id = I("id");
                if (IS_POST) {
                        $userpass = I("userpass");
                        $repass = I("repass");
                        $repaypass = I("repaypass");
                        $paypass = I("paypass");
//                        $paypass = I("repaypass");
                        $_POST['package_type'] = I("package_type");

                        if ($id != "") {
                                $_POST["id"] = $id;
                                if ($userpass == "") {
                                        $Member = M("Member")->where(array("id" => $id))->find();
                                        $_POST["userpass"] = $Member["userpass"];
                                        $_POST["repass"] = $Member["userpass"];
                                } else {
                                        $_POST["userpass"] = md5($userpass);
                                        $_POST["repass"] = md5($repass);
                                }
                                if ($paypass == "") {
                                        $Member = M("Member")->where(array("id" => $id))->find();
                                        $_POST["paypass"] = $Member["paypass"];
                                        $_POST["repaypass"] = $Member["repaypass"];
                                } else {
                                        $_POST["paypass"] = md5($paypass);
                                        $_POST["repaypass"] = md5($repaypass);
                                }
                                $femail = $_POST['femail'];

                                $tmem = M('member')->where(array('email' => $femail))->find();
                                if ($tmem) {
                                        $_POST['p_id'] = $tmem['id'];
                                        $_POST['r_id'] = $tmem['id'];
                                        $_POST['r_name'] = $tmem['r_name'];
                                        $_POST['femail'] = $femail;
                                }
                        }
                        $femail = $_POST['femail'];

                        $tmem = M('member')->where(array('email' => $femail))->find();
                        if ($tmem) {
                                $_POST['p_id'] = $tmem['id'];
                                $_POST['r_id'] = $tmem['id'];
                                $_POST['r_name'] = $tmem['r_name'];
                                $_POST['femail'] = $femail;
                        }
//                        $AddMember = D("AddMember");
//                        if (!$AddMember->create()) {
//                                session("model", 1);
//                                $this->error($AddMember->getError(), U('Admin/Main/addMember', array("id" => $id, "p" => I('p'))));
//                        } else {
                        if ($id != "") {
                                if ($_POST["bdmoney"] != '') {
                                        if ($_POST['stype'] == '1') {
                                                M("member")->where(array("id" => $id))->setInc('benjin', $_POST["bdmoney"]);
                                        } elseif ($_POST['stype'] == '2') {
                                                M("member")->where(array("id" => $id))->setDec('benjin', $_POST["bdmoney"]);
                                        } elseif ($_POST['stype'] == '3') {
                                                M("member")->where(array("id" => $id))->setInc('futou', $_POST["bdmoney"]);
                                        } elseif ($_POST['stype'] == '4') {
                                                M("member")->where(array("id" => $id))->setDec('futou', $_POST["bdmoney"]);
                                        }
                                        $mem = M('member')->where(array('id' => $id))->find();
                                        $moneybd = array(
                                                'email' => $mem['email'],
                                                'ctime' => date('Y-m-d H:i:s'),
                                                'money' => $_POST["bdmoney"],
                                                'type' => $_POST['stype']
                                        );
                                        M('moneybd')->add($moneybd);
                                }

                                $mmm = M('member')->where(array('id' => $id))->save($_POST);
                                if ($mmm !== false) {
                                        $this->success('修改成功');
                                } else {
                                        $this->error('修改失败');
                                }
//                                        $result = $AddMember->save();
//                                        if ($result || $result == 0) {
//                                                session("model", 4);
//                                        } else {
//                                                session("model", 3);
//                                        }
                        } else {
                                $_POST['adddate'] = date('Y-m-d');
                                $_POST['addtime'] = date('Y-m-d H:i:s');

                                $mmm = M('member')->add($_POST);
                                if ($mmm) {
                                        $this->success('添加成功');
                                } else {
                                        $this->error('添加失败');
                                }

//                                        if ($result = $AddMember->add()) {
//                                                session("model", 4);
//                                        } else {
//                                                session("model", 3);
//                                        }
//                                }
//                                $this->redirect('Admin/Main/member', array("p" => I('p')), 0, '页面跳转中...');
                        }
                } else {
                        if ($id != "") {
                                $this->Member = M("Member")->where(array("id" => $id, "delete" => 0))->find();
                        }
                        $package_type = M('package_type')->select();
                        $this->assign('package_type', $package_type);
                        $this->p = I("p", 0);
                        $this->Time = date("Y-m-d", time());
                        $this->display();
                }
        }

//添加会员
        public function yinhangka() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $Admin = SESSION("AdminID");
                $id = I("id");
                if (IS_POST) {
                        $bb = M("ServerBank")->where(array("member_id" => I('member_id')))->find();
                        $data = array(
                                'member_id' => I('member_id'),
                                'bank_number' => I('bank_number'),
                                'bank_username' => I('bank_username'),
                                'bank_name' => I('bank_name'),
                                'bank_child_name' => I('bank_child_name'),
                                'zhifubao' => I('zhifubao'),
                        );
                        if ($bb) {
//                               echo  1111;exit;
                                M("ServerBank")->where(array("member_id" => I('member_id')))->save($data);
                        } else {
//                                echo  2222;exit;
                                M("ServerBank")->add($data);
                        }



                        $this->success('操作成功');
                } else {
                        if ($id != "") {
                                $bank = M("ServerBank")->where(array("member_id" => $id))->find();
//                                $this->Bank = $bank;
                                $this->assign('Bank', $bank);
                        }
                        $this->assign('member_id', $id);
                        $this->p = I("p", 0);

                        $this->display();
                }
        }

//会员状态
        public function statusMember() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $result = M("Member")->where(array("id" => I("id")))->data(array("status" => I("status")))->save();
                if ($result || $result == 0) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/member', array("p" => I('p')), 0, '页面跳转中...');
        }

//删除会员
        public function deleteMember() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $id = I("id");
                if (del("Member", $id)) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/member', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function country() {
                if (session("countryid") && session("model") == 2) {
                        $Country = M("Country")->where(array("id" => session("countryid")))->find();
                        $this->cn_name = $Country["cn_name"];
                        $this->sort = $Country["sort"];
                        $this->en_name = $Country["en_name"];
                        $this->status = $Country["status"];
                } else {
                        $this->cn_name = urldecode(I("cn_name"));
                        $this->sort = urldecode(I("sort"));
                        $this->en_name = urldecode(I("en_name"));
                        $this->status = urldecode(I("status"));
                }
                $this->model = session("model");
                session("model", null);
                $count = M("Country")->where(array("delete" => 0))->count();
                $Page = new \Think\Page($count, 40);
                $show = $Page->show();
                $list = M("Country")->where(array("delete" => 0))->order('id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('CountryList', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->display();
        }

        public function addCountry() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (IS_POST) {
                        $AddCountry = D("AddCountry");
                        if (session("countryid")) {
                                $_POST["id"] = session("countryid");
                        }
                        $_POST['picture'] = UploadImg('picture', "country", $_POST["id"]);
                        if (!$AddCountry->create()) {
                                session("model", 1);
                                $this->error($AddCountry->getError(), U('Admin/Main/country', array("cn_name" => urlencode(I("cn_name")), "en_name" => urlencode(I("en_name")), "sort" => urlencode(I("sort")), "status" => urlencode(I("status")))));
                        } else {
                                if (session("countryid")) {
                                        session("countryid", null);
                                        $result = $AddCountry->save();
                                        if ($result || $result == 0) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                } else {
                                        if ($AddCountry->add()) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                }
                                $this->redirect('Admin/Main/country', array("p" => I('p')), 0, '页面跳转中...');
                        }
                } else {
                        if (I("id") != "") {
//有id  进入修改
                                session("countryid", I("id"));
                                session("model", 2);
                        } else {
                                session("countryid", null);
                                session("model", 1);
                        }
                        $this->redirect('Admin/Main/country', array("p" => I('p')), 0, '页面跳转中...');
                }
        }

        public function statusCountry() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (changeStatus("Country", I("id"), I("status"))) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/country', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function deleteCountry() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $id = I("id");
                if (del("Country", $id)) {
                        $this->success("加入回收站 成功");
                } else {
                        $this->error("加入回收站 失败");
                }
        }

        public function priceLevel() {
                if (session("pricelevelid") && session("model") == 2) {
                        $PriceLevel = M("PriceLevel")->where(array("id" => session("pricelevelid")))->find();
                        $this->title = $PriceLevel["title"];
                        $this->sort = $PriceLevel["sort"];
                        $this->price = $PriceLevel["price"];
                        $this->status = $PriceLevel["status"];
                        $this->package_id = $PriceLevel["package_id"];
                } else {
                        $this->title = urldecode(I("title"));
                        $this->sort = urldecode(I("sort"));
                        $this->price = urldecode(I("price"));
                        $this->status = urldecode(I("status"));
                        $this->package_id = urldecode(I("package_id"));
                }
                $this->model = session("model");
                session("model", null);
                $count = M("PriceLevel")->where(array("delete" => 0))->count();
                $Page = new \Think\Page($count, 40);
                $show = $Page->show();
                $list = M("PriceLevel")->where(array("delete" => 0))->order('id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('PriceLevel', $list);
                $this->assign('page', $show);
                $this->PackageList = M("PackageType")->where(array("delete" => 0))->select();
                $this->p = I("p", 0);
                $this->display();
        }

        public function addPriceLevel() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (IS_POST) {
                        $AddPriceLevel = D("AddPriceLevel");
                        if (session("pricelevelid")) {
                                $_POST["id"] = session("pricelevelid");
                        }
                        $PackageType = M("PackageType")->where(array("delete" => 0, "id" => I("package_id")))->find();
                        $_POST['package_id'] = $PackageType["id"];
                        $_POST['package_price'] = $PackageType["price"];
                        $_POST['package_title'] = $PackageType["title"];
                        if (!$AddPriceLevel->create()) {
                                session("model", 1);
                                $this->error($AddPriceLevel->getError(), U('Admin/Main/priceLevel', array("title" => urlencode(I("title")), "sort" => urlencode(I("sort")), "price" => urlencode(I("price")), "status" => urlencode(I("status")), "p" => I("p"))));
                        } else {
                                if (session("pricelevelid")) {
                                        session("pricelevelid", null);
                                        $result = $AddPriceLevel->save();
                                        if ($result || $result == 0) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                } else {
                                        if ($AddPriceLevel->add()) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                }
                                $this->redirect('Admin/Main/priceLevel', array("p" => I('p')), 0, '页面跳转中...');
                        }
                } else {
                        if (I("id") != "") {
//有id  进入修改
                                session("pricelevelid", I("id"));
                                session("model", 2);
                        } else {
                                session("pricelevelid", null);
                                session("model", 1);
                        }
                        $this->redirect('Admin/Main/priceLevel', array("p" => I('p')), 0, '页面跳转中...');
                }
        }

        public function statusPriceLevel() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (changeStatus("PriceLevel", I("id"), I("status"))) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/priceLevel', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function deletePriceLevel() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $id = I("id");
                if (del("PriceLevel", $id)) {
                        $this->success("加入回收站 成功");
                } else {
                        $this->error("加入回收站 失败");
                }
        }

        public function cash_wallet() {
                $where = array("delete" => 0);
                $where["_string"] = "type in (1,3,5,7,8)";
                $starttime = I("starttime");
                $endtime = I("endtime");
                if ($starttime != "" && $endtime != "") {
                        if ($starttime > $endtime) {
                                $this->error("开始时间不能大于结算时间");
                        }
                        if ($starttime == $endtime) {
                                $where["_string"] = "adddate = '" . $starttime . "'";
                        } else {
                                $where["_string"] = "adddate between '" . $starttime . "' and '" . $endtime . "'";
                        }
                        $this->starttime = $starttime;
                        $this->endtime = $endtime;
                }
                $page_set = I("page_set", 10);
                $count = M("PriceInfo")->where($where)->order("id desc")->count();
                $Page = new \Think\Page($count, $page_set);
                foreach ($where as $key => $val) {
                        $Page->parameter[$key] = urlencode($val);
                }
                if ($starttime != "") {
                        $Page->parameter["starttime"] = urlencode($starttime);
                }
                if ($endtime != "") {
                        $Page->parameter["endtime"] = urlencode($endtime);
                }
                $show = $Page->show();
                $list = M("PriceInfo")->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('PriceInfoList', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->MemberList = M("Member")->where(array("delete" => 0))->select();
                $this->page_set = I("page_set", 10);
                $this->display();
        }

        public function bcash_wallet() {
                $where = array("delete" => 0);
                $where["_string"] = "type in (2,4,6,7,8)";
                $starttime = I("starttime");
                $endtime = I("endtime");
                if ($starttime != "" && $endtime != "") {
                        if ($starttime > $endtime) {
                                $this->error("开始时间不能大于结算时间");
                        }
                        if ($starttime == $endtime) {
                                $where["_string"] = "adddate = '" . $starttime . "'";
                        } else {
                                $where["_string"] = "adddate between '" . $starttime . "' and '" . $endtime . "'";
                        }
                        $this->starttime = $starttime;
                        $this->endtime = $endtime;
                }
                $page_set = I("page_set", 10);
                $count = M("PriceInfo")->where($where)->order("id desc")->count();
                $Page = new \Think\Page($count, $page_set);
                foreach ($where as $key => $val) {
                        $Page->parameter[$key] = urlencode($val);
                }
                if ($starttime != "") {
                        $Page->parameter["starttime"] = urlencode($starttime);
                }
                if ($endtime != "") {
                        $Page->parameter["endtime"] = urlencode($endtime);
                }
                $show = $Page->show();
                $list = M("PriceInfo")->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('PriceInfoList', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->MemberList = M("Member")->where(array("delete" => 0))->select();
                $this->page_set = I("page_set", 10);
                $this->display();
        }

        public function digitPrice() {
                if (session("digitpriceid") && session("model") == 2) {
                        $DigitPrice = M("DigitPrice")->where(array("id" => session("digitpriceid")))->find();
                        $this->price = $DigitPrice["price"];
                        $this->date = $DigitPrice["date"];
                        $this->status = $DigitPrice["status"];
                        $this->open = $DigitPrice["open"];
                        $this->low = $DigitPrice["low"];
                        $this->high = $DigitPrice["high"];
                        $this->close = $DigitPrice["close"];
                        $this->volume = $DigitPrice["volume"];
                } else {
                        $this->price = urldecode(I("price"));
                        $this->date = urldecode(I("date"));
                        $this->status = urldecode(I("status"));
                        $this->open = urldecode(I("open"));
                        $this->low = urldecode(I("low"));
                        $this->high = urldecode(I("high"));
                        $this->close = urldecode(I("close"));
                        $this->volume = urldecode(I("volume"));
                }
                $this->model = session("model");
                session("model", null);
                $count = M("DigitPrice")->where(array("delete" => 0))->count();
                $Page = new \Think\Page($count, 20);
                $show = $Page->show();
                $list = M("DigitPrice")->where(array("delete" => 0))->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('DigitPriceList', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->display();
        }

        public function addDigitPrice() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (IS_POST) {
                        $AddDigitPrice = D("AddDigitPrice");
                        if (session("digitpriceid")) {
                                $_POST["id"] = session("digitpriceid");
                        }
                        if (!$AddDigitPrice->create()) {
                                session("model", 1);
                                $this->error($AddDigitPrice->getError(), U('Admin/Main/digitPrice', array("price" => urlencode(I("price")), "date" => urlencode(I("date")))));
                        } else {
                                if (session("digitpriceid")) {
                                        session("digitpriceid", null);
                                        $result = $AddDigitPrice->save();
                                        if ($result || $result == 0) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                } else {
                                        if ($AddDigitPrice->add()) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                }
                                $this->redirect('Admin/Main/digitPrice', array("p" => I('p')), 0, '页面跳转中...');
                        }
                } else {
                        if (I("id") != "") {
//有id  进入修改
                                session("digitpriceid", I("id"));
                                session("model", 2);
                        } else {
                                session("digitpriceid", null);
                                session("model", 1);
                        }
                        $this->redirect('Admin/Main/digitPrice', array("p" => I('p')), 0, '页面跳转中...');
                }
        }

        public function statusDigitPrice() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (changeStatus("DigitPrice", I("id"), I("status"))) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/digitPrice', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function deleteDigitPrice() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $id = I("id");
                if (del("DigitPrice", $id)) {
                        $this->success("加入回收站 成功");
                } else {
                        $this->error("加入回收站 失败");
                }
        }

//cash 存款
        public function cash() {
                if (session("cashid") && session("model") == 2) {
                        $Cash = M("Cash")->where(array("id" => session("cashid")))->find();
                        $this->content = $Cash["content"];
                        $this->remark = $Cash["remark"];
                        $this->price = $Cash["price"];
                        $this->recom = $Cash["recom"];
                        $this->status = $Cash["status"];
                } else {
                        $this->content = urldecode(I("content"));
                        $this->remark = urldecode(I("remark"));
                        $this->price = urldecode(I("price"));
                        $this->recom = urldecode(I("recom"));
                        $this->status = urldecode(I("status"));
                }
                $this->model = session("model");
                session("model", null);
                $count = M("Cash")->where(array("delete" => 0))->count();
                $Page = new \Think\Page($count, 40);
                $show = $Page->show();
                $list = M("Cash")->where(array("delete" => 0))->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('CashList', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->display();
        }

        public function addCash() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (IS_POST) {
                        $Cash = D("Cash");
                        if (session("cashid")) {
                                $_POST["id"] = session("cashid");
                                $CashFind = M("Cash")->where(array("id" => $_POST["id"]))->find();
                                if (M("PriceInfo")->where(array("cash_id" => $CashFind['id']))->find()) {
                                        $this->error("审核已通过，无法继续添加S币", U("Admin/Main/cash"));
                                }
                        } else {
                                $this->error("无数据");
                        }
                        if (!$Cash->create()) {
                                session("model", 1);
                                $this->error($Cash->getError(), U('Admin/Main/cash', array("remark" => urlencode(I("remark")), "content" => urlencode(I("content")))));
                        } else {
                                if (session("cashid")) {
                                        session("cashid", null);
                                        $result = $Cash->save();
                                        if ($result || $result == 0) {
                                                if (I("status") == 1) {
                                                        $Admin = SESSION("AdminID");
                                                        if (!$Member = M("Member")->where(array("id" => $CashFind['member_id'], "delete" => 0))->find()) {
                                                                $this->error("未找到该会员");
                                                        } else {
                                                                $PriceInfo = M("PriceInfo")->where(array("delete" => 0, "member_id" => $Member['id']))->order("id desc")->limit(1)->select();
                                                        }
                                                        $data = array(
                                                                "member_username" => $CashFind['member_username'],
                                                                "title" => "The Corporate deposit",
                                                                "remark" => "",
                                                                "type" => 1,
                                                                "member_id" => $CashFind['member_id'],
                                                                "admin_id" => $Admin["id"],
                                                                "s_price" => $CashFind['price'] + $PriceInfo[0]['s_price'],
                                                                "b_price" => $PriceInfo[0]['b_price'],
                                                                "cash_id" => $CashFind["id"],
                                                                "price" => $CashFind['price'],
                                                                "adddate" => date("Y-m-d", time()),
                                                                "addtime" => date("Y-m-d H:i:s", time()),
                                                        );
                                                        M("PriceInfo")->data($data)->add();
                                                }
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                } else {
                                        if ($Cash->add()) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                }
                                $this->redirect('Admin/Main/cash', array("p" => I('p')), 0, '页面跳转中...');
                        }
                } else {
                        if (I("id") != "") {
//有id  进入修改
                                session("cashid", I("id"));
                                session("model", 2);
                        } else {
                                session("cashid", null);
                                session("model", 1);
                        }
                        $this->redirect('Admin/Main/cash', array("p" => I('p')), 0, '页面跳转中...');
                }
        }

        public function recomCash() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (changeRecom("Cash", I("id"), I("recom"))) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/cash', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function statusCash() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $Cash = M("Cash")->where(array("id" => I("id")))->find();
                if (M("PriceInfo")->where(array("cash_id" => $Cash['id']))->find()) {
                        $this->error("审核已通过，无法继续添加S币", U("Admin/Main/cash"));
                }
                if (changeStatus("Cash", I("id"), I("status"))) {
                        $Admin = SESSION("AdminID");

                        if (!$Member = M("Member")->where(array("id" => $Cash['member_id'], "delete" => 0))->find()) {
                                $this->error("未找到该会员");
                        } else {
                                $PriceInfo = M("PriceInfo")->where(array("delete" => 0, "member_id" => $Member['id']))->order("id desc")->limit(1)->select();
                        }
                        $data = array(
                                "member_username" => $Cash['member_username'],
                                "title" => "The Corporate deposit",
                                "remark" => "",
                                "type" => 1,
                                "member_id" => $Cash['member_id'],
                                "admin_id" => $Admin["id"],
                                "s_price" => $Cash['price'] + $PriceInfo[0]['s_price'],
                                "b_price" => $PriceInfo[0]['b_price'],
                                "cash_id" => $Cash["id"],
                                "price" => $Cash['price'],
                                "adddate" => date("Y-m-d", time()),
                                "addtime" => date("Y-m-d H:i:s", time()),
                        );
                        M("PriceInfo")->data($data)->add();
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/cash', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function deleteCash() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $id = I("id");
                if (del("Cash", $id)) {
                        $this->success("加入回收站 成功");
                } else {
                        $this->error("加入回收站 失败");
                }
        }

        public function split_digit() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $Admin = SESSION("AdminID");
                $list = M("DigitCoinInfo")->field("max(id) as id")->where(array("delete" => 0))->group("member_id")->select();
                $count = M("Split")->where(array("delete" => 0))->count();
                foreach ($list as $val) {
                        $DigitCoinInfo = M("DigitCoinInfo")->where(array("id" => $val['id']))->find();
                        if ($DigitCoinInfo['adddate'] == date("Y-m-d", time())) {
//当用户登陆时间 等于今日是添加拆分，否则不添加
                                $data = array(
                                        'type' => 3,
                                        'member_username' => $DigitCoinInfo['member_username'],
                                        'member_id' => $DigitCoinInfo['member_id'],
                                        'cold_digit_coin' => $DigitCoinInfo['cold_digit_coin'] * 2,
                                        'cold_out_digit_coin' => 0,
                                        'cold_in_digit_coin' => $DigitCoinInfo['cold_digit_coin'],
                                        'free_digit_coin' => $DigitCoinInfo['free_digit_coin'] * 2,
                                        'free_in_digit_coin' => 0,
                                        'title' => "Split Limincoin(Escrow)",
                                        'remark' => ($count + 1),
                                        'adddate' => date("Y-m-d H:i:s", time()),
                                        'addtime' => date("Y-m-d H:i:s", time())
                                );
                                if (!M("DigitCoinInfo")->data($data)->add()) {
                                        session("model", 3);
                                } else {
                                        $Digit = M("DigitCoinInfo")->where(array("type" => 2, "member_id" => $val['id']))->limit(1)->order("id desc")->select();
                                        if (count($Digit) > 0) {
                                                $Digit[0]["cold_digit_coin"] = $Digit[0]['cold_digit_coin'] * 2;
                                                M("DigitCoinInfo")->data($Digit)->save();
                                        }
                                }
                        }
                }
                if (M("Split")->data(array("admin_id" => $Admin['id'], "admin_name" => $Admin['realname'], "remark" => ($count + 1), "adddate" => date("Y-m-d", time()), "addtime" => date("Y-m-d H:i:s", time())))->add()) {
                        session("model", 4);
                } else {
                        $this->error("拆分成功，但未记录", U("Admin/Main/split"));
                }
                $this->redirect('Admin/Main/split', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function split() {
                $this->model = session("model");
                session("model", null);
                $count = M("Split")->where(array("delete" => 0))->count();
                $Page = new \Think\Page($count, 40);
                $show = $Page->show();
                $list = M("Split")->where(array("delete" => 0))->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('SplitList', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->count = $count;
                $this->display();
        }

        public function deleteSplit() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $id = I("id");
                if (del("Split", $id)) {
                        $this->success("加入回收站 成功");
                } else {
                        $this->error("加入回收站 失败");
                }
        }

        public function coin_info() {
                if (session("coininfoid") && session("model") == 2) {
                        $CoinInfo = M("CoinInfo")->where(array("id" => session("coininfoid")))->find();
                        $this->number = $CoinInfo["number"];
                        $this->title = $CoinInfo["title"];
                        $this->status = $CoinInfo["status"];
                } else {
                        $this->number = urldecode(I("number"));
                        $this->title = urldecode(I("title"));
                        $this->status = urldecode(I("status"));
                }
                $this->model = session("model");
                session("model", null);
                $count = M("CoinInfo")->where(array("delete" => 0))->count();
                $Page = new \Think\Page($count, 20);
                $show = $Page->show();
                $list = M("CoinInfo")->where(array("delete" => 0))->order('id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('CoinInfo', $list);
                $this->assign('page', $show);
                $this->p = I("p", 0);
                $this->display();
        }

        public function addCoinInfo() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (IS_POST) {
                        $number = I("number", 0, "int");
                        if ($number > 100 && $number >= 0) {
                                $this->error("趋势在0-100之间");
                        }
                        $CoinInfo = D("CoinInfo");
                        if (session("coininfoid")) {
                                $_POST["id"] = session("coininfoid");
                        }

                        if (!$CoinInfo->create()) {
                                session("model", 1);
                                $this->error($CoinInfo->getError(), U('Admin/Main/coin_info', array("title" => urlencode(I("title")), "number" => urlencode(I("number")), "status" => urlencode(I("status")), "p" => I("p"))));
                        } else {
                                if (session("coininfoid")) {
                                        session("coininfoid", null);
                                        $result = $CoinInfo->save();
                                        if ($result || $result == 0) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                } else {
                                        if ($CoinInfo->add()) {
                                                session("model", 4);
                                        } else {
                                                session("model", 3);
                                        }
                                }
                                $this->redirect('Admin/Main/coin_info', array("p" => I('p')), 0, '页面跳转中...');
                        }
                } else {
                        if (I("id") != "") {
//有id  进入修改
                                session("coininfoid", I("id"));
                                session("model", 2);
                        } else {
                                session("coininfoid", null);
                                session("model", 1);
                        }
                        $this->redirect('Admin/Main/coin_info', array("p" => I('p', 0)), 0, '页面跳转中...');
                }
        }

        public function statusCoinInfo() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                if (changeStatus("CoinInfo", I("id"), I("status"))) {
                        session("model", 4);
                } else {
                        session("model", 3);
                }
                $this->redirect('Admin/Main/coin_info', array("p" => I('p')), 0, '页面跳转中...');
        }

        public function deleteCoinInfo() {
                $admin_manager = SESSION("AdminID");
                if ($admin_manager['id'] > 1) {
                        $this->error('您没有相关权限');
                }
                $id = I("id");
                if (del("CoinInfo", $id)) {
                        $this->success("加入回收站 成功");
                } else {
                        $this->error("加入回收站 失败");
                }
        }

        public function changgui() {
//                echo dirname(APP_PATH);exit;
                $settings = include( CONF_PATH . 'settings.php' );

                if (IS_POST) {

                        foreach ($settings as $k => $v) {
                                if (isset($_POST[$k])) {
                                        $settings[$k] = $_POST[$k];
                                }
                        }
                        $file_length = file_put_contents(CONF_PATH . 'settings.php', '<?php return ' . var_export($settings, true) . '; ?>');
                        $bilog = array(
                                'dian' => $_POST['gamerate'],
                                'date' => date('Y-m-d H:i:s'),
                                'type' => 1
                        );
                        M('bilog')->add($bilog);

                        if ($file_length) {
                                $this->success('保存成功！');
                        } else {
                                $this->error('保存失败！请检查文件权限');
                        }
                        return;
                }

                foreach ($settings as $k => $v) {

                        $this->assign($k, $v);
                }

                $this->assign('settings', $settings);
                $this->display();
//	$this->display( $tempsl );
        }

        public function choujiang() {
//                echo dirname(APP_PATH);exit;
                $settings = include( CONF_PATH . 'settings.php' );

                if (IS_POST) {

                        foreach ($settings as $k => $v) {
                                if (isset($_POST[$k])) {
                                        $settings[$k] = $_POST[$k];
                                }
                        }
                        $file_length = file_put_contents(CONF_PATH . 'settings.php', '<?php return ' . var_export($settings, true) . '; ?>');
                        $bilog = array(
                                'dian' => $_POST['gamerate'],
                                'date' => date('Y-m-d H:i:s'),
                                'type' => 1
                        );
                        M('bilog')->add($bilog);

                        if ($file_length) {
                                $this->success('保存成功！');
                        } else {
                                $this->error('保存失败！请检查文件权限');
                        }
                        return;
                }

                foreach ($settings as $k => $v) {

                        $this->assign($k, $v);
                }

                $this->assign('settings', $settings);
                $this->display();
//	$this->display( $tempsl );
        }

        public function shaizi() {
//                echo dirname(APP_PATH);exit;
                $settings = include( CONF_PATH . 'settings.php' );

                if (IS_POST) {

                        foreach ($settings as $k => $v) {
                                if (isset($_POST[$k])) {
                                        $settings[$k] = $_POST[$k];
                                }
                        }
                        $file_length = file_put_contents(CONF_PATH . 'settings.php', '<?php return ' . var_export($settings, true) . '; ?>');
                        $bilog = array(
                                'dian' => $_POST['gamerate'],
                                'date' => date('Y-m-d H:i:s'),
                                'type' => 1
                        );
                        M('bilog')->add($bilog);

                        if ($file_length) {
                                $this->success('保存成功！');
                        } else {
                                $this->error('保存失败！请检查文件权限');
                        }
                        return;
                }

                foreach ($settings as $k => $v) {

                        $this->assign($k, $v);
                }

                $this->assign('settings', $settings);
                $this->display();
//	$this->display( $tempsl );
        }

        public function pan1() {
//                echo dirname(APP_PATH);exit;
                $settings = include( CONF_PATH . 'settings.php' );

                if (IS_POST) {

                        foreach ($settings as $k => $v) {
                                if (isset($_POST[$k])) {
                                        $settings[$k] = $_POST[$k];
                                }
                        }
                        $file_length = file_put_contents(CONF_PATH . 'settings.php', '<?php return ' . var_export($settings, true) . '; ?>');

                        if ($file_length) {
                                $this->success('保存成功！');
                        } else {
                                $this->error('保存失败！请检查文件权限');
                        }
                        return;
                }

                foreach ($settings as $k => $v) {

                        $this->assign($k, $v);
                }

                $this->assign('settings', $settings);
                $this->display();
//	$this->display( $tempsl );
        }

}
