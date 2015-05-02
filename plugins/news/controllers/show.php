<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-16
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Show extends Cscms_Controller {
	function __construct(){
		    parent::__construct();
			$this->load->helper('news');
		    $this->load->model('CsdjTpl');
	}

    //内容
	public function index($fid = 'id', $id = 0)
	{
            $id = (intval($fid)>0)?intval($fid):intval($id);   //ID
            //判断ID
            if($id==0) msg_url('出错了，ID不能为空！',Web_Path);
            //获取数据
		    $row=$this->CsdjDB->get_row_arr('news','*',$id);
		    if(!$row || $row['yid']>0 || $row['hid']>0){
                     msg_url('出错了，该数据不存在或者没有审核！',Web_Path);
		    }
            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['show']['check']==1){
                //获取静态路径
				$Htmllink=LinkUrl('show',$fid,$id,0,'news');
				header("Location: ".$Htmllink);
				exit;
			}
			//摧毁部分需要超级链接字段数组
			$rows=$row; //先保存数组保留下面使用
			unset($row['tags']);
			unset($row['content']);
			//获取当前分类下二级分类ID
			$arr['cid']=getChild($row['cid']);
			$arr['uid']=$row['uid'];
			$arr['tags']=$rows['tags'];
			//默认模板
			$skins=empty($row['skins'])?'show.html':$row['skins'];
			//装载模板并输出
	        $Mark_Text=$this->CsdjTpl->plub_show('news',$row,$arr,TRUE,$skins,$row['name'],$row['name']);
			//评论
			$Mark_Text=str_replace("[news:pl]",get_pl('news',$id),$Mark_Text);
			//分类地址、名称
			$Mark_Text=str_replace("[news:link]",LinkUrl('show','id',$row['id'],1,'news'),$Mark_Text);
			$Mark_Text=str_replace("[news:classlink]",LinkUrl('lists','id',$row['cid'],1,'news'),$Mark_Text);
			$Mark_Text=str_replace("[news:classname]",$this->CsdjDB->getzd('news_list','name',$row['cid']),$Mark_Text);
			//获取上下篇
            preg_match_all('/[news:slink]/',$Mark_Text,$arr);
		    if(!empty($arr[0]) && !empty($arr[0][0])){
                   $rowd=$this->db->query("Select id,cid,name from ".CS_SqlPrefix."news where yid=0 and hid=0 and id<".$id." order by id desc limit 1")->row();
				   if($rowd){
			             $Mark_Text=str_replace("[news:slink]",LinkUrl('show','id',$rowd->id,1,'news'),$Mark_Text);
			             $Mark_Text=str_replace("[news:sname]",$rowd->name,$Mark_Text);
			             $Mark_Text=str_replace("[news:sid]",$rowd->id,$Mark_Text);
				   }else{
			             $Mark_Text=str_replace("[news:slink]","#",$Mark_Text);
			             $Mark_Text=str_replace("[news:sname]","没有了",$Mark_Text);
			             $Mark_Text=str_replace("[news:sid]",0,$Mark_Text);
				   }
			}
			unset($arr);
            preg_match_all('/[news:xlink]/',$Mark_Text,$arr);
		    if(!empty($arr[0]) && !empty($arr[0][0])){
                   $rowd=$this->db->query("Select id,cid,name from ".CS_SqlPrefix."news where yid=0 and hid=0 and id>".$id." order by id asc limit 1")->row();
				   if($rowd){
			             $Mark_Text=str_replace("[news:xlink]",LinkUrl('show','id',$rowd->id,1,'news'),$Mark_Text);
			             $Mark_Text=str_replace("[news:xname]",$rowd->name,$Mark_Text);
			             $Mark_Text=str_replace("[news:xid]",$rowd->id,$Mark_Text);
				   }else{
			             $Mark_Text=str_replace("[news:xlink]","#",$Mark_Text);
			             $Mark_Text=str_replace("[news:xname]","没有了",$Mark_Text);
			             $Mark_Text=str_replace("[news:xid]",0,$Mark_Text);
				   }
			}
			unset($arr);
			//标签加超级连接
			$Mark_Text=str_replace("[news:tags]",SearchLink($rows['tags']),$Mark_Text);
            //文章内容,判断是否是收费文章
			if($row['vip']>0 || $row['level']>0 || $row['cion']>0){
				  $content="<div id='cscms_content'></div>";
				  if(config('Ym_Mode','news')==1){
                        $content.="<script type='text/javascript' src='http://".config('Ym_Url','news').Web_Path."index.php/show/pay/".$id."'></script>";
				  }else{
                        $content.="<script type='text/javascript' src='http://".Web_Url.Web_Path."index.php/news/show/pay/".$id."'></script>";
				  }
			}else{
                  $content=$rows['content'];
			}
			$Mark_Text=str_replace("[news:content]",$content,$Mark_Text);
			//增加人气
			$Mark_Text=hits_js($Mark_Text,hitslink('hits/ids/'.$id,'news'));
            echo $Mark_Text;
			$this->cache->end(); //由于前面不是直接输出，所以这里需要加入写缓存
	}

    //判断权限、积分
	public function pay($id=0)
	{
            @header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
            @header("Cache-Control: no-cache, must-revalidate"); 
            @header("Pragma: no-cache");
		    $this->load->model('CsdjUser');
            //判断ID
            if($id==0) exit();
            //获取数据
		    $row=$this->CsdjDB->get_row_arr('news','name,uid,cid,yid,hid,id,vip,level,cion,content',$id);
		    if(!$row || $row['yid']>0 || $row['hid']>0){
                     exit("alert('数据没有审核，或者被删除~!');");
		    }

			//判断收费
            if($row['vip']>0 || $row['level']>0 || $row['cion']>0){
                  $login=$this->CsdjUser->User_Login(1);
                  if(!$login) exit("alert('抱歉，该文章需要登录才能阅读，请先登录！');");
				  $rowu=$this->CsdjDB->get_row_arr('user','vip,level,cion',$_SESSION['cscms__id']);
			}
            //判断会员组权限
			if($row['vip']>0 && $row['uid']!=$_SESSION['cscms__id']){
				  if($row['vip']>$rowu['vip']){
					   exit("alert('抱歉，您所在的会员组不能阅读该文章，请先升级！');");
				  }
			}

            //判断会员等级权限
			if($row['level']>0 && $row['uid']!=$_SESSION['cscms__id']){
				  if($row['level']>$rowu['level']){
					   exit("alert('抱歉，您等级不够，不能阅读该文章！');");
				  }
			}

            //判断金币
			$down=0;
			if($row['cion']>0 && $row['uid']!=$_SESSION['cscms__id']){

				  //判断是否下载过
				  $did=$id;
				  $rowd=$this->db->query("SELECT id,addtime FROM ".CS_SqlPrefix."news_look where did='".$did."' and uid='".$_SESSION['cscms__id']."'")->row_array();
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
						   exit("alert('这编文章阅读需要".$row['cion']."个金币，您的当前金币不够，请先充值！');");
				      }else{
						  //扣币
						  $edit['cion']=$rowu['cion']-$row['cion'];
						  $this->CsdjDB->get_update('user',$_SESSION['cscms__id'],$edit);
						  //写入消费记录
						  $add2['title']='阅读文章《'.$row['name'].'》';
						  $add2['uid']=$_SESSION['cscms__id'];
						  $add2['nums']=$row['cion'];
						  $add2['ip']=getip();
						  $add2['dir']='news';
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
						              $add3['title']='文章《'.$row['name'].'》 - 阅读分成';
						              $add3['uid']=$row['uid'];
						              $add3['dir']='news';
						              $add3['nums']=$scion;
						              $add3['ip']=getip();
                                      $add3['addtime']=time();
					                  $this->CsdjDB->get_insert('income',$add3);
								 }
						  }
					  }
				  }
			      //增加阅读记录
			      if($down==0){
                       $add['name']=$row['name'];
                       $add['cid']=$row['cid'];
                       $add['did']=$did;
                       $add['uid']=$_SESSION['cscms__id'];
                       $add['cion']=$row['cion'];
                       $add['addtime']=time();
					   $this->CsdjDB->get_insert('news_look',$add);
			      }
			}
			echo "var cscms_content='".escape($row['content'])."';$('#cscms_content').html(unescape(cscms_content));";
	}
}


