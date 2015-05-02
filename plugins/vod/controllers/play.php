<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-09-20
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Play extends Cscms_Controller {
	function __construct(){
		    parent::__construct();
			$this->load->helper('vod');
		    $this->load->model('CsdjTpl');
		    $this->load->model('CsdjUser');
	}

    //歌曲播放
	public function index($a1 , $a2 = 0, $a3 = 0, $a4 = 0)
	{
            if(intval($a1)>0){
                $id = intval($a1);   //ID
                $zu = intval($a2);   //组
                $ji = intval($a3);   //集数
			}else{
                $id = intval($a2);   //ID
                $zu = intval($a3);   //组
                $ji = intval($a4);   //集数
			}
			$login='no';

            //判断ID
            if($id==0) msg_url('出错了，ID不能为空！',Web_Path);
            //获取数据
		    $row=$this->CsdjDB->get_row_arr('vod','*',$id);
		    if(!$row || $row['yid']>0 || $row['hid']>0){
                     msg_url('出错了，该数据不存在或者没有审核！',Web_Path);
		    }
		    if(empty($row['purl'])){
                     msg_url('该视频播放地址不正确！',Web_Path);
		    }

            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['play']['check']==1 && !defined('MOBILE')){
                //获取静态路径
				$Htmllink=VodPlayUrl('play',$id,$zu,$ji);
				header("Location: ".$Htmllink);
				exit;
			}

			//判断收费
            if($row['vip']>0 || $row['level']>0 || $row['cion']>0){
                  if(!$this->CsdjUser->User_Login(1)){
					  msg_url('观看这部视频需要登录，请先登录！',spacelink('login'));
				  }
				  $rowu=$this->CsdjDB->get_row_arr('user','vip,level,cion',$_SESSION['cscms__id']);
			}
            //判断会员组下载权限
			if($row['vip']>0 && $row['uid']!=$_SESSION['cscms__id']){
				  if($row['vip']>$rowu['vip']){
                       msg_url('抱歉，您所在的会员组不能观看该视频，请先升级！','javascript:window.close();');
				  }
			}

            //判断会员等级下载权限
			if($row['level']>0 && $row['uid']!=$_SESSION['cscms__id']){
				  if($row['level']>$rowu['level']){
                       msg_url('抱歉，您等级不够，不能观看该视频！','javascript:window.close();');
				  }
			}

            //判断金币下载
			$down=0;
			if($row['cion']>0 && $row['uid']!=$_SESSION['cscms__id']){

				  //判断是否下载过
				  $did=$id.'-'.$zu.'-'.$ji;
				  $rowd=$this->db->query("SELECT id,addtime FROM ".CS_SqlPrefix."vod_look where did='".$did."' and uid='".$_SESSION['cscms__id']."' and sid=0")->row_array();
				  if($rowd){
					  $down=1; //数据已经存在
					  $downtime=User_Downtime*3600+$rowd['addtime'];
					  if($downtime>time()){
				           $down=2; //在多少时间内不重复扣币
					  }
				  }
				  //判断会员组下载权限
				  $rowz=$this->db->query("SELECT id,did FROM ".CS_SqlPrefix."userzu where id='".$rowu['vip']."'")->row_array();
				  if($rowz && $rowz['did']==1){ //有免费下载权限
                       $down=2; //该会员下载不收费
				  }
                  if($down<2){ //判断扣币
				      if($row['cion']>$rowu['cion']){
                           msg_url('这部视频观看每集需要'.$row['cion'].'个金币，您的当前金币不够，请先充值！','javascript:window.close();');
				      }else{
						  //扣币
						  $edit['cion']=$rowu['cion']-$row['cion'];
						  $this->CsdjDB->get_update('user',$_SESSION['cscms__id'],$edit);
						  //写入消费记录
						  $add2['title']='观看视频《'.$row['name'].'》- 第'.($ji+1).'集';
						  $add2['uid']=$_SESSION['cscms__id'];
						  $add2['nums']=$row['cion'];
						  $add2['ip']=getip();
						  $add2['dir']='vod';
                          $add2['addtime']=time();
					      $this->CsdjDB->get_insert('spend',$add2);

						  //判断分成
						  if(User_DownFun==1 && $row['uid']>0){
							     //分成比例
								 $bi=(User_Downcion<10)?'0.0'.User_Downcion:'0.'.User_Downcion;
                                 $scion= intval($row['cion'] * $bi);
								 if($scion>0){
						              $this->db->query("update ".CS_SqlPrefix."user set cion=cion+".$scion." where id=".$row['uid']."");
						              //写入分成记录
						              $add3['title']='视频《'.$row['name'].'》- 第'.($ji+1).'集 - 观看分成';
						              $add3['uid']=$row['uid'];
						              $add3['dir']='vod';
						              $add3['nums']=$scion;
						              $add3['ip']=getip();
                                      $add3['addtime']=time();
					                  $this->CsdjDB->get_insert('income',$add3);
								 }
						  }
					  }
				  }
			      //增加观看记录
			      if($down==0){
                       $add['name']=$row['name'];
                       $add['cid']=$row['cid'];
                       $add['sid']=0;
                       $add['did']=$did;
                       $add['uid']=$_SESSION['cscms__id'];
                       $add['cion']=$row['cion'];
                       $add['addtime']=time();
					   $this->CsdjDB->get_insert('vod_look',$add);
			      }
			}

			//摧毁部分需要超级链接字段数组
			$rows=$row; //先保存数组保留下面使用
			unset($row['zhuyan']);
			unset($row['daoyan']);
			unset($row['yuyan']);
			unset($row['diqu']);
			unset($row['tags']);
			unset($row['year']);
			unset($row['pfen']);
			unset($row['phits']);
			//获取当前分类下二级分类ID
			$arr['cid']=getChild($row['cid']);
			$arr['uid']=$row['uid'];
			$arr['singerid']=$row['singerid'];
			$arr['tags']=$rows['tags'];
			$skins=$row['skins'];
			if(empty($skins) || $skins=='play.html'){
			     $skins=getzd('vod_list','skins3',$row['cid']);
			}
			if(empty($skins)) $skins='play.html';
			//装载模板并输出
	        $Mark_Text=$this->CsdjTpl->plub_show('vod',$row,$arr,TRUE,$skins,$row['name'],$row['name']);
			//评论
			$Mark_Text=str_replace("[vod:pl]",get_pl('vod',$id),$Mark_Text);
			//分类地址、名称
			$Mark_Text=str_replace("[vod:zu]",($zu+1),$Mark_Text);
			$Mark_Text=str_replace("[vod:ji]",($ji+1),$Mark_Text);
			$Mark_Text=str_replace("[vod:link]",LinkUrl('show','id',$row['id'],1,'vod'),$Mark_Text);
			$Mark_Text=str_replace("[vod:playlink]",VodPlayUrl('play',$id,$zu,$ji),$Mark_Text);
			$Mark_Text=str_replace("[vod:classlink]",LinkUrl('lists','id',$row['cid'],1,'vod'),$Mark_Text);
			$Mark_Text=str_replace("[vod:classname]",$this->CsdjDB->getzd('vod_list','name',$row['cid']),$Mark_Text);
			//主演、导演、标签、年份、地区、语言加超级连接
			$Mark_Text=str_replace("[vod:zhuyan]",SearchLink($rows['zhuyan'],'zhuyan'),$Mark_Text);
			$Mark_Text=str_replace("[vod:daoyan]",SearchLink($rows['daoyan'],'daoyan'),$Mark_Text);
			$Mark_Text=str_replace("[vod:yuyan]",SearchLink($rows['yuyan'],'yuyan'),$Mark_Text);
			$Mark_Text=str_replace("[vod:diqu]",SearchLink($rows['diqu'],'diqu'),$Mark_Text);
			$Mark_Text=str_replace("[vod:tags]",SearchLink($rows['tags']),$Mark_Text);
			$Mark_Text=str_replace("[vod:year]",SearchLink($rows['year'],'year'),$Mark_Text);
			//评分
			$Mark_Text=str_replace("[vod:pfen]",getpf($rows['pfen'],$rows['phits']),$Mark_Text);
			$Mark_Text=str_replace("[vod:pfenbi]",getpf($rows['pfen'],$rows['phits'],2),$Mark_Text);
			//解析播放地址
            $Mark_Text=Vod_Playlist($Mark_Text,'play',$id,$row['purl']);
			//播放器
	        $Data_Arr=explode("#cscms#",$row['purl']);
            if($zu>=count($Data_Arr)) $zu=0;
		    $DataList_Arr=explode("\n",$Data_Arr[$zu]);
		    $Dataurl_Arr=explode('$',$DataList_Arr[$ji]);

		    $xpurl="";  //下集播放地址
		    $laiyuan=str_replace("\r","",@$Dataurl_Arr[2]); //来源
		    $url=$Dataurl_Arr[1];  //地址
		    $pname=$Dataurl_Arr[0];  //当前集数
		    $Mark_Text=str_replace("[vod:qurl]",$url,$Mark_Text);
		    $Mark_Text=str_replace("[vod:laiy]",$laiyuan,$Mark_Text);
		    $Mark_Text=str_replace("[vod:ji]",$pname,$Mark_Text);
			//手机播放地址
			if(substr($url,0,7)=='http://'){
			     $wapurl=$url;
			}else{
			     $wapurl='http://download.chshcms.com/mp4/'.$laiyuan.'/'.cs_base64_encode($url).'/cscms.mp4';
			}
		    $Mark_Text=str_replace("[vod:wapurl]",$wapurl,$Mark_Text);

		    if(count($DataList_Arr)>($ji+1)){
			    $DataNext=$DataList_Arr[($ji+1)];
			    $DataNextArr=explode('$',$DataNext);
			    if(count($DataNextArr)==2) $DataNext=$DataNextArr[1];
			    $xurl=VodPlayUrl('play',$id,$zu,($ji+1));
		        $Dataurl_Arr2=explode('$',$DataList_Arr[($ji+1)]);
                $xpurl=@$Dataurl_Arr2[1];  //下集播放地址
		    }else{
			    $DataNext=$DataList_Arr[$ji];
			    $DataNextArr=explode('$',$DataNext);
			    if(count($DataNextArr)==2) $DataNext=$DataNextArr[1];			
			    $xurl='#';
                $xpurl='';  //下集播放地址
		    }
            if($ji==0){
			    $surl='#';
            }else{
			    $surl=VodPlayUrl('play',$id,$zu,($ji-1));
            }
            $psname='';
			for($j=0;$j<count($Data_Arr);$j++){
				   $jis='';
			       $Ji_Arr=explode("\n",$Data_Arr[$j]);
                   for($k=0;$k<count($Ji_Arr);$k++){
			            $Ly_Arr=explode('$',$Ji_Arr[$k]);
						$jis.=$Ly_Arr[0].'$$'.@$Ly_Arr[2].'====';
				   }
				   $psname.=substr($jis,0,-4).'#cscms#';
			}
            $player_arr=str_replace("\r","",substr($psname,0,-7));
	        if($laiyuan=='xgvod'||$laiyuan=='jjvod'||$laiyuan=='yyxf'||$laiyuan=='bdhd'||$laiyuan=='qvod'){
				$xpurl=str_replace("+","__",base64_encode($xpurl));
	            $url=str_replace("+","__",base64_encode($url));
			}else{
				$xpurl=escape($xpurl);
	            $url=escape($url);
			}
		    $player="<script type='text/javascript' src='".hitslink('play/form','vod')."'></script><script type='text/javascript'>var cs_playlink='".VodPlayUrl('play',$id,$zu,$ji,1)."';var cs_did='".$id."';var player_name='".$player_arr."';var cs_pid='".$ji."';var cs_zid='".$zu."';var cs_vodname='".$row['name']." - ".$pname."';var cs_root='".Web_Path."';var cs_width=".CS_Play_sw.";var cs_height=".CS_Play_sh.";var cs_surl='".$surl."';var cs_xurl='".$xurl."';var cs_url='".$url."';var cs_xpurl='".$xpurl."';var cs_laiy='".$laiyuan."';var cs_adloadtime='".CS_Play_AdloadTime."';</script><iframe border=\"0\" name=\"cscms_vodplay\" id=\"cscms_vodplay\" src=\"".Web_Path."packs/vod_player/play.html\" marginwidth=\"0\" framespacing=\"0\" marginheight=\"0\" noresize=\"\" vspale=\"0\" style=\"z-index: 9998;\" frameborder=\"0\" height=\"".(CS_Play_sh+30)."\" scrolling=\"no\" width=\"100%\"></iframe>";
		    $Mark_Text=str_replace("[vod:player]",$player,$Mark_Text);
		    $Mark_Text=str_replace("[vod:surl]",$surl,$Mark_Text);
		    $Mark_Text=str_replace("[vod:xurl]",$xurl,$Mark_Text);
			//增加人气
			$Mark_Text=hits_js($Mark_Text,hitslink('hits/ids/'.$id,'vod'));
            echo $Mark_Text;
			$this->cache->end(); //由于前面不是直接输出，所以这里需要加入写缓存
	}

    //判断权限、积分
	public function pay($id=0,$zu=0,$ji=0)
	{

            //判断ID
            if($id==0) exit();
            //获取数据
		    $row=$this->CsdjDB->get_row_arr('vod','name,cid,uid,yid,hid,id,vip,level,cion,purl',$id);
		    if(!$row || $row['yid']>0 || $row['hid']>0){
                     exit("alert('数据没有审核，或者被删除~!');");
		    }
		    if(empty($row['purl'])){
					 exit("alert('视频播放地址不正确！');");
		    }

			//判断收费
            if($row['vip']>0 || $row['level']>0 || $row['cion']>0){
                  $login=$this->CsdjUser->User_Login(1);
				  if(!$login) exit("alert('抱歉，该视频需要登录才能观看，请先登录！');");
				  $rowu=$this->CsdjDB->get_row_arr('user','vip,level,cion',$_SESSION['cscms__id']);
			}

            //判断会员组下载权限
			if($row['vip']>0 && $row['uid']!=$_SESSION['cscms__id']){
				  if($row['vip']>$rowu['vip']){
					   exit("alert('抱歉，您所在的会员组不能观看该视频，请先升级！');");
				  }
			}

            //判断会员等级下载权限
			if($row['level']>0 && $row['uid']!=$_SESSION['cscms__id']){
				  if($row['level']>$rowu['level']){
					   exit("alert('抱歉，您等级不够，不能观看该视频！');");
				  }
			}

            //判断金币下载
			$down=0;
			if($row['cion']>0 && $row['uid']!=$_SESSION['cscms__id']){

				  //判断是否下载过
				  $did=$id.'-'.$zu.'-'.$ji;
				  $rowd=$this->db->query("SELECT id,addtime FROM ".CS_SqlPrefix."vod_look where did='".$did."' and uid='".$_SESSION['cscms__id']."' and sid=0")->row_array();
				  if($rowd){
					  $down=1; //数据已经存在
					  $downtime=User_Downtime*3600+$rowd['addtime'];
					  if($downtime>time()){
				           $down=2; //在多少时间内不重复扣币
					  }
				  }
				  //判断会员组下载权限
				  $rowz=$this->db->query("SELECT id,did FROM ".CS_SqlPrefix."userzu where id='".$rowu['vip']."'")->row_array();
				  if($rowz && $rowz['did']==1){ //有免费下载权限
                       $down=2; //该会员下载不收费
				  }
                  if($down<2){ //判断扣币
				      if($row['cion']>$rowu['cion']){
						   exit("alert('这部视频观看每集需要".$row['cion']."个金币，您的当前金币不够，请先充值！');");
				      }else{
						  //扣币
						  $edit['cion']=$rowu['cion']-$row['cion'];
						  $this->CsdjDB->get_update('user',$_SESSION['cscms__id'],$edit);
						  //写入消费记录
						  $add2['title']='观看视频《'.$row['name'].'》- 第'.($ji+1).'集';
						  $add2['uid']=$_SESSION['cscms__id'];
						  $add2['nums']=$row['cion'];
						  $add2['ip']=getip();
						  $add2['dir']='vod';
                          $add2['addtime']=time();
					      $this->CsdjDB->get_insert('spend',$add2);

						  //判断分成
						  if(User_DownFun==1 && $row['uid']>0){
							     //分成比例
								 $bi=(User_Downcion<10)?'0.0'.User_Downcion:'0.'.User_Downcion;
                                 $scion= intval($row['cion'] * $bi);
								 if($scion>0){
						              $this->db->query("update ".CS_SqlPrefix."user set cion=cion+".$scion." where id=".$row['uid']."");
						              //写入分成记录
						              $add3['title']='视频《'.$row['name'].'》- 第'.($ji+1).'集 - 观看分成';
						              $add3['uid']=$row['uid'];
						              $add3['dir']='vod';
						              $add3['nums']=$scion;
						              $add3['ip']=getip();
                                      $add3['addtime']=time();
					                  $this->CsdjDB->get_insert('income',$add3);
								 }
						  }
					  }
				  }
			      //增加观看记录
			      if($down==0){
                       $add['name']=$row['name'];
                       $add['cid']=$row['cid'];
                       $add['sid']=0;
                       $add['did']=$did;
                       $add['uid']=$_SESSION['cscms__id'];
                       $add['cion']=$row['cion'];
                       $add['addtime']=time();
					   $this->CsdjDB->get_insert('vod_look',$add);
			      }
			}
            $xpurl="";  //下集播放地址
            $Data_Arr=explode("#cscms#",$row['purl']);
            if($zu>=count($Data_Arr)) $zu=0;
		    $DataList_Arr=explode("\n",$Data_Arr[$zu]);
		    $Dataurl_Arr=explode('$',$DataList_Arr[$ji]);
			$laiyuan=$Dataurl_Arr[2]; //来源
		    $url=$Dataurl_Arr[1];  //地址
		    if(count($DataList_Arr)>($ji+1)){
		           $Dataurl_Arr2=explode('$',$DataList_Arr[($ji+1)]);
                   $xpurl=@$Dataurl_Arr2[1];  //下集播放地址
		    }else{
                   $xpurl='';  //下集播放地址
		    }
	        if($laiyuan=='xgvod'||$laiyuan=='jjvod'||$laiyuan=='yyxf'||$laiyuan=='bdhd'||$laiyuan=='qvod'){
				   $xpurl=str_replace("+","__",base64_encode($xpurl));
	               $url=str_replace("+","__",base64_encode($url));
			}else{
				   $xpurl=escape($xpurl);
	               $url=escape($url);
			}
			//手机播放地址
			if(substr($url,0,7)=='http://'){
			     $url=$url;
			}else{
			     $url='http://download.chshcms.com/mp4/'.$laiyuan.'/'.cs_base64_encode($url).'/cscms.mp4';
			}
			echo "var cs_url='".$url."';var cs_xpurl='".$xpurl."';";
	}

    //播放器配置
	public function form()
	{
		    $str='var cscms_vod_player={};';
            require_once APPPATH.'config/player.php';
			$player=$player_config;
			for ($i=0; $i<count($player); $i++) {
					$str.="cscms_vod_player['".$player[$i]['form']."']='".$player[$i]['name']."';";
			}
			echo $str;
	}
}

