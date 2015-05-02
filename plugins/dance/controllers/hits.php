<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-09
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Hits extends Cscms_Controller {

	function __construct(){
		  parent::__construct();
          $this->load->library('user_agent');
          if(!$this->agent->is_referral()) show_error(L('dance_01'),404,Web_Name.L('dance_02'));
	      //关闭数据库缓存
          $this->db->cache_off();
	}

    //增加播放人气
	public function ids()
	{
           $id = intval($this->uri->segment(3));   //ID			
           $op = $this->uri->segment(4);   //方式
		   $zd=($op=='topic')?'dance_topic':'dance';
		   $row=$this->CsdjDB->get_row($zd,'name,cid,rhits,zhits,yhits,hits',$id);
		   if(!$row){
			   exit();
		   }
           //增加人气
	       $updata['rhits']=$row->rhits+1;
	       $updata['zhits']=$row->zhits+1;
	       $updata['yhits']=$row->yhits+1;
	       $updata['hits']=$row->hits+1;
           $this->CsdjDB->get_update($zd,$id,$updata);
		   //增加播放记录
		   if($zd=='dance'){
		       $this->load->model('CsdjUser');
			   //判断是否登录
			   if($this->CsdjUser->User_Login(1)){
                   $rows=$this->db->query("Select id,addtime from ".CS_SqlPrefix."dance_play where did=".$id." and uid=".$_SESSION['cscms__id']."")->row();
			       if(!$rows){
                       $add['name']=$row->name;
                       $add['did']=$id;
                       $add['cid']=$row->cid;
                       $add['uid']=$_SESSION['cscms__id'];
                       $add['addtime']=time();
                       $this->CsdjDB->get_insert('dance_play',$add);
			       }else{  //修改时间
	                   $updata2['addtime']=time();
                       $this->CsdjDB->get_update('dance_play',$rows->id,$updata2);
				   }
			   }
		   }

		   //清空月人气
           $month=@file_get_contents(APPPATH."config/month.txt");
		   if($month!=date('m')){
			    $this->db->query("update ".CS_SqlPrefix."dance set yhits=0");
			    $this->db->query("update ".CS_SqlPrefix."dance_topic set yhits=0");
                write_file(APPPATH."config/month.txt",date('m'));
		   }
		   //清空周人气
		   $week=@file_get_contents(APPPATH."config/week.txt");
		   if($week!=date('W',time())){
			    $this->db->query("update ".CS_SqlPrefix."dance set zhits=0");
			    $this->db->query("update ".CS_SqlPrefix."dance_topic set zhits=0");
                write_file(APPPATH."config/week.txt",date('W',time()));
		   }
		   //清空日人气
		   $day=@file_get_contents(APPPATH."config/day.txt");
		   if($day!=date('d')){
			    $this->db->query("update ".CS_SqlPrefix."dance set rhits=0");
			    $this->db->query("update ".CS_SqlPrefix."dance_topic set rhits=0");
                write_file(APPPATH."config/day.txt",date('d'));
		   }
	}

    //动态加载人气
	public function dt()
	{
           $op = $this->uri->segment(3);   //类型
           $id = intval($this->uri->segment(4));   //ID
           $type = $this->uri->segment(5);   //ID
		   $zd=($type=='topic')?'dance_topic':'dance';

		   $dos = array('hits', 'yhits', 'zhits', 'rhits', 'dhits', 'chits', 'shits', 'xhits');
           $op= (!empty($op) && in_array($op, $dos))?$op:'hits';
		   $row=$this->CsdjDB->get_row($zd,$op,$id);
		   if(!$row){
			        echo "document.write('0');";
		   }else{
					echo "document.write('".$row->$op."');";
		   }
	}
}
