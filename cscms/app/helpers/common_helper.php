<?php
/**
 * @Cscms open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-07-26
 */

/**
 * 全局通用函数
 */

//检测PHP设置参数
function show($varName){
	switch($result = get_cfg_var($varName)){
		case 0:return '<font color="red">×</font>';break;
		case 1:return '<font color=#0076AE>√</font>';break;
		default: return $result;break;
	}
}
//检测Mysql
function mydb(){
	 $ci = &get_instance();
	 if (!isset($ci->db)){
        $ci->load->database();
	 }
     $getmysqlver=$ci->db->version();
     if(empty($getmysqlver)){
         return L('jiance_no');
     }
     $MysqlALL=explode(".",$getmysqlver);
     if($MysqlALL[0] < 5){
	     return '<font color="red">×</font>&nbsp;&nbsp;('.$getmysqlver.')';
     }else{
	     return '<font color=#0076AE>√</font>&nbsp;&nbsp;('.$getmysqlver.')';
     }

}
//检测PHP环境
function isfun($funName = ''){
    if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return L('error');
	return (false !== function_exists($funName)) ? '<font color=#0076AE>√</font>' : '<font color="red">×</font>';
}
//获取版块配置参数
function config($found,$dir=''){
	    //判断版块是否安装
		if (defined('PLUBPATH') || !empty($dirs)) {
			 $dir=($dir=='')?PLUBPATH:$dir;
		}
		if($dir){
             $site = require FCPATH.'plugins/'.$dir.'/config/site.php';
		}else{
			 if(defined('MOBILE') && Mobile_Is==1){ //手机
                  $site[$found] = Mobile_Skins;
			 }elseif(defined('USERPATH')){ //会员
                  $site[$found] = User_Skins;
			 }elseif(defined('HOMEPATH')){ //空间
                  $site[$found] = Home_Skins;
			 }else{
                  $site[$found] = Web_Skins;
			 }
		}
		if(!empty($site[$found]) || $site[$found]==0){
               return $site[$found];
		}else{
               return 'null';
		}
}
//加载语言
function L($key = '', $arr=array()){
	if($key == ''){
            return '';
	}else{
            $ci	= &get_instance();
			if(!empty($arr)){
                return vsprintf($ci->lang->line($key),$arr);
			}else{
                return $ci->lang->line($key);
			}
	}
}
//获取版块风格目录
function Skins_Dir($dir='',$skins=null){
		if(!defined('PLUBPATH') || $dir=='index'){
			 if(defined('MOBILE') && Mobile_Is==1){ //手机
				  $TempImg = Web_Path.'cscms/tpl/mobile/'.Mobile_Skins.'/';
			 }elseif(defined('USERPATH')){ //会员
                  $TempImg = Web_Path.'cscms/tpl/user/'.User_Skins.'/';
			 }elseif(defined('HOMEPATH')){ //空间
				  if(empty($skins)){
                      $TempImg = Web_Path.'cscms/tpl/home/'.Home_Skins.'/';
				  }else{
                      $TempImg = Web_Path.'cscms/tpl/home/'.$skins;
				  }
			 }else{
                  $TempImg = Web_Path.'cscms/tpl/skins/'.Web_Skins.'/';
			 }
		}else{
			 if(file_exists(FCPATH.'plugins/'.$dir.'/config/site.php')){
		          $site=require FCPATH.'plugins/'.$dir.'/config/site.php';
			 }else{
		          $site=array();
			 }
             if(empty($site)){
                  $site['Skins_Dir']='default';
                  $site['Mobile_Is']=0;
			 }
			 if(defined('MOBILE') && $site['Mobile_Is']==1){ //手机
                  $TempImg = Web_Path.'plugins/'.PLUBPATH.'/tpl/mobile/'.$site['Skins_Dir'].'/';
			 }elseif(defined('USERPATH')){ //会员
                  $TempImg = Web_Path.'plugins/'.PLUBPATH.'/tpl/user/'.$site['User_Dir'].'/';
			 }elseif(defined('HOMEPATH')){ //空间
				  if(empty($skins)){
                      $TempImg = Web_Path.'plugins/'.PLUBPATH.'/tpl/home/'.Home_Skins.'/';
				  }else{
                      $TempImg = Web_Path.'plugins/'.PLUBPATH.'/tpl/home/'.$skins;
				  }
			 }else{
                  $TempImg = Web_Path.'plugins/'.PLUBPATH.'/tpl/skins/'.$site['Skins_Dir'].'/';
			 }
		}
        $TempImg = str_replace('//','/',$TempImg);
        $TempImg = substr(substr($TempImg,0,strlen($TempImg)-1),0,strrpos(substr($TempImg,0,strlen($TempImg)-1),'/')+1);
        return $TempImg;
}
//获取任意字段信息
function getzd($table,$ziduan,$id,$cha='id'){
	 $ci = &get_instance();
	 if (!isset($ci->db)){
        $ci->load->database();
	 }
     if($table && $ziduan && $id){
	     $ci->db->where($cha,$id);
	   	 $ci->db->select($ziduan);
	   	 $row=$ci->db->get($table)->row();
		 if($row){
			  return $row->$ziduan;
		 }else{
			  return "";	
		 }
	 }
}
//判断是否关注
function getgz($uid=0){
	 $ci = &get_instance();
	 if (!isset($ci->db)){
        $ci->load->database();
	 }
	 if((int)$uid==0){
		return 0;
	 }
	 $guanzu=0;
     if(isset($_SESSION['cscms__id'])){
	     $guanzu=$ci->db->query("SELECT id FROM ".CS_SqlPrefix."friend where uidb=".$uid." and uida=".$_SESSION['cscms__id']."")->num_rows();
	 }
	 return $guanzu;
}
//获取会员等级
function getlevel($jinyans=0,$type=0){
	 $ci = &get_instance();
	 if (!isset($ci->db)){
        $ci->load->database();
	 }
	 $level = 1;
	 $name = '初级';
	 $xid = 0;
	 $jinyan = 0;
	 $stars = 1;
	 $row=$ci->db->query("SELECT * FROM ".CS_SqlPrefix."userlevel where jinyan<".intval($jinyans+1)." order by xid desc")->row();
	 if($row){
	     $level = $row->xid; //等级ID
	     $name = $row->name; //等级名称
	     $jinyan = $row->jinyan; //等级经验
	     $xid = $row->id;   //当前等级ID
	     $stars = $row->stars; //星星数量
	 }
	 if($type==0){
		 return $level;	 //会员等级
	 }
	 if($type==1){
		 return get_stars($stars,$level,$name);	//显示星星
	 }
	 $xjinyan=$jinyan;
	 $rowx=$ci->db->query("SELECT jinyan FROM ".CS_SqlPrefix."userlevel where id>".$xid." order by xid asc")->row();
     if($rowx){
	      $xjinyan=$rowx->jinyan;
	 }
	 if($type==2){
		 return $xjinyan;	//下个级别需要经验
	 }
	 if($type==3){
		 return ($xjinyan-$jinyans);	//下个级别剩余经验
	 }
	 if($type==4){
		 return number_format($jinyans/$xjinyan*100,2);	//下个级别百分比
	 }
	 if($type==5){
		 return $name;	//等级名称
	 }
}
//替换静态生成标签
function str_replace_dir($url,$id,$sname,$sort,$page,$dir=''){
	if(empty($sort)) $sort='id';
	if(strpos($url,'{sname}') !== FALSE && !empty($dir)){
	     $ci = &get_instance();
         if(empty($sname) || $sname=='null'){
		     if($ci->db->table_exists(CS_SqlPrefix.$dir) && $ci->db->field_exists('name', CS_SqlPrefix.$dir)){
                 $sname=getzd($dir,'name',$id);
			 }
		 }
		 $ci->load->library('pinyin');
		 $sname=$ci->pinyin->result($sname);
	}
	$old = array('{id}','{sname}','{pinyinid}','{sort}','{md5}','{page}','{zu}','{ji}');
	$new = array($id,$sname,topinyin($id),$sort,substr(md5($id),0,16),$page,0,0);
	return str_replace($old,$new,$url);
}
//获取主域名
function host_ym($k=0) {
    	preg_match('/[\w][\w-]*\.(?:com\.cn|com|cn|co|net|org|gov|cc|so|name|dj|biz|info|pw|dj|pro|wang|hk|tk|gov\.cn|org\.cn|tm|in|asia|mobi|us|net\.cn|pro|me|eu|es|ws|tv|club|ren|la|top)(\/|$)/isU', $_SERVER['HTTP_HOST'], $domain);
        $ym=rtrim(@$domain[0], '/');
		if(empty($ym)) $ym=$_SERVER['HTTP_HOST'];
       	$ym=($k==0)?'.'.$ym:$ym;
	    return $ym;
}
//获取后缀名称归类
function get_extpic($ext) {
	    $ext=strtolower($ext);
        if($ext=='php' || $ext=='asp' || $ext=='css' || $ext=='js' || $ext=='jsp' || $ext=='tpl') {
            $pic = 'php';
        }elseif($ext=='jpg' || $ext=='png' || $ext=='gif' || $ext=='bmp' || $ext=='jpge') {
            $pic = 'pic';
        }elseif($ext=='html' || $ext=='htm' || $ext=='shtm' || $ext=='shtml') {
            $pic = 'html';
        }elseif($ext=='mp3' || $ext=='wma' || $ext=='m4a') {
            $pic = 'mp3';
        }elseif($ext=='mp4' || $ext=='wmv' || $ext=='flv' || $ext=='wav' || $ext=='avi') {
            $pic = 'mp4';
        }elseif($ext=='rar' || $ext=='zip' || $ext=='7z') {
            $pic = 'rar';
		}else{
            $pic = 'no';
        }
		return $pic;
}
//截取字符串的函数
function sub_str($str, $length, $start=0, $suffix="...", $charset="utf-8"){
		 if(($length+2) >= strlen($str)){
            return $str;
         }
         if(function_exists("mb_substr")){
             return mb_substr($str, $start, $length, $charset).$suffix;
         }elseif(function_exists('iconv_substr')){
             return iconv_substr($str,$start,$length,$charset).$suffix;
         }
         $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
         $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
         $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
         $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
         preg_match_all($re[$charset], $str, $match);
         $slice = join("",array_slice($match[0], $start, $length));
         return $slice.$suffix;
}
//从isgod网站获取指定URL的短路径
function isgod($str){
         $content = '' ;
         $url = "http://is.gd/api.php?longurl=$str" ;
         $content = htmlall($url);
         return $content;
}
//写文件
function write_file($path, $data, $mode = FOPEN_WRITE_CREATE_DESTRUCTIVE){
		$dir = dirname($path);
		if(!is_dir($dir)){
			mkdirss($dir);
		}
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}
		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);
		return TRUE;
}
//递归创建文件夹
function mkdirss($dir) {
    if (!$dir) {
        return FALSE;
    }
    if (!is_dir($dir)) {
        mkdirss(dirname($dir));
        if (!file_exists($dir)) {
            mkdir($dir, 0777);
        }
    }
    return true;
}
//得到两时间间隔的秒数
function datediff($d1,$d2){
     	if(is_string($d1)) $d1=strtotime($d1);
     	if(is_string($d2)) $d2=strtotime($d2);
     	return ($d2-$d1);
}
//得到时间的年月日
function getdates($time){
         if(empty($time)){
			 return date('Y-m-d');
		 }else{
			 if(is_string($time)) $time=strtotime($time);
     	     return date('Y-m-d',$time);
		 }
}
//时间格式转换
function datetime($TimeTime){
    	$limit=time()-$TimeTime;
    	if ($limit <5) {$show_t = L('time_01');}
    	if ($limit >= 5 and $limit <60) {$show_t = $limit.L('time_02');}
    	if ($limit >= 60 and $limit <3600) {$show_t = sprintf("%01.0f",$limit/60).L('time_03');}
    	if ($limit >= 3600 and $limit <86400) {$show_t = sprintf("%01.0f",$limit/3600).L('time_04');}
    	if ($limit >= 86400 and $limit <2592000) {$show_t = sprintf("%01.0f",$limit/86400).L('time_05');}
   		if ($limit >= 2592000 and $limit <31104000) {$show_t = sprintf("%01.0f",$limit/2592000).L('time_06');}
    	if ($limit >= 31104000) {$show_t = L('time_07');}
  	    return $show_t;
}
//Base64加密
function cs_base64_encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
}
//Base64解密
function cs_base64_decode($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data.= substr('====', $mod4);
        }
        return base64_decode($data);
}
//数组实列化
function arraystring($data) {
        return $data ? addslashes(serialize($data)) : '';
}
//实列化数组
function unarraystring($data) {
		 if(empty($data)) return '';
         return unserialize(stripslashes($data));
}
//HTML转字符
function str_encode($str){
	     if(is_array($str)) {
             foreach($str as $k => $v) {
                  $str[$k] = str_encode($v); 
			 }
		 }else{
                  $str=str_replace("<","&lt;",$str);
                  $str=str_replace(">","&gt;",$str);
                  $str=str_replace("\"","&quot;",$str);
				  $str=str_replace("'",'&#039;',$str);
		 }
         return $str;
}
//字符转HTML
function str_decode($str){
	     if(is_array($str)) {
             foreach($str as $k => $v) {
                  $str[$k] = str_decode($v); 
			 }
		 }else{
                  $str=str_replace("&lt;","<",$str);
                  $str=str_replace("&gt;",">",$str);
                  $str=str_replace("&quot;","\"",$str);
				  $str=str_replace("&#039;","'",$str);
		 }
         return $str;
}
//SQL过滤
function safe_replace($string){
	     if(is_array($string)) {
             foreach($string as $k => $v) {
                  $string[$k] = safe_replace($v); 
			 }
		 }else{
			      if(!is_numeric($string)){
	                    $string = str_replace('%20','',$string);
	                    $string = str_replace('%27','',$string);
	                    $string = str_replace('%2527','',$string);
						$string = str_replace("'",'&#039;',$string);
	                    $string = str_replace('"','&quot;',$string);
	                    $string = str_replace(';','',$string);
	                    $string = str_replace('<','&lt;',$string);
	                    $string = str_replace('>','&gt;',$string);
	                    $string = str_replace('\\','',$string);
	                    $string = preg_replace("/ /","", $string);
	                    $string = preg_replace("/　/","", $string);
						$string = str_encode($string);
				  }
		 }
	     return $string;
}
//屏蔽所有html
function str_checkhtml($str,$sql=0) {
	     if(is_array($str)) {
             foreach($str as $k => $v) {
                  $str[$k] = str_checkhtml($v); 
			 }
		 }else{
	              $str = preg_replace("/\s+/"," ", $str);
	              $str = preg_replace("/&nbsp;/","",$str);
	              $str = preg_replace("/\r\n/","",$str);
	              $str = preg_replace("/\n/","",$str);
	              $str = str_replace(chr(13),"",$str);
	              $str = str_replace(chr(10),"",$str);
	              $str = str_replace(chr(9),"",$str);
	              $str = strip_tags($str);
	              $str = str_encode($str);
		 }
		 if($sql==1){
	           $str = safe_replace($str);
		 }
	     return $str;
}
//xss过滤函数
function remove_xss($val) { 
	     if(is_array($val)) {
             foreach($val as $k => $v) { 
                  $val[$k] = remove_xss($v); 
             } 
		 }else{
               $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);   
               $search = 'abcdefghijklmnopqrstuvwxyz'; 
               $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';  
               $search .= '1234567890!@#$%^&*()'; 
               $search .= '~`";:?+/={}[]-_|\'\\'; 
               for ($i = 0; $i < strlen($search); $i++) { 
                     $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ; 
                     $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ; 
               } 
               $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'); 
               $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload', 'onbegin', 'onend'); 
               $ra = array_merge($ra1, $ra2); 

               $found = true; // keep replacing as long as the previous round replaced something 
               while ($found == true) { 
                     $val_before = $val; 
                     for ($i = 0; $i < sizeof($ra); $i++) { 
                        $pattern = '/'; 
                        for ($j = 0; $j < strlen($ra[$i]); $j++) { 
                           if ($j > 0) { 
                              $pattern .= '(';  
                              $pattern .= '(&#[xX]0{0,8}([9ab]);)'; 
                              $pattern .= '|';  
                              $pattern .= '|(&#0{0,8}([9|10|13]);)'; 
                              $pattern .= ')*'; 
                           } 
                           $pattern .= $ra[$i][$j]; 
                        } 
                        $pattern .= '/i';  
                        $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag  
                        $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags  
                        if ($val_before == $val) {  
                           // no replacements were made, so exit the loop  
                           $found = false;  
                        }  
                     }  
               }  
		 }
		 $val=str_replace("em<x>bed=","embed=",str_replace("ti<x>tle=","title=",str_replace("st<x>yle=","style=",$val)));
         return $val; 
}
//检查密码长度是否符合规定
function is_userpass($password) {
	$strlen = strlen($password);
	if($strlen >= 6 && $strlen <= 20) return true;
	return false;
}
//检测输入中是否含有错误字符
function is_badword($string) {
	$badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
	foreach($badwords as $value){
		if(strpos($string, $value) !== FALSE) {
			return TRUE;
		}
	}
	return FALSE;
}
//判断用户名格式是否正确
function is_username($username,$s=0) {
	$strlen = strlen($username);
    if($s==0 && User_RegZw==0 && preg_match("/[\x7f-\xff]/", $username)) {
		return false;
	} elseif (is_badword($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)){
		return false;
	} elseif ( 20 < $strlen || $strlen < 2 ) {
		return false;
	}
	return true;
}
//判断email格式是否正确
function is_email($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}
//判断手机号码格式是否正确
function is_tel($tel) {
	return preg_match("/^1[3|4|5|7|8][0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|18[0-9]{9}$/", $tel);
}
//判断QQ号码格式是否正确
function is_qq($qq) {
    return preg_match('/^[1-9][0-9]{4,12}$/', $qq);
}
//编码转换
function get_bm($string,$s1='gbk',$s2='utf-8') {

	     if(is_array($string)) {
             foreach($string as $k => $v) { 
                  $string[$k] = get_bm($v); 
             } 
		 }else{
	         if(strtolower($s1)=='gbk'){
                  if(is_utf8($string)){
                      return $string;
			      }
		     }
	         if(function_exists("mb_convert_encoding")){
				$string = mb_convert_encoding($string, $s2, $s1);
             }else{
                $string = iconv($s1, $s2, $string);
             }
		 }
         return $string;
}
//urlencode解码
function rurlencode($string) {
         $key=rawurldecode($string);
         if(!is_utf8($key)){
			  $key = get_bm($key,'gbk', 'utf-8');
         }
         return $key;
}
//判断字符是否是UTF-8
function is_utf8($liehuo_net) { 
         if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$liehuo_net) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$liehuo_net) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$liehuo_net) == true) { 
                return true; 
         } else { 
                return false; 
         } 
}
//escape编码
function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') { 
    $return = ''; 
    if (function_exists('mb_get_info')) { 
        for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) { 
            $str = mb_substr ( $string, $x, 1, $in_encoding ); 
            if (strlen ( $str ) > 1) { // 多字节字符 
                $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) ); 
            } else { 
                $return .= '%' . strtoupper ( bin2hex ( $str ) ); 
            } 
        } 
    } 
    return $return; 
}
//escape编码解析
function unescape($str) { 
    $ret = ''; 
    $len = strlen($str); 
    for ($i = 0; $i < $len; $i ++) { 
        if ($str[$i] == '%' && $str[$i + 1] == 'u') { 
            $val = hexdec(substr($str, $i + 2, 4)); 
            if ($val < 0x7f) 
                $ret .= chr($val); 
            else  
                if ($val < 0x800) 
                    $ret .= chr(0xc0 | ($val >> 6)) . 
                     chr(0x80 | ($val & 0x3f)); 
                else 
                    $ret .= chr(0xe0 | ($val >> 12)) . 
                     chr(0x80 | (($val >> 6) & 0x3f)) . 
                     chr(0x80 | ($val & 0x3f)); 
            $i += 5; 
        } else  
            if ($str[$i] == '%') { 
                $ret .= urldecode(substr($str, $i, 3)); 
                $i += 2; 
            } else 
                $ret .= $str[$i]; 
    } 
    return $ret; 
}
//字符加密、解密
function sys_auth($string,$operation,$key=''){
	     $key=($key=='')?CS_Encryption_Key:$key;
         $key=md5($key);
         $key_length=strlen($key);
         $string=$operation=='D'?base64_decode(str_replace('-','/',str_replace('_','+',$string))):substr(md5($string.$key),0,8).$string;
         $string_length=strlen($string);
         $rndkey=$box=array();
         $result='';
         for($i=0;$i<=255;$i++){
                   $rndkey[$i]=ord($key[$i%$key_length]);
                   $box[$i]=$i;
         }
         for($j=$i=0;$i<256;$i++){
                   $j=($j+$box[$i]+$rndkey[$i])%256;
                   $tmp=$box[$i];
                   $box[$i]=$box[$j];
                   $box[$j]=$tmp;
		 }
         for($a=$j=$i=0;$i<$string_length;$i++){
                   $a=($a+1)%256;
                   $j=($j+$box[$a])%256;
                   $tmp=$box[$a];
                   $box[$a]=$box[$j];
                   $box[$j]=$tmp;
                   $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
		 }
         if($operation=='D'){
               if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
                       return substr($result,8);
               }else{
                       return'';
			   }
		 }else{
               return str_replace('+','_',str_replace('=','',base64_encode($result)));
		 }
}
//地址简单加密
function urltostr($s, $w) {
	$s = $w.'#cscms#'.$s;
    $v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
    $v = array_values($v);
    $v[count($v)] = strlen($s);
	$str=implode('.', $v);
    return $str;
}
//随机颜色
function random_color() {
    $str = '#';
    for ($i = 0; $i < 6; $i++) {
        $randNum = rand(0, 15);
        switch ($randNum) {
            case 10: $randNum = 'A'; break;
            case 11: $randNum = 'B'; break;
            case 12: $randNum = 'C'; break;
            case 13: $randNum = 'D'; break;
            case 14: $randNum = 'E'; break;
            case 15: $randNum = 'F'; break;
        }
        $str.= $randNum;
    }
    return $str;
}
//表情转换
function facehtml($str)  {  
          if (empty($str)) return false;  
          for($i=1;$i<=56;$i++){
                 $str = str_replace( '[em:'.$i.']', "<img align='absmiddle' src=http://".Web_Url.Web_Path."packs/images/faces/e".$i.".gif border=0>", $str); 
          }
          return $str;  
}
//大小转换
function formatsize($size, $dec=2){
         $a = array("B", "KB", "MB", "GB", "TB", "PB");
         $pos = 0;
         while ($size >= 1024) {
              $size /= 1024;
                $pos++;
         }
         return round($size,$dec)." ".$a[$pos];
}
//判断权限
function getqx($value,$perm,$sid=0){
         if(empty($perm)){
              return "no";
         }else{
	          $permarr=explode(',',$perm);
	          for($i=0;$i<count($permarr);$i++){
				  if($sid>0){
				      if(strpos($value,$permarr[$i]) !== FALSE){
			              return "ok";
			              break;
		              }
				  }else{
		              if($permarr[$i]==$value){
			              return "ok";
			              break;
		              }
				  }
	          }
         }
         return "no";
}
//获取IP
function getip(){ 
         $ci = &get_instance();
		 $ip = $ci->input->ip_address();
         if(preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/",$ip)){
                return $ip; 
         }else{
                return "";
         }
} 
//邮件消息标签替换
function getmsgto($str,$all){ 
         foreach ($all as $key => $value) {
                if($key=='username'){
                       $str = str_replace("{cscms:user}", $value, $str);
	            }
                if($key=='url'){
                       $str = str_replace("{cscms:url}", $value, $str); 
	            }
         }
         $str = str_replace("{cscms:webname}", Web_Name, $str); 
         $str = str_replace("{cscms:time}", date('Y-m-d H:i:s'), $str); 
         return str_decode($str); 
} 
//通过key查找数组的value
function arr_key_value($arr,$key){
	     if(is_array($arr)){
            foreach ($arr as $keys => $value) {
                if($key==$keys){
                      return $value;
	            }
            }
		 }
         return false;
}
//写入新数组到文件
function arr_file_edit($arr,$file=''){
	     if($file=='') $file=CSCMS.'lib/Cs_Domain.php';
	     if(is_array($arr)){
		        $con = var_export($arr,true);
	     } else{
		        $con = $arr;
	     }
	     $strs="<?php if (!defined('FCPATH')) exit('No direct script access allowed');".PHP_EOL;
	     $strs.="return $con;";
	     $strs.="?>";
         return write_file($file, $strs);
}
//加入全局核心JS
function cs_addjs($Mark_Text){
	     if(strpos($Mark_Text,'</head>') !== FALSE){
                $view = explode('</head>',$Mark_Text); 
                $views = explode('<script',$view[0]); 
				$str='';
                for($i=1;$i<count($views);$i++){
                    $str.='<script'.$views[$i];
				}
	            $Mark_Text=$views[0]."<script type='text/javascript'>var cscms_path='http://".Web_Url.Web_Path."';</script>\r\n<script type='text/javascript' src='".Web_Path."packs/js/jquery.min.js'></script>\r\n<script type='text/javascript' src='".Web_Path."packs/js/cscms.js'></script>\r\n".$str."</head>";
	            if(count($view)>1) $Mark_Text.=$view[1];
		 }
         return $Mark_Text;
}
//给模板加入增加人气JS
function hits_js($Mark_Text,$jslink){
	 $js="<script type='text/javascript'>cscms_inc_js('".$jslink."');</script>";
	 if(strpos($Mark_Text,'</body>') !== FALSE){
           $view = explode('</body>',$Mark_Text); 
	       $Mark_Text=$view[0].$js."\r\n</body>";
		   if(count($view)>1) $Mark_Text.=$view[1];
	 }else{
           $Mark_Text.=$js;
	 }
     return $Mark_Text;
}
//数字转拼音
function topinyin($str) {
	     $str=str_replace("0","ling",$str);
	     $str=str_replace("1","yi",$str);
	     $str=str_replace("2","er",$str);
	     $str=str_replace("3","san",$str);
	     $str=str_replace("4","si",$str);
	     $str=str_replace("5","wu",$str);
	     $str=str_replace("6","liu",$str);
	     $str=str_replace("7","qi",$str);
	     $str=str_replace("8","ba",$str);
	     $str=str_replace("9","jiu",$str);
	     return $str;	
}
//获取远程内容
function htmlall($url,$codes='utf-8'){
         if(empty($url)) return '';
         // fopen模式
         if (ini_get('allow_url_fopen')) {
              $data = @file_get_contents($url);
         }

         // curl模式
         if (function_exists('curl_init') && function_exists('curl_exec') && empty($data)) {
		      $curl = curl_init(); //初始化curl
		      curl_setopt($curl, CURLOPT_URL, $url); //设置访问的网站地址
		      curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //模拟用户使用的浏览器
		      curl_setopt($curl, CURLOPT_AUTOREFERER, 1);    //自动设置来路信息
		      curl_setopt($curl, CURLOPT_TIMEOUT, 100);      //设置超时限制防止死循环
		      curl_setopt($curl, CURLOPT_HEADER, 0);         //显示返回的header区域内容
		      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //获取的信息以文件流的形式返回
		      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); 
		      $data = curl_exec($curl);
		      curl_close($curl);
	      }
          if(strtolower($codes)=='gbk'){
               $data=get_bm($data);
          }
          $data=str_replace('</textarea>','&lt;/textarea&gt;',$data);
          return $data;
}
// HTML转JS  
function htmltojs($str){
            $re='';
            $str=str_replace('\\','\\\\',$str);
            $str=str_replace("'","\'",$str);
            $str=str_replace('"','\"',$str);
            $str=str_replace('\t','',$str);
            $str= split("\r\n",$str);
            for($i=0;$i<count($str);$i++){
                $re.="document.writeln(\"".$str[$i]."\");\r\n";
            }
            return $re;
}
//文件夹文件归替
function dirtofiles($dir,$hz='mp3'){ 
        $dir = rtrim($dir,'/');    
		$handle = opendir($dir);
        $sizeResult=0;
        $filesResult= "";
        while (false!==($FolderOrFile = readdir($handle)))
        { 
            if($FolderOrFile != "." && $FolderOrFile != "..") 
            { 
                if(is_dir("$dir/$FolderOrFile")){ 
                    $filesResult[]=dirtofiles("$dir/$FolderOrFile",$hz); 
                }else{ 
                    $sys=pathinfo($FolderOrFile);//获取文件名后缀
                    if(is_int(strpos($hz,$sys['extension'])) !== false){  //只显示要扫描的文件
                        $filesResult[]= $dir."/".$FolderOrFile; 
                    }
                }
            }    
        }
        closedir($handle);
        return array_hebing(array_multi2array($filesResult));
}
//多维数组转一维数组
function array_multi2array($array,$goon=False) {
      if(!empty($array)){
                if(!$goon) {static $result_array = NULL;$result_array = array();} else {static $result_array = array();}
	            foreach($array as $value) {
		              if (is_array($value)) {
			                   array_multi2array($value,TRUE);
		              } else {
			                   $result_array[] = $value;
		              }
				}
      }else{
                $result_array="";
      }
	  return $result_array;
}
//一维数组合并相同的值
function array_hebing($array) {
		$tempArray = array();
		foreach($array as $val){
			$tempArray[$val] = true;
		}
	    return array_keys($tempArray);
}
//删除目录和文件
function deldir($dir,$sid='ok') {
         //先删除目录下的文件：
		 if(!is_dir($dir)){
              return true;
		 }
         $dh=opendir($dir);
         while ($file=readdir($dh)) {
           if($file!="." && $file!="..") {
             $fullpath=$dir."/".$file;
             if(!is_dir($fullpath)) {
                 @unlink($fullpath);
             } else {
                 deldir($fullpath);
             }
           }
         }
         closedir($dh);
         //删除当前文件夹：
         if($sid=='ok'){
            if(@rmdir($dir)) {
                return true;
            } else {
                return false;
            }
         }else{
            return true;
         }
}
//获取当前目录总大小
function getdirsize($dir){ 
        $handle = opendir($dir);
        $sizeResult=0;
        while (false!==($FolderOrFile = readdir($handle))){ 
            if($FolderOrFile != "." && $FolderOrFile != ".."){ 
                if(is_dir("$dir/$FolderOrFile")){ 
                    $sizeResult += getDirSize("$dir/$FolderOrFile"); 
                }else{ 
                    $sizeResult += filesize("$dir/$FolderOrFile"); 
                }
            }    
        }
        closedir($handle);
        return $sizeResult;
}
//星星数量转图标
function get_stars($num, $level=0, $name='', $starthreshold = 4) {
    $str = '';
    $alt = ($level>0)?'title="当前等级: Lv.' . $level . '('.$name.')"':'';
    for ($i = 3; $i > 0; $i--) {
        $numlevel = intval($num / pow($starthreshold, ($i - 1)));
        $num = ($num % pow($starthreshold, ($i - 1)));
        for ($j = 0; $j < $numlevel; $j++) {
            $str.= '<img align="absmiddle" src="http://'.Web_Url.Web_Path.'packs/images/stars'.$i.'.gif" ' . $alt . ' />';
        }
    }
    return $str;
}
//评论、留言屏蔽关键字
function filter($str){
	     $KeyArr=explode(',',Pl_Str);
	     for($i=0;$i<count($KeyArr);$i++){
		        $str=str_replace($KeyArr[$i],"**",$str);
	     }
	     return $str;
}
//中文分词
function gettag($title,$content=''){
	$data = htmlall('http://keyword.discuz.com/related_kw.html?ics=gbk&ocs=gbk&title='.rawurlencode($title).'&content='.rawurlencode(sub_str($content,500)));
	if($data) {
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $data, $values, $index);
		xml_parser_free($parser);
		$kws = array();
		foreach($values as $valuearray) {
			if($valuearray['tag'] == 'kw') {
				if(strlen($valuearray['value']) > 3){
					$kws[] = trim($valuearray['value']);
				}
			}elseif($valuearray['tag'] == 'ekw'){
				$kws[] = trim($valuearray['value']);
			}
		}
		return get_bm(implode(',',$kws));
	}
	return false;
}
//评论调用
function get_pl($dir, $did=0, $cid=0){
     $cscms_pl="<div id='cscms_pl'><div class='cscms_txt'><img src='".Web_Path."packs/images/load.gif'>&nbsp;&nbsp;加载评论内容,请稍等......</div></div>\r\n<script type='text/javascript'>var dir='".$dir."';var did=".$did.";var cid=".$cid.";cscms_pl(1,0,0);</script>";
	 return $cscms_pl;
}
//留言调用
function get_gbook($uid=0){
     $cscms_gbook="<div id='cscms_gbook'><div class='cscms_txt'><img src='".Web_Path."packs/images/load.gif'>&nbsp;&nbsp;加载留言内容,请稍等......</div></div>\r\n<script type='text/javascript'>var uid=".$uid.";cscms_home_gbook(1);</script>";
	 return $cscms_gbook;
}
//后台提示信息
function admin_msg($msg, $gourl, $pic='ok', $color='',$left=''){
	    $clas=($left)?'left:'.$left.'px;':'';
	    echo '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>'.L('err_title').'</title><link type="text/css" rel="stylesheet" href="'.Web_Path.'packs/admin/css/style.css" /><div class="tip" style="display:block;'.$clas.'height:200px;"><div class="tiptop"><span>'.L('err_title').'</span></div><div class="tipinfo"><span><img src="'.Web_Path.'packs/admin/images/tip'.$pic.'.png" /></span><div class="tipright"><p style="color:'.$color.';">'.$msg.'</p><cite>'.L('err_01').'<a href="'.$gourl.'">'.L('err_02').'</a></cite></div></div></div>';
		if($gourl=='javascript:window.close();'){
			echo "<script type=\"text/javascript\">window.setTimeout(function (){window.opener=null;window.open('','_self');window.close();},2000)</script>";
		}else{
			echo "<script type=\"text/javascript\">window.setTimeout(function (){location.href=\"".$gourl."\";},3000)</script>";
		}
		echo "</body></html>";
		exit;
}
//前台页面返回信息
function msg_url($title,$url,$time=3000) {
	    if(intval($time)==0) $time=3000;
		//手机访问
		if(defined('MOBILE')){
		    if($url=='javascript:window.close();' or $url=='close'){
			    echo"<script type=\"text/javascript\">window.setTimeout(function (){window.opener=null;window.open('','_self');window.close();},2000)</script>";
		    }else{
			    echo"<script type=\"text/javascript\">window.setTimeout(function (){location.href=\"".$url."\";},".$time.")</script>";
		    }
			echo "<!DOCTYPE html><html><head><meta name='viewport' content='width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no'></head><body><center><br><br><br><br>".$title."<br><br><a href=\"".$url."\">".L('cscms_msg_02')."</a><br><br><a href=\"http://".Web_Url.Web_Path."\">".L('cscms_msg_03')."</a></center></body></html>";
            exit();
		}
		//PC访问
        echo '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><link rel="stylesheet" type="text/css" href="'.Web_Path.'packs/images/msg.css"/><title>'.L('cscms_msg_title').'</title><script type="text/javascript">function run(){var s = document.getElementById("time");if(s.innerHTML == 1){document.getElementById("div_time").style.visibility="hidden";return false;}s.innerHTML = s.innerHTML * 1 - 1;}window.setInterval("run();", 1000);</script></head><body class="msg_tip"><div class="tip_page"><div class="tip_title"><div id="div_time" class="time">'.vsprintf(L('cscms_msg_01'),array($time/1000)).'</div></div><div class="tip_jiange"></div><div class="tip_msg"><div class="tip_msg_content_wai"><div class="tip_msg_content">'.$title.'</div></div><div class="tip_input"><a href="'.$url.'" class="tip_back" tabindex="1">'.L('cscms_msg_02').'</a><span><a href="http://'.Web_Url.Web_Path.'" tabindex="2">'.L('cscms_msg_03').'</a></span></div></div><div class="tip_bot"></div></div>';
		if($url=='javascript:window.close();'){
			echo"<script type=\"text/javascript\">window.setTimeout(function (){window.opener=null;window.open('','_self');window.close();},2000)</script>";
		}else{
			echo"<script type=\"text/javascript\">window.setTimeout(function (){location.href=\"".$url."\";},".$time.")</script>";
		}
		echo"</body></html>";
        exit();
}
//前台错误返回信息
function msg_txt($msg) {
		//手机访问
		if(defined('MOBILE')){
            exit("<!DOCTYPE html><html><head><meta name='viewport' content='width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no'></head><body><center><br><br>".$msg."</center></body></html>");
		}
		//PC访问
		echo '<!DOCTYPE html><html><head><meta http-equiv="Content-Type"content="text/html; charset=utf-8"/><meta name="generator" content="cscms 4.x" /><link rel="stylesheet" type="text/css" href="'.Web_Path.'packs/images/msg.css"/><title>Cscms Error</title></head><body class="msg_txt"><div id="container"><h1>Cscms Error</h1><div class="box"><span class="code"><b class="f14">'.L('cscms_msg_04').'</b><font><p><b>'.$msg.'</b></p></font></span></div><div class="box"><span class="code"><b class="f14">'.L('cscms_msg_05').'</b><font><p>'.L('cscms_msg_06').'</p><p><a href="http://bbs.chshcms.com/search.html?key='.urlencode($msg).'" target="help">'.L('cscms_msg_07').'</a></p><p><a href="javascript:history.back(-1);">'.L('cscms_msg_08').'</a></p><p><a href="http://'.Web_Url.Web_Path.'">返回主页 ('.Web_Url.')</a></p></font></span></div></div></body></html>';
		exit();
}

