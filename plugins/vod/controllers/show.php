<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-02
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Show extends Cscms_Controller {
	function __construct(){
		    parent::__construct();
			$this->load->helper('vod');
		    $this->load->model('CsdjTpl');
	}

    //视频内容
	public function index($fid = 'id', $id = 0, $return = FALSE)
	{
            $id = (intval($fid)>0)?intval($fid):intval($id);   //ID
            //判断ID
            if($id==0) msg_url('出错了，ID不能为空！',Web_Path);
            //获取数据
		    $row=$this->CsdjDB->get_row_arr('vod','*',$id);
		    if(!$row || $row['yid']>0 || $row['hid']>0){
                     msg_url('出错了，该数据不存在或者没有审核！',Web_Path);
		    }
            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['show']['check']==1 && !defined('MOBILE')){
                //获取静态路径
				$Htmllink=LinkUrl('show',$fid,$id,0,'vod');
				header("Location: ".$Htmllink);
				exit;
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
			$skins=getzd('vod_list','skins2',$row['cid']);
			if(empty($skins)) $skins='show.html';
			//装载模板并输出
	        $Mark_Text=$this->CsdjTpl->plub_show('vod',$row,$arr,TRUE,$skins,$row['name'],$row['name']);
			//评论
			$Mark_Text=str_replace("[vod:pl]",get_pl('vod',$id),$Mark_Text);
			//分类地址、名称
			$Mark_Text=str_replace("[vod:link]",LinkUrl('show','id',$row['id'],1,'vod'),$Mark_Text);
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
			//解析播放下载地址
            $Mark_Text=Vod_Playlist($Mark_Text,'play',$id,$row['purl']);
            $Mark_Text=Vod_Playlist($Mark_Text,'down',$id,$row['durl']);
            echo $Mark_Text;
			$this->cache->end(); //由于前面不是直接输出，所以这里需要加入写缓存
	}
}

