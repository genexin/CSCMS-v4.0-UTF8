<?php
/**
 * @Cscms v4.0 open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-07-27
 */
@header('Content-Type: text/html; charset=utf-8');
//判断是否安装
if(!file_exists(FCPATH.'packs/install/install.lock') && strpos(REQUEST_URI,'index.php/install/') === FALSE){
	$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$url = substr($url,0,-9);
	$url .= 'index.php/install/';
	header("location:".$url);exit;
}
//装载全局配置文件
require_once 'Cs_Version.php';
require_once 'Cs_DB.php';
require_once 'Cs_Config.php';
require_once 'Cs_User.php';
require_once 'Cs_Home.php';
require_once 'Cs_Pay.php';
require_once 'Cs_Mail.php';
require_once 'Cs_Water.php';
require_once 'Cs_Denglu.php';
require_once 'Cs_Ftp.php';
require_once 'Cs_Sms.php';

//判断网站运行状态
if(!defined('IS_ADMIN') && Web_Off==0){
    die(Web_Onneir);
}

//判断会员系统开关
if(!defined('IS_ADMIN') && User_Mode==0 && strpos(REQUEST_URI,'user') !== FALSE){
	die(User_No_info);
}

//手机客户端访问标示
if(preg_match("/(iPhone|iPad|iPod|Android)/i", strtoupper($_SERVER['HTTP_USER_AGENT']))){
    if(!defined('IS_ADMIN')){
	    define('MOBILE', true);	
	}
}
	
//判断手机客户端访问
if(defined('MOBILE')){
    if(!defined('IS_ADMIN') && Mobile_Is==1){
          if(Mobile_Url!='' && $_SERVER['HTTP_HOST']!=Mobile_Url){
			    $Wap_Url="http://".Mobile_Url;
                header("location:".$Wap_Url);exit;
		  }
    }elseif($_SERVER['HTTP_HOST']==Mobile_Url){
          $Wap_Url="http://".Web_Url.Web_Path;
          header("location:".$Wap_Url);exit;
	}
}else{
     $Web_Link="http://".Web_Url.Web_Path;
	 if(Mobile_Is==0 && $_SERVER['HTTP_HOST']==Mobile_Url){
           header("location:".$Web_Link);exit;
	 }
	 if(Mobile_Win==0 && $_SERVER['HTTP_HOST']==Mobile_Url){
           header("location:".$Web_Link);exit;
	 }else{
           //增加手机访问标示
           if(!defined('IS_ADMIN') && strpos(REQUEST_URI, '/mobile') !== false){
	            if(!defined('MOBILE')){
                    define('MOBILE', true);
				}
           }
	 }
}

//判断会员主页泛域名
if(Home_Ym==1){
    $HOME_YMALL=explode(".",$_SERVER['HTTP_HOST']);
	$HOME_YM=str_replace($HOME_YMALL[0].'.', "",$_SERVER['HTTP_HOST']);
	$HOME_EXT = explode('|',Home_Ymext);
	if($HOME_YMALL[0]!='www' && !in_array($HOME_YMALL[0], $HOME_EXT) && $HOME_YM==Home_YmUrl){
		define('HOMEPATH', 'home');
	}
}
//判断版块绑定域名
$_ERYM=FALSE;
if(file_exists(CSCMS.'lib/Cs_Domain.php')){
    $_CS_Domain = require_once(CSCMS.'lib/Cs_Domain.php');
    if (is_array($_CS_Domain)) {
          foreach ($_CS_Domain as $key => $host) {
                if($_SERVER['HTTP_HOST']==$host){
					if($key=='user'){ //会员中心二级域名
						 define('USERPATH', $key);
						 //判断版块会员二级域名
						 $REQUEST=str_replace('index.php/','',REQUEST_URI);
						 $SELFALL=(substr($REQUEST,0,1)=='/')?substr($REQUEST,1):$REQUEST;
						 $Mxall=explode("/",$SELFALL);
						 if(file_exists(FCPATH.'plugins/'.$Mxall[0].'/config/site.php')){
			                 define('PLUBPATH', $Mxall[0]);
							 $app_folder = 'plugins/'.$Mxall[0];
						 }
					}else{ //板块二级域名
						if (!defined('PLUBPATH')) {
							 $app_folder = 'plugins/'.$key;
			                 define('PLUBPATH', $key);
						}
					}
					$_ERYM=TRUE;
					break;
                }
		  }
    }
}

//板块未开启二级域名获得版块实际目录
if(!$_ERYM){
	 $SELF='';
     if(strpos(REQUEST_URI,SELF)!==FALSE){ //动态模式
          $SELFALL=explode(SELF."/",REQUEST_URI);
          if(count($SELFALL)>1){
	          $Mxall=explode("/",$SELFALL[1]);
              $SELF=str_replace("/", "", $Mxall[0]);
          }
     }else{ //伪静态模式
          $SELFALL=(substr(REQUEST_URI,0,1)=='/')?substr(REQUEST_URI,1):REQUEST_URI;
	      $Mxall=explode("/",$SELFALL);
          $SELF=str_replace("/", "", $Mxall[0]);
	 }
	 //板块
     if(!empty($SELF) && $_SERVER['HTTP_HOST']==Web_Url && is_dir(FCPATH.'plugins/'.$SELF) && file_exists(FCPATH.'plugins/'.$SELF.'/controllers/index.php')){
          $app_folder = 'plugins/'.$SELF;
	      define('PLUBPATH', $SELF);
     }
	 //会员中心
     if((!defined('PLUBPATH') && $SELF=='user') || (defined('PLUBPATH') && strpos(REQUEST_URI,$SELF.'/user')!==FALSE)){
		 define('USERPATH', $SELF);
	 }
	 //会员主页
	 if(!empty($Mxall) && count($Mxall)>1){
         $SELF=str_replace("/", "", $Mxall[1]);
	 }
	 if((!defined('PLUBPATH') && $SELF=='home') || (defined('PLUBPATH') && strpos(REQUEST_URI,$SELF.'/home')!==FALSE)){
	      define('HOMEPATH', $SELF);
     }
}

//判断板块会员控制器
if(!defined('IS_ADMIN') && !defined('PLUBPATH')){
     $arr=parse_url(REQUEST_URI);
     $re_url=str_replace("/".SELF."/", "",$arr['path']);
	 if(substr($re_url,0,1)=='/') $re_url=substr($re_url,1);
     $permarr=explode('/',$re_url);
	 $SELF=$permarr[0];
     if(!empty($SELF)){
            //手机访问
		    if(defined('MOBILE') && is_dir(FCPATH.'plugins/'.$SELF) && !file_exists(CSCMS.'app/controllers/home/'.$SELF.'.php')){
                  define('PLUBPATH', $SELF);
				  $app_folder = 'plugins/'.$SELF;
			//会员
			}elseif((defined('USERPATH') || defined('HOMEPATH')) && is_dir(FCPATH.'plugins/'.$SELF) && !file_exists(CSCMS.'app/controllers/user/'.$SELF.'.php')){
                  define('PLUBPATH', $SELF);
				  $app_folder = 'plugins/'.$SELF;
			}
	 }
	 //判断会员主页版块
     if(!defined('PLUBPATH') && defined('HOMEPATH')){
		    if(count($permarr)>2){
	            $SELF=$permarr[2];
                //手机访问
		        if(defined('MOBILE') && is_dir(FCPATH.'plugins/'.$SELF) && !file_exists(CSCMS.'app/controllers/home/'.$SELF.'.php')){
                      define('PLUBPATH', $SELF);
				      $app_folder = 'plugins/'.$SELF;
			    //会员
			    }elseif((defined('USERPATH') || defined('HOMEPATH')) && is_dir(FCPATH.'plugins/'.$SELF) && !file_exists(CSCMS.'app/controllers/home/'.$SELF.'.php')){
                      define('PLUBPATH', $SELF);
				      $app_folder = 'plugins/'.$SELF;
			    }
		    }
     }
	 //会员主页版块
	 if(!defined('PLUBPATH') && defined('HOMEPATH')){
         if(is_dir(FCPATH.'plugins/'.$SELF) && file_exists(FCPATH.'plugins/'.$SELF.'/controllers/index.php')){
              $app_folder = 'plugins/'.$SELF;
	          define('PLUBPATH', $SELF);
         }
	 }
}