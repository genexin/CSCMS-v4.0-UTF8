<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-03-30
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pay extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjTpl');
		    $this->load->model('CsdjUser');
			$this->CsdjUser->User_Login();
			$this->load->helper('string');
			$this->lang->load('user');
	}

    //充值
	public function index()
	{
			//模板
			$tpl='pay.html';
			//URL地址
		    $url='pay/index';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=L('pay_01');
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,1,$tpl,$title,'','',$ids,true,false);
			//token
			$token=random_string('alnum',10);
			$_SESSION['token']=$token;
			$Mark_Text=str_replace("[user:token]",$token,$Mark_Text);
			//充值提交地址
			$Mark_Text=str_replace("[user:paysave]",spacelink('pay/save'),$Mark_Text);
			$Mark_Text=str_replace("[user:alipay]",CS_Alipay,$Mark_Text);//支付宝开关
			$Mark_Text=str_replace("[user:tenpay]",CS_Tenpay,$Mark_Text);//财付通开关
			$Mark_Text=str_replace("[user:wypay]",CS_Wypay,$Mark_Text);//网银在线开关
			$Mark_Text=str_replace("[user:kqpay]",CS_Kqpay,$Mark_Text);//快钱开关
			$Mark_Text=str_replace("[user:ybpay]",CS_Ybpay,$Mark_Text);//易宝支付开关
			$Mark_Text=str_replace("[user:cspay]",CS_Cspay,$Mark_Text);//官方支付开关
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

    //充值订单
	public function save()
	{
			$token=$this->input->post('token', TRUE);
			if($token!=$_SESSION['token']) msg_url(L('pay_06'),'javascript:history.back();');

			$rmb=intval($this->input->post('rmb'));  //充值金额
			$type=$this->input->post('type',true,true);  //充值方式
			if($rmb<1 || $rmb>99999){
                  msg_url(L('pay_02'),'javascript:history.back();');
			}
			if(empty($type)){
                  msg_url(L('pay_03'),'javascript:history.back();');
			}
			//记录订单
			$add['dingdan'] = date('Ymd').time().random_string('numeric',5);
			$add['type'] = $type;
			$add['rmb'] = $rmb;
			$add['uid'] = $_SESSION['cscms__id'];
			$add['ip'] = getip();
			$add['addtime'] = time();
			$ids=$this->CsdjDB->get_insert('pay',$add);
			if($ids){
				  //转到对应支付平台
				  exit("<script>window.location='".site_url('pay/'.$type.'/index/'.$ids)."';</script>");
			}else{
                  msg_url(L('pay_04'),'javascript:history.back();');
			}
	}

    //充值卡充值
	public function card()
	{
			//模板
			$tpl='pay-card.html';
			//URL地址
		    $url='pay/card';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=L('pay_05');
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,1,$tpl,$title,'','',$ids,true,false);
			//token
			$token=random_string('alnum',10);
			$_SESSION['token']=$token;
			$Mark_Text=str_replace("[user:token]",$token,$Mark_Text);
			//充值提交地址
			$Mark_Text=str_replace("[user:cardsave]",spacelink('pay/cardsave'),$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

    //充值卡充值
	public function cardsave()
	{
			$token=$this->input->post('token', TRUE);
			if($token!=$_SESSION['token']) msg_url(L('pay_06'),'javascript:history.back();');

			$card=$this->input->post('card',true,true);  //卡号
			$pass=$this->input->post('pass',true,true);  //卡密
			if(empty($card) || empty($pass)){
                 msg_url(L('pay_07'),'javascript:history.back();');
			}
			//判断充值卡是否存在
			$row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."paycard where card='".$card."' and pass='".$pass."'")->row();
			if(!$row){
                 msg_url(L('pay_08'),'javascript:history.back();');
			}
			if($row->uid>0){
                 msg_url(L('pay_09'),'javascript:history.back();');
			}
			$edit['uid']=$_SESSION['cscms__id'];
			$edit['usertime']=time();
            $this->CsdjDB->get_update('paycard',$row->id,$edit);
			//增加金钱
			$this->db->query("update ".CS_SqlPrefix."user set rmb=rmb+".$row->rmb." where id=".$_SESSION['cscms__id']."");
			//发送通知
			$add['uida']=$_SESSION['cscms__id'];
			$add['uidb']=0;
			$add['name']=L('pay_10');
			$add['neir']=L('pay_11',array($row->rmb));
			$add['addtime']=time();
            $this->CsdjDB->get_insert('msg',$add);
            msg_url(L('pay_12',array($row->rmb)),spacelink('pay/card'));
	}

    //升级
	public function group()
	{
			//模板
			$tpl='group.html';
			//URL地址
		    $url='pay/upgrade';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=L('pay_13');
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,1,$tpl,$title,'','',$ids,true,false);
			//token
			$token=random_string('alnum',10);
			$_SESSION['token']=$token;
			$Mark_Text=str_replace("[user:token]",$token,$Mark_Text);
			//充值提交地址
			$Mark_Text=str_replace("[user:groupsave]",spacelink('pay/groupsave'),$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

    //升级会员组
	public function groupsave()
	{
			$token=$this->input->post('token', TRUE);
			if($token!=$_SESSION['token']) msg_url(L('pay_06'),'javascript:history.back();');

			$zid=intval($this->input->post('zid'));  //组ID
			$type=intval($this->input->post('type'));  //方式
			$times=intval($this->input->post('times')); //时长
			if($zid==0 || $type==0 || $times<1 || $times>99999){
                 msg_url(L('pay_14'),'javascript:history.back();');
			}
			$row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."userzu where id=".$zid."")->row();
			if(!$row){
                 msg_url(L('pay_15'),'javascript:history.back();');
			}
			if($type==3){ //包年
                 $cion=$row->cion_y;
				 $zutime=time()+86400*365*$times;
			}elseif($type==2){ //包月
                 $cion=$row->cion_m;
				 $zutime=time()+86400*30*$times;
			}else{ //包天
                 $cion=$row->cion_d;
				 $zutime=time()+86400*$times;
			}
			//总金币
			$zcion=$cion*$times;
			//判断金币是否够
			$ucion=getzd('user','cion',$_SESSION['cscms__id']);
			if($ucion < $zcion){
                 msg_url(L('pay_16'),'javascript:history.back();');
			}
            //修改入库
			$this->db->query("update ".CS_SqlPrefix."user set cion=cion-".$zcion.",zid=".$zid.",zutime=".$zutime." where id=".$_SESSION['cscms__id']."");
			//发送通知
			$add['uida']=$_SESSION['cscms__id'];
			$add['uidb']=0;
			$add['name']=L('pay_17');
			$add['neir']=L('pay_18',array($row->name));
			$add['addtime']=time();
            $this->CsdjDB->get_insert('msg',$add);
            msg_url(L('pay_19',array($row->name)),spacelink('pay/group'));
	}

    //兑换
	public function change()
	{
			//模板
			$tpl='change.html';
			//URL地址
		    $url='pay/change';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=L('pay_20');
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,1,$tpl,$title,'','',$ids,true,false);
			//token
			$token=random_string('alnum',10);
			$_SESSION['token']=$token;
			$Mark_Text=str_replace("[user:token]",$token,$Mark_Text);
			//提交地址
			$Mark_Text=str_replace("[user:changesave]",spacelink('pay/changesave'),$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

    //兑换金币
	public function changesave()
	{
			$token=$this->input->post('token', TRUE);
			if($token!=$_SESSION['token']) msg_url(L('pay_06'),'javascript:history.back();');

			$rmb=intval($this->input->post('rmb'));
			if($rmb<1 || $rmb>99999){
                 msg_url(L('pay_21'),'javascript:history.back();');
			}
			//判断余额是否够
			$urmb=getzd('user','rmb',$_SESSION['cscms__id']);
			if($urmb < $rmb){
                 msg_url(L('pay_22',array($rmb)),'javascript:history.back();');
			}
			$cion=$rmb*User_RmbToCion;
            //修改入库
			$this->db->query("update ".CS_SqlPrefix."user set rmb=rmb-".$rmb.",cion=cion+".$cion." where id=".$_SESSION['cscms__id']."");
			//写入消费记录
			$add2['title']=L('pay_23',array($cion));
			$add2['uid']=$_SESSION['cscms__id'];
			$add2['dir']='user';
			$add2['nums']=$rmb;
			$add2['sid']=1;
			$add2['ip']=getip();
            $add2['addtime']=time();
			$this->CsdjDB->get_insert('spend',$add2);
			//发送通知
			$add['uida']=$_SESSION['cscms__id'];
			$add['uidb']=0;
			$add['name']=L('pay_24');
			$add['neir']=L('pay_25',array($rmb,$cion));
			$add['addtime']=time();
            $this->CsdjDB->get_insert('msg',$add);
            msg_url(L('pay_26',array($cion)),spacelink('pay/change'));
	}

    //充值记录
	public function lists()
	{
		    $pid=intval($this->uri->segment(4)); //分页
		    $page=intval($this->uri->segment(5)); //分页
			//模板
			$tpl='pay-list.html';
			//URL地址
		    $url='pay/lists/'.$pid;
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=L('pay_27');
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
			$sqlstr = "select * from ".CS_SqlPrefix."pay where uid=".$_SESSION['cscms__id'];
            if($pid>0){
				$pids=($pid>3)?0:$pid;
			    $sqlstr.=" and pid=".$pids;
			}
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,$pid,$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[pay:pid]",$pid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

    //消费记录
	public function spend()
	{
		    $sid=intval($this->uri->segment(4)); //分页
		    $page=intval($this->uri->segment(5)); //分页
			//模板
			$tpl='pay-spend.html';
			//URL地址
		    $url='pay/spend/'.$sid;
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=L('pay_28');
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
			$sqlstr = "select * from ".CS_SqlPrefix."spend where uid=".$_SESSION['cscms__id'];
            if($sid>0){
			    $sqlstr.=" and sid=".($sid-1);
			}
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,$sid,$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[spend:sid]",$sid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

    //分成记录
	public function income()
	{
		    $sid=intval($this->uri->segment(4)); //分页
		    $page=intval($this->uri->segment(5)); //分页
			//模板
			$tpl='pay-income.html';
			//URL地址
		    $url='pay/income/'.$sid;
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=L('pay_29');
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
			$sqlstr = "select * from ".CS_SqlPrefix."income where uid=".$_SESSION['cscms__id'];
            if($sid>0){
			    $sqlstr.=" and sid=".($sid-1);
			}
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,$sid,$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[income:sid]",$sid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

    //充值卡记录
	public function cardlist()
	{
		    $page=intval($this->uri->segment(4)); //分页
			//模板
			$tpl='pay-cardlist.html';
			//URL地址
		    $url='pay/cardlist';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=L('pay_30');
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
			$sqlstr = "select * from ".CS_SqlPrefix."paycard where uid=".$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,'',$sqlstr,$ids,true,false);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}
}
