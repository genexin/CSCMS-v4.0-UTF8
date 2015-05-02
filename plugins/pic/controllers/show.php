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
			$this->load->helper('pic');
		    $this->load->model('CsdjTpl');
	}

    //内容
	public function index($fid = 'id', $id = 0)
	{
            $id = (intval($fid)>0)?intval($fid):intval($id);   //ID
            //判断ID
            if($id==0) msg_url('出错了，ID不能为空！',Web_Path);
            //获取数据
		    $row=$this->CsdjDB->get_row_arr('pic_type','*',$id);
		    if(!$row || $row['yid']>0 || $row['hid']>0){
                     msg_url('出错了，该数据不存在或者没有审核！',Web_Path);
		    }
            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['show']['check']==1){
                //获取静态路径
				$Htmllink=LinkUrl('show',$fid,$id,0,'pic');
				header("Location: ".$Htmllink);
				exit;
			}
			//摧毁部分需要超级链接字段数组
			$rows=$row; //先保存数组保留下面使用
			unset($row['tags']);
			//获取当前分类下二级分类ID
			$arr['cid']=getChild($row['cid']);
			$arr['uid']=$row['uid'];
			$arr['tags']=$rows['tags'];
			$arr['sid']=$row['id'];
			//默认模板
			$skins=empty($row['skins'])?'show.html':$row['skins'];
			//装载模板并输出
	        $Mark_Text=$this->CsdjTpl->plub_show('pic',$row,$arr,TRUE,$skins,$row['name'],$row['name']);
			//评论
			$Mark_Text=str_replace("[pic:pl]",get_pl('pic',$id),$Mark_Text);
			//分类地址、名称
			$Mark_Text=str_replace("[pic:link]",LinkUrl('show','id',$row['id'],1,'pic'),$Mark_Text);
			$Mark_Text=str_replace("[pic:classlink]",LinkUrl('lists','id',$row['cid'],1,'pic'),$Mark_Text);
			$Mark_Text=str_replace("[pic:classname]",$this->CsdjDB->getzd('pic_list','name',$row['cid']),$Mark_Text);
			//获取上下张
            preg_match_all('/[pic:slink]/',$Mark_Text,$arr);
		    if(!empty($arr[0]) && !empty($arr[0][0])){
                   $rowd=$this->db->query("Select id,cid,pic,name from ".CS_SqlPrefix."pic_type where yid=0 and hid=0 and id<".$id." order by id desc limit 1")->row();
				   if($rowd){
			             $Mark_Text=str_replace("[pic:slink]",LinkUrl('show','id',$rowd->id,1,'pic'),$Mark_Text);
			             $Mark_Text=str_replace("[pic:sname]",$rowd->name,$Mark_Text);
			             $Mark_Text=str_replace("[pic:sid]",$rowd->id,$Mark_Text);
			             $Mark_Text=str_replace("[pic:spic]",piclink('pic',$rowd->pic),$Mark_Text);
				   }else{
			             $Mark_Text=str_replace("[pic:slink]","#",$Mark_Text);
			             $Mark_Text=str_replace("[pic:sname]","没有了",$Mark_Text);
			             $Mark_Text=str_replace("[pic:sid]",0,$Mark_Text);
			             $Mark_Text=str_replace("[pic:spic]",piclink('pic',''),$Mark_Text);
				   }
			}
			unset($arr);
            preg_match_all('/[pic:xlink]/',$Mark_Text,$arr);
		    if(!empty($arr[0]) && !empty($arr[0][0])){
                   $rowd=$this->db->query("Select id,cid,pic,name from ".CS_SqlPrefix."pic_type where yid=0 and hid=0 and id>".$id." order by id asc limit 1")->row();
				   if($rowd){
			             $Mark_Text=str_replace("[pic:xlink]",LinkUrl('show','id',$rowd->id,1,'pic'),$Mark_Text);
			             $Mark_Text=str_replace("[pic:xname]",$rowd->name,$Mark_Text);
			             $Mark_Text=str_replace("[pic:xid]",$rowd->id,$Mark_Text);
			             $Mark_Text=str_replace("[pic:xpic]",piclink('pic',$rowd->pic),$Mark_Text);
				   }else{
			             $Mark_Text=str_replace("[pic:xlink]","#",$Mark_Text);
			             $Mark_Text=str_replace("[pic:xname]","没有了",$Mark_Text);
			             $Mark_Text=str_replace("[pic:xid]",0,$Mark_Text);
			             $Mark_Text=str_replace("[pic:xpic]",piclink('pic',''),$Mark_Text);
				   }
			}
			unset($arr);
			//标签加超级连接
			$Mark_Text=str_replace("[pic:tags]",SearchLink($rows['tags']),$Mark_Text);
			//获取当前相册总数
			$pcount=$this->db->query("Select id from ".CS_SqlPrefix."pic where sid=".$id." and hid=0 and yid=0")->num_rows();
			$Mark_Text=str_replace("[pic:count]",$pcount,$Mark_Text);
			//第一张图片
			$rowp=$this->db->query("Select pic,content from ".CS_SqlPrefix."pic where sid=".$id." and hid=0 and yid=0 order by id desc limit 1")->row();
            $pics=($rowp)?$rowp->pic:'';
            $content=($rowp)?$rowp->content:'';
			$Mark_Text=str_replace("[pic:url]",piclink('pic',$pics),$Mark_Text);
			$Mark_Text=str_replace("[pic:content]",$content,$Mark_Text);
			//增加人气
			$Mark_Text=hits_js($Mark_Text,hitslink('hits/ids/'.$id,'pic'));
            echo $Mark_Text;
			$this->cache->end(); //由于前面不是直接输出，所以这里需要加入写缓存
	}
}


