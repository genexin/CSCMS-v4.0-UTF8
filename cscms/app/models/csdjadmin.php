<?php
/**
 * @Cscms 3.5 open source management system
 * @copyright 2009-2013 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2013-04-27
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class CsdjAdmin extends CI_Model{
    function __construct (){
	       parent:: __construct ();
		   $this->lang->load('admin_cscms');
		   //判断是否从正确的入口进来的
		   if(!defined('IS_ADMIN')){
               header("location:".$Web_Path);exit;
           }
    }

    //检测后台登入
    function Admin_Login($key='') {
		  if(empty($key)){
              $id   = isset($_SESSION['admin_id'])?intval($_SESSION['admin_id']):0;
              $name = isset($_SESSION['admin_name'])?$_SESSION['admin_name']:'';
              $pass = isset($_SESSION['admin_pass'])?$_SESSION['admin_pass']:'';
		  }else{
			  $str  = unserialize(stripslashes(sys_auth($key,'D')));
              $id   = isset($str['id'])?intval($str['id']):0;
              $name = isset($str['name'])?$str['name']:'';
              $pass = isset($str['pass'])?$str['pass']:'';
		  }

          $type=($this->uri->segment(1)=='opt')?'top':'window';

          $admin_id = intval($this->cookie->get_cookie('admin_id'));
          $admin_login = $this->cookie->get_cookie('admin_login');

          if(empty($id)||empty($name)||empty($pass)){
			     $login=FALSE;
				 //判断记住登录COOKIE
			     if($admin_id>0 && !empty($admin_login)){ 

					     //判断非法COOKIE
				         if(!preg_match('/^[0-9a-zA-Z]*$/', $admin_login)){
						     $adminlogin= '';
					     }
		                 $row=$this->db->query("SELECT id,adminname,adminpass,logip,logtime FROM ".CS_SqlPrefix."admin where id='$admin_id'")->row();
					     if($row && md5($row->adminname.$row->adminpass)==$admin_login){

                            $_SESSION['admin_name']  = $row->adminname;
                            $_SESSION['admin_id']    = $row->id;
                            $_SESSION['admin_pass']  = md5($row->adminpass);
                            $_SESSION['admin_logtime']  = date('Y-m-d H:i:s',$row->logtime);
                            $_SESSION['admin_logip']  = $row->logip;

							$login=true;
						 }
				 }
				 if(!$login){
                      die("<script>".$type.".location='".site_url('login/logout')."';</script>");
				 }
          }else{
                $admin=$this->db->query("SELECT * FROM ".CS_SqlPrefix."admin where id=".$id."")->row();
		        if($admin){
					 //密码不对
                     if(md5($admin->adminpass)!=$pass || $admin->adminname!=$name){
                            die("<script>".$type.".location='".site_url('login/logout')."';</script>");
                     }
					 //IP不对
					 if(getip()!=$admin->logip){
                            die("<script>".$type.".location='".site_url('login/logout')."';</script>");
					 }
					 //判断权限
					 if($admin->sid>1){
                             $zu=$this->db->query("SELECT sys,app FROM ".CS_SqlPrefix."adminzu where id=".$admin->sid."")->row();
					         if(!defined('PLUBPATH')){
								 $quanxian=$zu->sys; //系统默认权限
							 }else{  
								 $apparr=unarraystring($zu->app);
								 $quanxian=(!empty($apparr[PLUBPATH]))?$apparr[PLUBPATH]:'';//板块权限
							 }
							 $arr=@parse_url(REQUEST_URI);
							 $re_url=str_replace("/".SELF."/", "",$arr['path']);
							 $permarr=explode('/',$re_url);
							 if(count($permarr)<2 && $re_url!='index'){
							     $re_url.='/index';
							 }
							 if($re_url!='index' && $re_url!='/index' && $re_url!='opt/main' && $re_url!='opt/head' && $re_url!='opt/error' && $re_url!='opt/menu' && $re_url!='opt/bottom'){
								 if(getqx($re_url,$quanxian,1)!='ok'){
										if($re_url=='upload/up'){
										    die("<script>alert('".L('err_03')."');parent.$('.webox').css({display:'none'});parent.$('.background').css({display:'none'});parent.parent.web_box(2);</script>");
										}else{
                                            admin_msg(L('err_03'),'javascript:history.back();','no');
										}
								 }
							 }
					 }
                }else{
                     die("<script>".$type.".location='".site_url('login/logout')."';</script>");
                }
          }
    }

	//安装版块
    function plub_install($dir,$name) {
		     $msg=L('plub_msg_00');
		     if (is_dir(FCPATH.'plugins/'.$dir)){
				    $api_file=FCPATH.'plugins/'.$dir.'/config/setting.php';
                    if (is_file($api_file)) {
						 $model = require $api_file;
                         if ($model['mid']) {
                                //获取数据表文件
								$sql_file=FCPATH.'plugins/'.$dir.'/config/install.php';
								if (is_file($api_file)) {
									 $install= require $sql_file;
	            					 //数据表安装
	            					 if (is_array($install)) {
	                					 foreach ($install as $sql) {
	                    					 $this->CsdjDB->get_table(str_replace('{prefix}', CS_SqlPrefix, $sql));
	                					 }
									 } else {
	                                         $this->CsdjDB->get_table(str_replace('{prefix}', CS_SqlPrefix, $install));
									 }
                                }
								// 更新站点到版块表,首先判断是否安装
								$row=$this->db->query("select id from ".CS_SqlPrefix."plugins where dir='".$dir."'")->row();
								if(!$row){ //不存在。则安装进库
                                       $add['name']=$model['name'];
                                       $add['author']=$model['author'];
                                       $add['version']=$model['version'];
                                       $add['description']=$model['description'];
                                       $add['dir']=$dir;
									   $this->CsdjDB->get_insert('plugins',$add);
									   $msg="ok";
								}else{
                                       $msg=vsprintf(L('plub_msg_01'),array($dir));
								}
                         }else{
                                $msg=vsprintf(L('plub_msg_02'),array($dir));
						 }
					}else{
                         $msg=vsprintf(L('plub_msg_03'),array($dir));
					}
						
			 }else{
                  $msg=vsprintf(L('plub_msg_04'),array($dir));
			 }
             return $msg;
    }
}

