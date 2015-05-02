<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Down extends Cscms_Controller {
	function __construct(){
		    parent::__construct();
			$this->load->helper('vod');
		    $this->load->model('CsdjTpl');
		    $this->load->model('CsdjUser');
	}

    //下载
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
		    if(empty($row['durl'])){
                     msg_url('该视频下载地址不正确！',Web_Path);
		    }

			//判断收费
            if($row['vip']>0 || $row['level']>0 || $row['cion']>0 || User_YkDown==0){
                  $this->CsdjUser->User_Login();
				  $rowu=$this->CsdjDB->get_row_arr('user','vip,level,cion',$_SESSION['cscms__id']);
			}

            //判断会员组下载权限
			if($row['vip']>0 && $row['uid']!=$_SESSION['cscms__id']){
				  if($row['vip']>$rowu['vip']){
                       msg_url('抱歉，您所在的会员组不能下载该视频，请先升级！','javascript:window.close();');
				  }
			}

            //判断会员等级下载权限
			if($row['level']>0 && $row['uid']!=$_SESSION['cscms__id']){
				  if($row['level']>$rowu['level']){
                       msg_url('抱歉，您等级不够，不能下载该视频！','javascript:window.close();');
				  }
			}

            //判断金币下载
			$down=0;
			if($row['dcion']>0 && $row['uid']!=$_SESSION['cscms__id']){

				  //判断是否下载过
				  $did=$id.'-'.$zu.'-'.$ji;
				  $rowd=$this->db->query("SELECT id,addtime FROM ".CS_SqlPrefix."vod_look where did='".$did."' and uid='".$_SESSION['cscms__id']."' and sid=1")->row_array();
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
				      if($row['dcion']>$rowu['cion']){
                           msg_url('这部视频下载每集需要'.$row['cion'].'个金币，您的当前金币不够，请先充值！','javascript:window.close();');
				      }else{
						  //扣币
						  $edit['cion']=$rowu['cion']-$row['dcion'];
						  $this->CsdjDB->get_update('user',$_SESSION['cscms__id'],$edit);
						  //写入消费记录
						  $add2['title']='下载视频《'.$row['name'].'》- 第'.($ji+1).'集';
						  $add2['uid']=$_SESSION['cscms__id'];
						  $add2['dir']='vod';
						  $add2['nums']=$row['cion'];
						  $add2['ip']=getip();
                          $add2['addtime']=time();
					      $this->CsdjDB->get_insert('spend',$add2);

						  //判断分成
						  if(User_DownFun==1 && $row['uid']>0){
							     //分成比例
								 $bi=(User_Downcion<10)?'0.0'.User_Downcion:'0.'.User_Downcion;
                                 $scion= intval($row['dcion'] * $bi);
								 if($scion>0){
						              $this->db->query("update ".CS_SqlPrefix."user set cion=cion+".$scion." where id=".$row['uid']."");
						              //写入分成记录
						              $add3['title']='视频《'.$row['name'].'》- 第'.($ji+1).'集 - 下载分成';
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
			      //增加下载记录
			      if($down==0){
                       $add['name']=$row['name'];
                       $add['cid']=$row['cid'];
                       $add['sid']=1;
                       $add['did']=$did;
                       $add['uid']=$_SESSION['cscms__id'];
                       $add['cion']=$row['dcion'];
                       $add['addtime']=time();
					   $this->CsdjDB->get_insert('vod_look',$add);
			      }
			}
			//增加下载人气
			$this->db->query("update ".CS_SqlPrefix."vod set xhits=xhits+1 where id=".$row['id']."");
			//相关搜索数组
			$arr['cid']=getChild($row['cid']);
			$arr['uid']=$row['uid'];
			$arr['tags']=$row['tags'];
			//装载模板并输出
	        $Mark_Text=$this->CsdjTpl->plub_show('vod',$row,$arr,TRUE,'down.html');
			//评论
			$dance_pl="<div id='cscms_pl'><img src='".Web_Path."packs/images/load.gif'>&nbsp;&nbsp;加载评论内容,请稍等......</div>\r\n<script type='text/javascript'>var dir='vod';var did=".$id.";var cid=0;cscms_pl(1,0,0);</script>";
			$Mark_Text=str_replace("[vod:pl]",$dance_pl,$Mark_Text);
			//分类地址、名称
			$Mark_Text=str_replace("[vod:link]",LinkUrl('show','id',$row['id'],1,'vod'),$Mark_Text);
			$Mark_Text=str_replace("[vod:classlink]",LinkUrl('lists','id',$row['cid'],1,'vod'),$Mark_Text);
			$Mark_Text=str_replace("[vod:classname]",$this->CsdjDB->getzd('vod_list','name',$row['cid']),$Mark_Text);

			//输出下载地址
	        $Data_Arr=explode("#cscms#",$row['durl']);
            if($zu>=count($Data_Arr)) $zu=0;
		    $DataList_Arr=explode("\n",$Data_Arr[$zu]);
		    $Dataurl_Arr=explode('$',$DataList_Arr[$ji]);

		    $laiyuan=$Dataurl_Arr[2]; //来源
		    $url=$Dataurl_Arr[1];  //地址
		    $pname=$Dataurl_Arr[0];  //当前集数

		    $Mark_Text=str_replace("[down:url]",$url,$Mark_Text); //当前集下载地址
		    $Mark_Text=str_replace("[down:laiy]",$laiyuan,$Mark_Text); //当前集来源
		    $Mark_Text=str_replace("[down:ji]",$pname,$Mark_Text);  //当前集数

            echo $Mark_Text;
	}
}


