<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-08-21
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 模板解析类
 */
class Skins {

    public function __construct()
    {
         //----------
		 //print_r($datas);
    }

	//解析模板
	public function template_parse($str,$ts=TRUE,$if=true) {
		     if(empty($str)) msg_txt(L('skins_null'));
			 //解析头部、底部、左右分栏
		     $str = $this->topandend($str);
			 //会员登录框
	         $str=str_replace('{cscms:logkuang}',$this->logkuang(),$str);
             //自定义标签
	         $str=$this->cscmsopt($str);
             //数据循环
             $str=$this->csskins($str);

             //PHP代码解析
             preg_match_all('/{cscmsphp}([\s\S]+?){\/cscmsphp}/',$str,$php_arr);
		     if(!empty($php_arr[0])){
			     for($i=0;$i<count($php_arr[0]);$i++){
                         $str=$this->cscms_php($php_arr[0][$i],$php_arr[1][$i],$str);
			     }
		     }
		     unset($php_arr);

             //数据统计解析
             preg_match_all('/{cscmscount\s+param\=\"(.*?)\"}/',$str,$count_arr);
		     if(!empty($count_arr[0])){
			     for($i=0;$i<count($count_arr[0]);$i++){
					     $link='<script src="http://'.Web_Url.Web_Path.'index.php/api/count?param='.$count_arr[1][$i].'"></script>';
                         $str=str_replace($count_arr[0][$i],$link,$str);
			     }
		     }
		     unset($count_arr);

             //会员主页模板
             preg_match_all('/{cscmsweb}([\s\S]+?){\/cscmsweb}/',$str,$web_arr);
		     if(!empty($web_arr[0])){
			     for($i=0;$i<count($web_arr[0]);$i++){
                         $str=$this->cscms_web($web_arr[0][$i],$web_arr[1][$i],$str);
			     }
		     }
		     unset($web_arr);

             //解析全局标签
             $str=$this->cscms_common($str);
			 //解析IF判断标签
		     if($if) $str=$this->labelif($str);
			 //加入全局JS
			 if($ts) $str=cs_addjs($str);

             //后台操作
			 if (defined('IS_ADMIN')){
                  $str=str_replace(SELF,"index.php",$str);
			 }

			 //伪静态
			 if (defined('PLUBPATH')){
			       $Web_Mode=config('Web_Mode',PLUBPATH);
			       if($Web_Mode==2){
					   $str=str_replace("index.php/","",$str);
				   }
			 }else{
			       if(Web_Mode==3){
					   $str=str_replace("index.php/","",$str);
				   }
			 }
		     return $str;
	}

	//解析CSCMS大标签
	public function csskins($str,$autoarr=array()){
			 $str=$this->cscmsopt($str);
             preg_match_all('/{cscms:([\S]+)\s+(.*?)}([\s\S]+?){\/cscms:\1}/',$str,$str_arr);
		     if(!empty($str_arr)){
			     for($i=0;$i<count($str_arr[0]);$i++){
                         $str=$this->cscms_sql_to($str_arr[1][$i],$str_arr[2][$i],$str_arr[0][$i],$str_arr[3][$i],$str,$autoarr);
			     }
		     }
		     unset($str_arr);
			 //二级分类
             preg_match_all('/{cscmstype:([\S]+)\s+(.*?)}([\s\S]+?){\/cscmstype:\1}/',$str,$str_arr2);
		     if(!empty($str_arr2)){
			     for($i=0;$i<count($str_arr2[0]);$i++){
                        $str=$this->cscms_sql_to($str_arr2[1][$i],$str_arr2[2][$i],$str_arr2[0][$i],$str_arr2[3][$i],$str,$autoarr);
			     }
		     }
		     unset($str_arr2);
		     return $str;
	}

    //自定义OPT模板
	public function cscmsopt($str,$uid=0){
			 //字母标签解析
	         preg_match_all('/{cscmszm:([0-9]+)}([\s\S]+?){\/cscmszm}/',$str,$Mark_ZM);  //歌曲字母搜索标签解析
	         if(!empty($Mark_ZM)){
                 $strzm=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','其他');
		         for($k=0;$k<count($Mark_ZM[0]);$k++){
                        $zmstr='';
						$zmlen=($Mark_ZM[1][$k]>27)?27:$Mark_ZM[1][$k];
		                for($i=0;$i<$zmlen;$i++){
                             $zmstr.=str_replace('[zm:name]',$strzm[$i],$Mark_ZM[2][$k]);
		                }
                        $str=str_replace($Mark_ZM[0][$k],$zmstr,$str);
				 }
	         }	
			 unset($Mark_ZM);
			 //自定义标签解析
	         preg_match_all('/{cscmsself:([A-Za-z0-9-_]+)}/',$str,$Arrself);
	         if(!empty($Arrself[1])){
		        for($i=0;$i<count($Arrself[1]);$i++){
	 	        	    $ci = &get_instance();
	 	        	    if (!isset($ci->db)){
        	        	    $ci->load->database();
	 	        	    }
                	    $sql="SELECT selflable FROM ".CS_SqlPrefix."label where name='".$Arrself[1][$i]."'";
	        	        $row=$ci->db->query($sql)->row(); 
	        	        if($row){
				            $str=str_replace($Arrself[0][$i],str_decode($row->selflable),$str);
			            }
		        }
	         }
	         unset($Arrself);
			 //自定义JS标签解析
	         preg_match_all('/{cscmsjs:([A-Za-z0-9-_]+)}/',$str,$ArrJs);   
	         if(!empty($ArrJs[1])){
		        for($j=0;$j<count($ArrJs[1]);$j++){
	 	        	    $ci = &get_instance();
	 	        	    if (!isset($ci->db)){
        	        	    $ci->load->database();
	 	        	    }
                	    $sql="SELECT js FROM ".CS_SqlPrefix."ads where name='".$ArrJs[1][$j]."'";
	        	        $rows=$ci->db->query($sql)->row(); 
	        	        if($rows){
				            $str=str_replace($ArrJs[0][$j],"<script src='http://".Web_Url.Web_Path."attachment/js/".$rows->js.".js'></script>",$str);
						}
		        }
	         }
	         unset($ArrJs);
			 //自定义页面标签解析
	         preg_match_all('/{cscmspage:([A-Za-z0-9-_]+)}/',$str,$ArrPage);   
	         if(!empty($ArrPage[1])){
		        for($j=0;$j<count($ArrPage[1]);$j++){
	 	        	    $ci = &get_instance();
	 	        	    if (!isset($ci->db)){
        	        	    $ci->load->database();
	 	        	    }
                	    $sql="SELECT url FROM ".CS_SqlPrefix."page where name='".$ArrPage[1][$j]."'";
	        	        $rows=$ci->db->query($sql)->row(); 
	        	        if($rows){
                                if(Web_Mode==2){  //伪静态
                                       $rows->url=str_replace('index.php/','',$rows->url);
                                }
				                $str=str_replace($ArrPage[0][$j],"http://".Web_Url.$rows->url,$str);
						}
		        }
	         }
	         unset($ArrPage);
             //自定义opt模板
	         preg_match_all('/{cscms:opt-([A-Za-z0-9-_]+)}/',$str,$ArrBd);   //榜单标签解析
	         if(!empty($ArrBd[1])){
		          for($i=0;$i<count($ArrBd[1]);$i++){
					  if(!defined('PLUBPATH')){
			               $str=str_replace($ArrBd[0][$i],site_url('opt/'.$ArrBd[1][$i]),$str);
					  }else{
						   $optlink=Web_Path.'index.php/'.PLUBPATH.'/opt/'.$ArrBd[1][$i];
	                       $Web_Mode=config('Web_Mode',PLUBPATH); //运行模式
						   if($Web_Mode==2){
                               $optlink=str_replace('index.php/','',$optlink);
						   }elseif($Web_Mode==3){
                               $optlink=str_replace('index.php/','',$optlink.'_'.PLUBPATH.'.html');
						   }
						   //二级域名
	                       $Ym_Mode=config('Ym_Mode',PLUBPATH);
	                       $Ym_Url=config('Ym_Url',PLUBPATH);
						   if($Ym_Mode==1){
                               $optlink='http://'.$Ym_Url.str_replace('index.php/'.PLUBPATH.'/','index.php/',$optlink);
						   }
                           $str=str_replace($ArrBd[0][$i],$optlink,$str);
					  }
		          }
	         }
	         unset($ArrBd);
		     return $str;
	}

    //解析会员版块导航
	public function cscmsumenu($str,$uid=0){
	         preg_match_all('/{cscmsmenu:([A-Za-z0-9]+)}([\s\S]+?){\/cscmsmenu}/',$str,$Mark_M);   //榜单标签解析
	         if(!empty($Mark_M) && !empty($Mark_M[0][0])){
	 	        $ci = &get_instance();
	 	        if (!isset($ci->db)){
        	        $ci->load->database();
	 	        }
				$us=$ci->db->query("select level,zid from ".CS_SqlPrefix."user where id=".$uid."")->row_array();
				if(!$us){
                    $us['zid']=0;
                    $us['level']=0;
				}
                $sqlstr="select dir from ".CS_SqlPrefix."plugins order by id asc";
		        $result=$ci->db->query($sqlstr);
				$menu_s='';
				foreach ($result->result() as $row) {
					 if(file_exists(FCPATH.'plugins/'.$row->dir.'/config/menu.php') && file_exists(FCPATH.'plugins/'.$row->dir.'/config/site.php')){
	                      preg_match_all('/{cscmsmenu:auto}([\s\S]+?){\/cscmsmenu:auto}/',$str,$Mark_A);   //榜单标签解析
	                      if(!empty($Mark_A)){
                              $site_arr=require(FCPATH.'plugins/'.$row->dir.'/config/site.php');
                              $menu_arr=require(FCPATH.'plugins/'.$row->dir.'/config/menu.php');
						      $menu=$menu_arr['user'];
							  if(!empty($menu[0]['menu']) && getqx($us['zid'],$site_arr['User_Qx'])!='ok' && getqx($us['level'],$site_arr['User_Dj_Qx'])!='ok'){
		                          $count=count($menu[0]['menu']);
						          $mstr='';
	                              for($j=0;$j<$count;$j++){
                                     $mstr.=str_replace(array('[menu:i]','[menu:dir]','[menu:name]','[menu:link]'),array(($j+1),$row->dir,$menu[0]['menu'][$j]['name'],spacelink($menu[0]['menu'][$j]['link'],$row->dir)),$Mark_A[1][0]);
		                          }
                                  $mstr=str_replace($Mark_A[0][0],$mstr,$Mark_M[2][0]);
							  }else{
                                  $mstr='';
							  }
						  }
						  unset($Mark_A);
				     }
                     $menu_s.=$mstr;
                     if(!empty($menu[0]['name'])){
                        $menu_s=str_replace('[menu:name]',$menu[0]['name'],$menu_s);
					 }
				}
                $str=str_replace($Mark_M[0][0],$menu_s,$str);
	         }	
	         unset($Mark_M);
		     return $str;
	}

	//解析头部、底部、左右分栏
	public function topandend($str,$skins=''){
		     $str = preg_replace ( "/\{cscms:head\}/", $this->topandbottom('head',$skins), $str );
		     $str = preg_replace ( "/\{cscms:left\}/", $this->topandbottom('left',$skins), $str );
		     $str = preg_replace ( "/\{cscms:right\}/", $this->topandbottom('right',$skins), $str );
		     $str = preg_replace ( "/\{cscms:bottom\}/", $this->topandbottom('bottom',$skins), $str );
             //获取非版块模板
		     $str = preg_replace ( "/\{cscms:indexhead\}/", $this->topandbottom2('indexhead',$skins), $str );
		     $str = preg_replace ( "/\{cscms:indexleft\}/", $this->topandbottom2('indexleft',$skins), $str );
		     $str = preg_replace ( "/\{cscms:indexright\}/", $this->topandbottom2('indexright',$skins), $str );
		     $str = preg_replace ( "/\{cscms:indexbottom\}/", $this->topandbottom2('indexbottom',$skins), $str );
		     return $str;
	}

	//解析头部底部模板
	public function topandbottom($type,$skins=''){
             $str='';
			 if(defined('PLUBPATH')){
				 if(!defined('IS_ADMIN') && defined('MOBILE') && config('Mobile_Is')==1){
				      $DIRS=APPPATH."tpl/mobile/".config('Mobile_Dir');
				 }else{
				      $DIRS=APPPATH."tpl/skins/".config('Skins_Dir');
				 }
				 if(defined('USERPATH')){
				      $DIRS=APPPATH."tpl/user/".config('User_Dir');
				 }
				 if(defined('HOMEPATH')){
				      if(empty($skins)){
				           $DIRS=APPPATH."tpl/home/".Home_Skins;
					  }else{
				           $DIRS=APPPATH."tpl/home/".$skins;
					  }
				 }
			 }else{
				 if(!defined('IS_ADMIN') && defined('MOBILE') && Mobile_Is==1){
				      $DIRS=CSCMS."tpl/mobile/".Mobile_Skins;
				 }else{
				      $DIRS=CSCMS."tpl/skins/".Web_Skins;
				 }
				 if(defined('USERPATH')){
					  if(!defined('IS_ADMIN') && defined('MOBILE') && Mobile_Is==1){
				          $DIRS.="/user";
					  }else{
				          $DIRS=CSCMS."tpl/user/".User_Skins;
					  }
				 }
				 if(defined('HOMEPATH')){
					  if(!defined('IS_ADMIN') && defined('MOBILE') && Mobile_Is==1){
				          $DIRS.="/home";
					  }else{
						  if(empty($skins)){
				               $DIRS=CSCMS."tpl/home/".Home_Skins;
						  }else{
				               $DIRS=CSCMS."tpl/home/".$skins;
						  }
					  }
				 }
			 }
             switch($type){
				     case 'head'  : $files = str_replace("//","/",$DIRS."/head.html");break;
				     case 'left'  : $files = str_replace("//","/",$DIRS."/left.html");break;
				     case 'right' : $files = str_replace("//","/",$DIRS."/right.html");break;
				     case 'bottom': $files = str_replace("//","/",$DIRS."/bottom.html");break;
		     }
			 $str = file_exists($files) ? file_get_contents($files) : '';
             return $str;
	}

	//解析非版块头部底部模板
	public function topandbottom2($type,$skins=''){
             $str='';
			 if(!defined('IS_ADMIN') && defined('MOBILE') && Mobile_Is==1){
				      $DIRS=CSCMS."tpl/mobile/".Mobile_Skins;
			 }else{
				      $DIRS=CSCMS."tpl/skins/".Web_Skins;
			 }
			 if(defined('USERPATH')){
					  if(!defined('IS_ADMIN') && defined('MOBILE') && Mobile_Is==1){
				          $DIRS.="/user";
					  }else{
				          $DIRS=CSCMS."tpl/user/".User_Skins;
					  }
			 }
			 if(defined('HOMEPATH')){
					  if(!defined('IS_ADMIN') && defined('MOBILE') && Mobile_Is==1){
				          $DIRS.="/home";
					  }else{
						  if(empty($skins)){
				              $DIRS=CSCMS."tpl/home/".Home_Skins;
						  }else{
				              $DIRS=CSCMS."tpl/home/".$skins;
						  }
					  }
			 }
             switch($type){
				     case 'indexhead'  : $files = str_replace("//","/",$DIRS."/head.html");break;
				     case 'indexleft'  : $files = str_replace("//","/",$DIRS."/left.html");break;
				     case 'indexright' : $files = str_replace("//","/",$DIRS."/right.html");break;
				     case 'indexbottom': $files = str_replace("//","/",$DIRS."/bottom.html");break;
		     }
			 $str = file_exists($files) ? file_get_contents($files) : '';
             return $str;
	}

	//全局会员登陆框
	public function logkuang(){
			 if(defined('PLUBPATH')){ //版块
				   $type=PLUBPATH;
		     }elseif(defined('USERPATH')){ //会员中心
		           $type='user';
			 }elseif(defined('HOMEPATH')){ //会员空间
		           $type='home';
			 }else{
		           $type='index';
			 }
			 if(defined('HOMEPATH')){
                   if(Home_Ym==1){
                        $arr = explode('.', $_SERVER['HTTP_HOST']);
                        $uid = $arr[0];
                   }else{
	                    $ci = &get_instance();
                        $uid = $ci->uri->segment(1);
                   }
		           $type.='/'.$uid;
			 }
             $Mark_Text="<div id='cscms_login'></div>
                          <script type='text/javascript'>
                                       var cscms_loginlink='http://".Web_Url.Web_Path."index.php/api/ulog/log/".$type."';
                                       var cscms_loginaddlink='http://".Web_Url.Web_Path."index.php/api/ulog/login';
                                       var cscms_logoutlink='http://".Web_Url.Web_Path."index.php/api/ulog/logout';
                                       cscms_login();
                         </script>";
		     return $Mark_Text;
	}

	//解析全局标签
	public function cscms_common($str,$skins=''){
		     //解析头部、底部、左右分栏
		     $str = $this->topandend($str,$skins);
	         $str  = str_replace("{cscms:webname}",Web_Name,$str);
	         $str  = str_replace("{cscms:weburl}",Web_Url,$str);
	         $str  = str_replace("{cscms:webpath}",Web_Path,$str);
	         $str  = str_replace("{cscms:path}","http://".Web_Url.Web_Path,$str);
	         $str  = str_replace("{cscms:indextempurl}",Skins_Dir('index',$skins),$str);
			 $dir  = !defined('PLUBPATH')?'':PLUBPATH;
	         $str  = str_replace("{cscms:tempurl}",Skins_Dir($dir,$skins),$str);
	         $str  = str_replace("{cscms:stat}",str_decode(Web_Count),$str);
	         $str  = str_replace("{cscms:notice}",str_decode(Web_Notice),$str);
	         $str  = str_replace("{cscms:regxy}",str_decode(User_Regxy),$str);

			 //SEO代码
             $seo  = (defined('PLUBPATH'))?config('Seo'):'';
			 $title=(!empty($seo['title']))?$seo['title']:str_decode(Web_Title);
	         $str  = str_replace("{cscms:title}",$title,$str);
			 $keywords=(!empty($seo['keywords']))?$seo['keywords']:str_decode(Web_Keywords);
	         $str  = str_replace("{cscms:keywords}",$keywords,$str);
			 $description=(!empty($seo['description']))?$seo['description']:str_decode(Web_Description);
	         $str  = str_replace("{cscms:description}",$description,$str);

	         $str  = str_replace("{cscms:mail}",Admin_Mail,$str); 
	         $str  = str_replace("{cscms:qq}",Admin_QQ,$str);
	         $str  = str_replace("{cscms:tel}",Admin_Tel,$str);
	         $str  = str_replace("{cscms:icp}",Web_Icp,$str);

			 //判断登录状态
			 if(isset($_SESSION['cscms__id']) && isset($_SESSION['cscms__login'])){
	              $str  = str_replace("{cscms:login}","ok",$str);
	              $str  = str_replace("{cscms:uid}",$_SESSION['cscms__id'],$str);
			 }else{
	              $str  = str_replace("{cscms:login}","no",$str);
	              $str  = str_replace("{cscms:uid}",0,$str);
			 }

			 //网站部分链接
			 if(Web_Mode==2){
		          $str=str_replace('{cscms:gbooklink}','http://'.Web_Url.Web_Path.'gbook.html',$str);
			 }else{
		          $str=str_replace('{cscms:gbooklink}',site_url('gbook'),$str);
			 }
			 //搜索链接
			 $solink='';
			 if($dir!=''){
		          $Ym_Mode=config('Ym_Mode',$dir); //二级域名状态
		          $Ym_Url=config('Ym_Url',$dir);   //二级域名地址
				  if($Ym_Mode==1){
                        $solink='http://'.$Ym_Url.Web_Path.'index.php/search';
				  }else{
                        $solink=Web_Path.'index.php/'.$dir.'/search';
				  }
			 }
		     $str=str_replace('{cscms:solink}',$solink,$str);
		     $str=str_replace('{cscms:codes}',site_url('api/codes'),$str);
		     $str=str_replace('{cscms:userlink}',site_url('user'),$str);
		     $str=str_replace('{cscms:loginlink}',site_url('user/login'),$str);
		     $str=str_replace('{cscms:reglink}',site_url('user/reg'),$str);
		     $str=str_replace('{cscms:passlink}',site_url('user/pass'),$str);
		     $str=str_replace('{cscms:qqlink}',site_url('user/open/login/qq'),$str);
	         $str=str_replace('{cscms:weibolink}',site_url('user/open/login/weibo'),$str);
	         $str=str_replace('{cscms:kaixinlink}',site_url('user/open/login/kaixin'),$str);
	         $str=str_replace('{cscms:baidulink}',site_url('user/open/login/baidu'),$str);
	         $str=str_replace('{cscms:doubanlink}',site_url('user/open/login/douban'),$str);
	         $str=str_replace('{cscms:sohulink}',site_url('user/open/login/sohu'),$str);
	         $str=str_replace('{cscms:wangyilink}',site_url('user/open/login/netease'),$str);
	         $str=str_replace('{cscms:renrenlink}',site_url('user/open/login/renren'),$str);
			 $str=userurl($str);
             return $str;
	}

	//组装SQL标签
	public function cscms_sql($fields, $para, $str_arr, $label, $sorts='', $autoarr=array(), $cid=0, $sql='') {
             preg_match_all("/([a-z]+)\=[\"]?([^\"]+)[\"]?/i", stripslashes($para), $matches, PREG_SET_ORDER);
		     $arr = array('field', 'table', 'loop', 'pagesize', 'order', 'sort', 'start');
			 //获取数据表
			 $table=$this->arr_val('table',$matches);
             if($table==''){  //模板标签错误，缺少table参数
				 $strs=str_replace($label,".....",$str_arr);
                 msg_txt(vsprintf(L('skins_table'),array($strs)));
			 }
			 //获取要查询的字段
			 $field=$this->arr_val('field',$matches);
			 if(!$field) $field="*";
	 	     $ci = &get_instance();
	 	     if (!isset($ci->db)){
        	       $ci->load->database();
	 	     }
			 if(!$ci->db->table_exists(CS_SqlPrefix.$table)){  //数据表不存在
				   $strs=str_replace($label,".....",$str_arr);
                   msg_txt(vsprintf(L('skins_table_err'),array($strs,$table)));
			 }
		     if($sql==''){
				 $sql = "select ".$field." from `".CS_SqlPrefix.$table."` where 1=1";
			 }else{
				 $sql = str_replace("{field}",$field,$sql);
			 }
			 $yid=$hid=0;
		     foreach ($matches as $v) {
			     if(in_array($v[1], $arr)) {
				     $v[1] = $v[2];
				     continue;
			     }
				 //解析字母搜索标签
				 if($v[1]=='zm'){
                     $zmall=explode(",",$v[2]);
					 if($ci->db->field_exists($zmall[0], $table)){ //判断条件字段是否存在
			             $zimu_arr=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
			             $zimu_arr1=array(-20319,-20283,-19775,-19218,-18710,-18526,-18239,-17922,-1,-17417,-16474,-16212,-15640,-15165,-14922,-14914,-14630,-14149,-14090,-13318,-1,-1,-12838,-12556,-11847,-11055);
			             $zimu_arr2=array(-20284,-19776,-19219,-18711  ,-18527,-18240,-17923,-17418,-1,-16475,-16213,-15641,-15166,-14923,-14915,-14631,-14150,-14091,-13319,-12839,-1,-1,-12557,-11848,-11056,-2050);
                         if(!in_array(strtoupper($zmall[1]),$zimu_arr)){ //其他
                              $sql.=" and substring( ".$zmall[0].", 1, 1 ) NOT REGEXP '^[a-zA-Z]' and substring( ".$zmall[0].", 1, 1 ) REGEXP '^[u4e00-u9fa5]'";
						 }else{
			                  $posarr=array_keys($zimu_arr,strtoupper($zmall[1]));
			                  $pos=$posarr[0];
			                  $sql.=" and (((ord( substring(".$zmall[0].", 1, 1 ) ) -65536>=".($zimu_arr1[$pos])." and  ord( substring( ".$zmall[0].", 1, 1 ) ) -65536<=".($zimu_arr2[$pos]).")) or UPPER(substring( ".$zmall[0].", 1, 1 ))='".$zimu_arr[$pos]."')";
						 }
					 }
				 }elseif ($ci->db->field_exists($v[1], $table)){ //判断条件字段是否存在

				     if($v[2]=='auto' || substr($v[2],0,5)=='auto,'){ //当前分类
						  $auall=explode(",",$v[2]);
						  if(!empty($auall[1])){
							  if(empty($autoarr[$auall[1]])){
                                  $vs=0;
							  }else{
                                  $vs=(is_array($autoarr))?$autoarr[$auall[1]]:$autoarr;
							  }
						  }else{
							  if(empty($autoarr[$v[1]])){
                                  $vs=0;
							  }else{
                                  $vs=(is_array($autoarr))?$autoarr[$v[1]]:$autoarr;
							  }
						  }
						  if($v[1]=='tags'){ //TAGS标签
					           $sql.=" and (".$this->gettags($vs).")";
						  }elseif(!empty($vs)){
					           $sql.=" and ".$v[1]." in (".$vs.")";
						  }
				     }else{
						  if(strpos($v[2],',') === FALSE){
			                  $sql.=" and ".$v[1]."=".$v[2];
						  }else{
			                  $sql.=" and ".$v[1]." in (".$v[2].")";
						  }
					 }
				 }
				 if($v[1]=='hid') $hid=1;
				 if($v[1]=='yid') $yid=1;
		     }
			 //判断审核字段
             if ($yid==0 && strpos($sql,'yid=')===FALSE && $ci->db->field_exists('yid', $table)){
				 $sql.=' and yid=0';
		     }
			 //判断回收站字段
             if ($hid==0 && strpos($sql,'hid=')===FALSE && $ci->db->field_exists('hid', $table)){
				 $sql.=' and hid=0';
		     }
			 if($cid>0) $sql.=" and cid=".$cid;
			 $sort=($sorts)?$sorts:$this->arr_val('sort',$matches);
			 if(!$sort) $sort='id';
			 $order=$this->arr_val('order',$matches);
			 if(!$order) $order='desc';
			 if($sort=='rand') $sort='rand()';
			 $sql.=" order by ".$sort." ".$order;  
			 $loop=(int)$this->arr_val('loop',$matches);
			 $start=(int)$this->arr_val('start',$matches);
			 if(!$start) $start=1;
			 if($loop>0) $sql.=" limit ".($start-1).",".($loop)."";
		     unset($matches);
		     return $sql;
	}

	//解析SQL标签
	public function cscms_sql_to($fields, $para, $str_arr, $label, $str, $autoarr) {
		     $sql=$this->cscms_sql($fields, $para, $str_arr, $label, '', $autoarr);
	 	     $ci = &get_instance();
	 	     if (!isset($ci->db)){
        	     $ci->load->database();
	 	     }
		     $result_array=$ci->db->query($sql)->result_array();
			 if(!$result_array){
					$Data_Content="";
					$str=str_replace($str_arr,$Data_Content,$str);
			 }else{
					$Data_Content='';$sorti=1;
					foreach ($result_array as $row) {
                          $Data_Content.=$this->cscms_skins($fields,$str,$label,$row,$sorti,$autoarr);
						  $sorti++;
					}
			 }
			 $str=str_replace($str_arr,$Data_Content,$str);	
		     return $str;
	}

	//解析CSCMS标签
	public function cscms_skins($field, $str, $label, $row, $sorti=1, $autoarr=array()) {

		     preg_match_all('/\['.$field.':\s*([0-9a-zA-Z\_\-]+)([\s]*[link|ulink|dir|level|zd|len|style]*)[=]??([\d0-9a-zA-Z\,\{\}\/\-\\\\:\s]*)\]/',$str,$field_arr);
		     if(!empty($field_arr)){
                 //判断是否嵌套二级
				 preg_match('/{cscmstype:([\S]+)\s+(.*?)}([\s\S]+?){\/cscmstype:\1}/',$label,$type_arr);
			     if(!empty($type_arr)){
			         $label=$this->cscms_sql_to($type_arr[1],$type_arr[2],$type_arr[0],$type_arr[3],$label,$row['id']);
				 }
		         unset($type_arr);
			     for($i=0;$i<count($field_arr[0]);$i++){
					 $type=$field_arr[1][$i];
					 if(array_key_exists($type,$row) && trim($field_arr[2][$i])!='zd'){
						    if($type=='addtime'){
						         $label=str_replace('['.$field.':'.$type.']',date('Y-m-d H:i:s',$row[$type]),$label);
							}else{
						         $label=str_replace('['.$field.':'.$type.']',$row[$type],$label);
							}

							//判断自定义标签
                            if(!empty($field_arr[2][$i]) && !empty($field_arr[3][$i])){
                                 //格式化时间
								 if(trim($field_arr[2][$i])=='style' && trim($field_arr[3][$i])=='time'){
									 $label=str_replace($field_arr[0][$i],datetime($row[$type]),$label);
                                 //获取IP地区
								 }elseif(trim($field_arr[2][$i])=='style' && trim($field_arr[3][$i])=='city'){
	 								 $ci = &get_instance();
									 $ci->load->library('ip');
									 $label=str_replace($field_arr[0][$i],$ci->ip->address($row[$type]),$label);
                                 //自定义时间
								 }elseif(trim($field_arr[2][$i])=='style'){
									 $label=str_replace($field_arr[0][$i],date(str_replace('f','i',$field_arr[3][$i]),$row[$type]),$label);
                                 //图片地址
								 }elseif(trim($field_arr[2][$i])=='dir'){
                                     $lall=explode(",",$field_arr[3][$i]);
                                     $lass=count($lall)>1?$lall[1]:'';
									 $pic=piclink($lall[0],$row[$type],$lass);
									 $label=str_replace($field_arr[0][$i],$pic,$label);
								 }
								 //字符截取
                                 if(trim($field_arr[2][$i])=='len'){
									 $label=str_replace($field_arr[0][$i],sub_str(str_checkhtml($row[$type]),$field_arr[3][$i]),$label);
								 }
							}
					 }else{  //外部字段

            			 	switch($type){
								  //序
				    			  case 'i'  :  
									       $label=str_replace($field_arr[0][$i],$sorti,$label);
								  break;
								  //序
				    			  case 'addres'  :  
                                           if(trim($field_arr[2][$i])=='zd' && !empty($field_arr[3][$i]) && array_key_exists($field_arr[3][$i],$row)){
									            $zd=$field_arr[3][$i];
                                                $ci = &get_instance();
												$ci->load->library('ip');
									            $label=str_replace($field_arr[0][$i],$ci->ip->address($row[$zd]),$label);
								           }
								  break;
								  //数据统计
				    			  case 'count'  : 
                                           if(trim($field_arr[2][$i])=='zdy' && !empty($field_arr[3][$i])){
												$count=0;
												$arr=explode(',',$field_arr[3][$i]);
												$table=$arr[0];
                                                $czd=empty($arr[1])?'id':$arr[1];
                                                $szd=empty($arr[2])?'id':$arr[2];
												if(array_key_exists($szd,$row)){
	 									             $ci = &get_instance();
	 									             if (!isset($ci->db)){
        									             $ci->load->database();
	 									             }
												     $uid=!empty($row[$szd])?$row[$szd]:(!empty($row['uid'])?$row['uid']:0);
												     if(!empty($table) && $ci->db->table_exists(CS_SqlPrefix.$table) && $ci->db->field_exists($czd, CS_SqlPrefix.$table)){
												          if(!empty($arr[3]) && (!empty($arr[4]) || (int)$arr[4]==0)){
															  if($ci->db->field_exists($arr[3], CS_SqlPrefix.$table)){
                                                                   $count=$ci->db->query("SELECT id FROM ".CS_SqlPrefix.$table." where ".$czd."='".$uid."' and ".$arr[3]."='".$arr[4]."'")->num_rows();
															  }
												          }else{
												               $count=$ci->db->query("SELECT id FROM ".CS_SqlPrefix.$table." where ".$czd."='".$uid."'")->num_rows();
														  }
												     }
												}
									            $label=str_replace($field_arr[0][$i],$count,$label);
								           }
								  break;
								  //会员信息
				    			  case 'user'  :  
									       if(($field=='user' or array_key_exists('uid',$row) or array_key_exists('uidb',$row)) && trim($field_arr[2][$i])=='zd' && !empty($field_arr[3][$i])){
	 									        $ci = &get_instance();
	 									        if (!isset($ci->db)){
        									        $ci->load->database();
	 									        }
												$ziduan=$field_arr[3][$i];
												$zdneir=($field=='gbook' or $field=='pl')?'游客':'null';
												if($field=='user'){
													$uid=$row['id'];
												}else{
													$lall=explode(",",$ziduan);
													if(!empty($lall[1])){
                                                       $uid=!empty($lall[1])?$lall[1]:0;
													   $ziduan=$lall[1];
													}else{
												       if(!empty($row['uid'])){
														   $uid=$row['uid'];
													   }else{
														   $uid=!empty($row['uidb'])?$row['uidb']:0;
													   }
													}
												}
												$czd=($ziduan=='nichen')?$ziduan.',name':$ziduan;
												if($ci->db->field_exists($ziduan, CS_SqlPrefix.'user')){
												     $rowu=$ci->db->query("SELECT ".$czd." FROM ".CS_SqlPrefix."user where id='".$uid."'")->row();
												     if($rowu){
													      $zdneir=($field_arr[3][$i]=='nichen' && empty($rowu->$ziduan))?$rowu->name:$rowu->$ziduan;
													 }
												}
												if($ziduan=='logo'){
													if($uid==0) $zdneir='';
                                                    $zdneir=piclink('logo',$zdneir);
												}
												if($ziduan=='zid'){
													if($zdneir==0) $zdneir=1;
                                                    $zdneir=getzd('userzu','name',$zdneir);
												}
												if($ziduan=='qianm'){
													if(empty($zdneir)) $zdneir='暂时没有签名...';
												}
												if($ziduan=='city'){
													if(empty($zdneir)) $zdneir='保密';
												}
									            $label=str_replace($field_arr[0][$i],$zdneir,$label);
										   //会员等级
								           }elseif(($field=='user' or array_key_exists('uid',$row) or array_key_exists('uidb',$row)) && trim($field_arr[2][$i])=='level' && !empty($field_arr[3][$i])){
											    $zdneir='';
												if($field=='user'){
													$uid=$row['id'];
												}else{
												    $uid=!empty($row['uid'])?$row['uid']:(!empty($row['uidb'])?$row['uidb']:0);
												}
												$jinyan=getzd('user','jinyan',$uid);
                                                if($field_arr[3][$i]=='1'){ //星星数
                                                      $zdneir=getlevel($jinyan,1);
												}
                                                if($field_arr[3][$i]=='2'){ //下个级别需要经验
                                                      $zdneir=getlevel($jinyan,2);
												}
                                                if($field_arr[3][$i]=='3'){ //下个级别剩余经验
                                                      $zdneir=getlevel($jinyan,3);
												}
                                                if($field_arr[3][$i]=='4'){ //剩余百分比
                                                      $zdneir=getlevel($jinyan,4);
												}
                                                if($field_arr[3][$i]=='5'){ //名称
                                                      $zdneir=getlevel($jinyan,5);
												}
									            $label=str_replace($field_arr[0][$i],$zdneir,$label);
										   }
								  break;
								  //歌手信息
				    			  case 'singer'  :  
									       if(array_key_exists('singerid',$row) && trim($field_arr[2][$i])=='zd' && !empty($field_arr[3][$i])){
	 									        $ci = &get_instance();
	 									        if (!isset($ci->db)){
        									        $ci->load->database();
	 									        }
												$zdneir='null';
												if($ci->db->table_exists(CS_SqlPrefix.'singer')){  //歌手表存在
												     $ziduan=$field_arr[3][$i];
												     if($ci->db->field_exists($ziduan, CS_SqlPrefix.'singer')){
												          $rows=$ci->db->query("SELECT ".$ziduan." FROM ".CS_SqlPrefix."singer where id='".$row['singerid']."'")->row();
												          if($rows){
													           $zdneir=$rows->$ziduan;
													      }
													 }
													 if($ziduan=='pic'){
														 if($row['singerid']==0) $zdneir='';
                                                    	 $zdneir=piclink('singer',$zdneir);
													 }
												}
									            $label=str_replace($field_arr[0][$i],$zdneir,$label);
								           }
								  break;
								  //版块链接
				    			  case 'murl'  :  
									       if(array_key_exists('dir',$row)){
										        $link=cscmslink($row['dir']);
									            $label=str_replace($field_arr[0][$i],$link,$label);
								           } 
								  break;
								  //网站链接
				    			  case 'url'  : 
									  //全局
									  if(array_key_exists('id',$row) && trim($field_arr[2][$i])=='link' && !empty($field_arr[3][$i])){
									       $lall=explode(",",$field_arr[3][$i]);
										   $lass=count($lall)>1?$lall[1]:'';
										   $link=linkurl($lall[0],$lass,$row['id']);
									       $label=str_replace($field_arr[0][$i],$link,$label);
								      }
									  //会员
									  if((array_key_exists('uid',$row) || array_key_exists('uidb',$row) || $field=='user') && trim($field_arr[2][$i])=='ulink' && !empty($field_arr[3][$i])){
	 									        $ci = &get_instance();
	 									        if (!isset($ci->db)){
        									        $ci->load->database();
	 									        }
												$link='';
												if($field=='user'){
													 if(array_key_exists('id',$row) && array_key_exists('name',$row)){
									                      $lall=explode(",",$field_arr[3][$i]);
                                                          $lass=count($lall)>1?$lall[1]:'';
													      $link=userlink($lall[0],$row['id'],$row['name'],$lass);
													 }
												}else{
													 $uid=!empty($row['uid'])?$row['uid']:(!empty($row['uidb'])?$row['uidb']:0);
												     $rowu=$ci->db->query("SELECT id,name FROM ".CS_SqlPrefix."user where id='".$uid."'")->row();
												     if(!$rowu){
													      $link='http://'.Web_Url.Web_Path;
												     }else{
									                      $lall=explode(",",$field_arr[3][$i]);
														  $lass=count($lall)>1?$lall[1]:'';
													      $link=userlink($lall[0],$rowu->id,$rowu->name,$lass);
												     }
												}
									            $label=str_replace($field_arr[0][$i],$link,$label);
								      }
									  //会员中心...
									  if(trim($field_arr[2][$i])=='userlink' && !empty($field_arr[3][$i])){
										        $link=spacelink($field_arr[3][$i]);
									            $label=str_replace($field_arr[0][$i],$link,$label);
								      }
									  //自定义URL，板块，字段，参数，参数...
									  if(trim($field_arr[2][$i])=='zdy' && !empty($field_arr[3][$i])){
										        $lall=explode(",",$field_arr[3][$i]);
												if(!array_key_exists($lall[1],$row) || $row[$lall[1]]==0){
													 $link='http://'.Web_Url.Web_Path;
												}else{
										             $lass=count($lall)>3?$lall[3]:'';
													 $link=linkurl($lall[2],$lass,$row[$lall[1]],1,$lall[0]);
												}
									            $label=str_replace($field_arr[0][$i],$link,$label);
								      }
				    			  break;
		     			 	}
					 }
				 }
			 }
		     unset($field_arr);
		     return $label;
	}

    // 会员主页模板标签处理
    public function cscms_web($web,$content,$str) {
        $dir=CSCMS.'tpl/home/';
		$newweb='';
	    if (is_dir($dir)){
		    if ($dh = opendir($dir)){
			    while (($file = readdir($dh))!= false){
				    $filePath = $dir.$file;
					if (is_dir($filePath)){
                      $confiles=$filePath.'/config.php';
				      if (file_exists($confiles)){
						  $con=require_once($confiles);
						  $vip='全部级别';
						  if($con['vip']>0){
                              $vip=getzd('userzu','name',$con['vip']);
						  }
						  $level='全部等级';
						  if($con['level']>0){
                              $level=getzd('userlevel','name',$con['level']);
						  }
						  $pic=file_exists($filePath.'/preview.jpg')?Web_Path.'cscms/tpl/home/'.$con['dir'].'/preview.jpg':Web_Path.'packs/images/skins.jpg';
						  $ystr=array('[web:pic]','[web:name]','[web:dir]','[web:path]','[web:vip]','[web:level]','[web:cion]');
						  $xstr=array($pic,$con['name'],$con['dir'],$con['path'],$vip,$level,$con['cion']);
                          $newweb.=str_replace($ystr,$xstr,$content);
					  }
					}
			    }
			    closedir($dh);
		    }
	    }
        $str=str_replace($web,$newweb,$str);
		return $str;
    }

    // php标签处理
    public function cscms_php($php,$content,$str) {
		$evalstr=" return $content";
		$newsphp=@eval($evalstr);
        $str=str_replace($php,$newsphp,$str);
		return $str;
    }

	//if标签处理
	public function labelif($Mark_Text){
		preg_match_all('/{if:(.*?)}(.*?){end if}/',$Mark_Text,$arr2);
		if(!empty($arr2[0]) && !empty($arr2[0][0])){
			     for($i=0;$i<count($arr2[0]);$i++){
				     $else=explode('{else}',$arr2[2][$i]);
				     if(count($else)==2){
					     $evalstr="if(".$arr2[1][$i]."){ return '".addslashes($else[0])."';}else{return '".addslashes($else[1])."';}";	
					     $str=@eval($evalstr);
						 $str=str_replace("\\","",$str);
					     $Mark_Text=str_replace($arr2[0][$i],$str,$Mark_Text);
				     }else{
					     $evalstr="if(".$arr2[1][$i]."){ return '".addslashes($arr2[2][$i])."' ;}";
					     $str=@eval($evalstr);
						 $str=str_replace("\\","",$str);
					     $Mark_Text=str_replace($arr2[0][$i],$str,$Mark_Text);
				     }
			     }
		}
		preg_match_all('/{if:([\S]+)}([\s\S]+?){end if}/',$Mark_Text,$arr);
	    if(!empty($arr[0]) && !empty($arr[0][0])){
			     for($i=0;$i<count($arr[0]);$i++){
				     $else=explode('{else}',$arr[2][$i]);
				     if(count($else)==2){
					     $evalstr="if(".$arr[1][$i]."){ return '".addslashes($else[0])."';}else{return '".addslashes($else[1])."';}";	
					     $str=eval($evalstr);
						 $str=str_replace("\\","",$str);
					     $Mark_Text=str_replace($arr[0][$i],$str,$Mark_Text);
			     	}else{
					     $evalstr="if(".$arr[1][$i]."){ return '".addslashes($arr[2][$i])."' ;}";
					     $str=eval($evalstr);
						 $str=str_replace("\\","",$str);
					     $Mark_Text=str_replace($arr[0][$i],$str,$Mark_Text);
				     }
			     }
		}
		return $Mark_Text;
	}

	//查找数组指定元素
	public function arr_val($key,$array){
		     foreach ($array as $v) {
                  if(strtolower($v[1])==$key){
                       return $v[2];
					   break;
				  }
			 }
			 return NULL;
	}

    //相关标签转换
	public function gettags($tags){
	         $tags_list=$tags_key="";
             $tags=trim($tags);
             $Str=" @,@，@|@/@_";
             $StrArr=explode('@',$Str);
             for($i=0;$i<=5;$i++){
                    if(stristr($tags,$StrArr[$i])){
                         $tags_key=explode($StrArr[$i],$tags);
                    }
	         }
             if(is_array($tags_key)){
                    for($j=0;$j<count($tags_key);$j++){
                         $tags_list.=" tags like '%".$tags_key[$j]."%' or";
			        }
	         }else{
                    $tags_list.=" tags like '%".$tags_key."%'";
	         }
             if(substr($tags_list,-2)=='or'){
                 $tags_list=substr($tags_list,0,-2);
	         }
             return $tags_list;
	}
}
