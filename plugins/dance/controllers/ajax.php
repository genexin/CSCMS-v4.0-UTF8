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
          if(!$this->agent->is_referral()) show_error(L('dance_01'),404,Web_Name.L('dance_02'));
	      //关闭数据库缓存
          $this->db->cache_off();
		  $this->load->model('CsdjUser');
	}

    //收藏
	public function dancefav()
	{
		   $callback = $this->input->get('callback',true);
           $id = intval($this->uri->segment(3));   //方式
		   if($id==0){
                $error=L('dance_03');
		   }elseif(!$this->CsdjUser->User_Login(1)){
                $error=L('dance_04');
		   }else{
		        $row=$this->CsdjDB->get_row('dance','cid,name,shits',$id);
		        if(!$row){
			        $error=L('dance_05');
		        }else{
					//判断是否收藏
		            $rows=$this->db->query("SELECT id FROM ".CS_SqlPrefix."dance_fav where sid=1 and did=".$id." and uid=".$_SESSION['cscms__id']."")->row();
		            if($rows){
			            $error=L('dance_06');
					}else{
						$add['did']=$id;
						$add['cid']=$row->cid;
						$add['uid']=$_SESSION['cscms__id'];
						$add['name']=$row->name;
						$add['addtime']=time();
						$this->CsdjDB->get_insert('dance_fav',$add);
                        //增加收藏人气
	                    $updata['shits']=$row->shits+1;
                        $this->CsdjDB->get_update('dance',$id,$updata);
						//增加动态
						$add2['dir']='dance';
						$add2['uid']=$_SESSION['cscms__id'];
						$add2['did']=$id;
						$add2['name']=$row->name;
						$add2['link']=linkurl('play','id',$id,0,'dance');
						$add2['title']=L('dance_07');
						$add2['addtime']=time();
						$this->CsdjDB->get_insert('dt',$add2);
						$error='ok';
					}
				}
           }
		   $error=get_bm($error,'gbk','utf-8');
           echo $callback."({msg:".json_encode($error)."})";
	}

    //顶
	public function danceding()
	{
		   $callback = $this->input->get('callback',true);
           $id = intval($this->uri->segment(3));   //ID
		   $ding = $this->cookie->get_cookie("danceding_id_".$id);
		   if($id==0){
                $error=L('dance_03');
		   }elseif(!$this->CsdjUser->User_Login(1)){
                $error=L('dance_04');
		   }elseif(!empty($ding)){
                $error=L('dance_08');
		   }else{
		        $row=$this->CsdjDB->get_row('dance','dhits',$id);
		        if(!$row){
			        $error=L('dance_05');
		        }else{
                        //记住cookie
	                    $this->cookie->set_cookie("danceding_id_".$id,'ok',time()+86400);
                        //增加顶人气
	                    $updata['dhits']=$row->dhits+1;
                        $this->CsdjDB->get_update('dance',$id,$updata);
						$error='ok';
				}
           }
		   $error=get_bm($error,'gbk','utf-8');
           echo $callback."({msg:".json_encode($error)."})";
	}

    //收藏专辑
	public function albumfav()
	{
		   $callback = $this->input->get('callback',true);
           $id = intval($this->uri->segment(3));   //方式
		   if($id==0){
                $error=L('dance_03');
		   }elseif(!$this->CsdjUser->User_Login(1)){
                $error=L('dance_04');
		   }else{
		        $row=$this->CsdjDB->get_row('dance_topic','cid,name,shits',$id);
		        if(!$row){
			        $error=L('dance_23');
		        }else{
					//判断是否收藏
		            $rows=$this->db->query("SELECT id FROM ".CS_SqlPrefix."dance_fav where did=".$id." and uid=".$_SESSION['cscms__id']." and sid=2")->row();
		            if($rows){
			            $error=L('dance_06');
					}else{
						$add['did']=$id;
						$add['sid']=2;
						$add['cid']=$row->cid;
						$add['uid']=$_SESSION['cscms__id'];
						$add['name']=$row->name;
						$add['addtime']=time();
						$this->CsdjDB->get_insert('dance_fav',$add);
                        //增加收藏人气
	                    $updata['shits']=$row->shits+1;
                        $this->CsdjDB->get_update('dance_topic',$id,$updata);
						//增加动态
						$add2['dir']='dance';
						$add2['uid']=$_SESSION['cscms__id'];
						$add2['did']=$id;
						$add2['name']=$row->name;
						$add2['link']=linkurl('topic','show',1,1,'dance');
						$add2['title']=L('dance_24');
						$add2['addtime']=time();
						$this->CsdjDB->get_insert('dt',$add2);
						$error='ok';
					}
				}
           }
		   $error=get_bm($error,'gbk','utf-8');
           echo $callback."({msg:".json_encode($error)."})";
	}

}
