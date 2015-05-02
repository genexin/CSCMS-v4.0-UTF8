<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-01-17
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ajax extends Cscms_Controller {

	function __construct(){
		  parent::__construct();
          $this->load->library('user_agent');
          if(!$this->agent->is_referral()) show_error('您访问的页面不存在~!',404,Web_Name.'提醒您');
	      //关闭数据库缓存
          $this->db->cache_off();
		  $this->load->model('CsdjUser');
	}

    //顶、踩
	public function picding()
	{
		   $callback = $this->input->get('callback',true);
           $ac = $this->uri->segment(3);   //ac
           $id = intval($this->uri->segment(4));   //ID
		   $ding = $this->cookie->get_cookie("picding_id_".$id);
		   if($id==0){
                $error='ID为空';
		   }elseif(!$this->CsdjUser->User_Login(1)){
                $error='您还没有登录';
		   }elseif(!empty($ding) && date('Y-m-d',$ding)==date('Y-m-d')){
                $error='您今天已经赞过了';
		   }else{
		        $row=$this->CsdjDB->get_row('pic_type','dhits,chits',$id);
		        if(!$row){
			        $error='数据不存在';
		        }else{
                        //记住cookie
	                    $this->cookie->set_cookie("picding_id_".$id,time(),time()+86400);
                        //增加顶人气
						if($ac=='ding'){
	                        $updata['dhits']=$row->dhits+1;
						}else{
	                        $updata['chits']=$row->chits+1;
						}
                        $this->CsdjDB->get_update('pic_type',$id,$updata);
						$error='ok';
				}
           }
		   $error=get_bm($error,'gbk','utf-8');
           echo $callback."({msg:".json_encode($error)."})";
	}
}
