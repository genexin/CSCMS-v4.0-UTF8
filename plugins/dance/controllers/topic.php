<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-03
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Topic extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
			$this->load->helper('dance');
		    $this->load->model('CsdjTpl'); //装载视图模型
	}

    //专题首页
	public function index($fid='id',$page = 0)
	{
            $page  = intval($page);   //页数
            if($page==0) $page=1;

            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['topic/lists']['check']==1 && !defined('MOBILE')){
                //获取静态路径
				$Htmllink=LinkUrl('topic/lists',$fid,0,$page,'dance');
				header("Location: ".$Htmllink);
				exit;
			}
			$row=array();
			//装载模板并输出
	        $Mark_Text=$this->CsdjTpl->plub_list($row,0,$fid,$page,'',true,'topic.html','topic','','歌曲专辑','歌曲专辑');
			$Mark_Text=str_replace("[topic:link]",LinkUrl('topic','id',0,$page,'dance'),$Mark_Text);
			$Mark_Text=str_replace("[topic:hlink]",LinkUrl('topic','hits',0,$page,'dance'),$Mark_Text);
			$Mark_Text=str_replace("[topic:rlink]",LinkUrl('topic','ri',0,$page,'dance'),$Mark_Text);
			$Mark_Text=str_replace("[topic:zlink]",LinkUrl('topic','zhou',0,$page,'dance'),$Mark_Text);
			$Mark_Text=str_replace("[topic:ylink]",LinkUrl('topic','yue',0,$page,'dance'),$Mark_Text);
			$Mark_Text=str_replace("[topic:slink]",LinkUrl('topic','fav',0,$page,'dance'),$Mark_Text);
            echo $Mark_Text;
			$this->cache->end(); //由于前面不是直接输出，所以这里需要加入写缓存
	}

    //专题列表
	public function lists($fid='id', $cid=0, $page=1)
	{
            $page  = intval($page);   //页数
            if($page==0) $page=1;
            //判断ID
            if($cid==0) msg_url(L('dance_09'),Web_Path);
            //获取数据
		    $row=$this->CsdjDB->get_row_arr('dance_list','*',$cid);
		    if(!$row){
                     msg_url(L('dance_18'),Web_Path);
		    }

            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['topic/lists']['check']==1){
                //获取静态路径
				$Htmllink=LinkUrl('topic/lists',$fid,$cid,$page,'dance');
				header("Location: ".$Htmllink);
				exit;
			}

			//装载模板并输出
	        $Mark_Text=$this->CsdjTpl->plub_list($row,$cid,$fid,$page,$cid,true,'topic.html','topic/lists','',L('dance_22'),L('dance_22'));
			$Mark_Text=str_replace("[topic:cids]",$cid,$Mark_Text);
			$Mark_Text=str_replace("[topic:link]",LinkUrl('topic/lists','id',$cid,$page,'dance'),$Mark_Text);
			$Mark_Text=str_replace("[topic:hlink]",LinkUrl('topic/lists','hits',$cid,$page,'dance'),$Mark_Text);
			$Mark_Text=str_replace("[topic:rlink]",LinkUrl('topic/lists','ri',$cid,$page,'dance'),$Mark_Text);
			$Mark_Text=str_replace("[topic:zlink]",LinkUrl('topic/lists','zhou',$cid,$page,'dance'),$Mark_Text);
			$Mark_Text=str_replace("[topic:ylink]",LinkUrl('topic/lists','yue',$cid,$page,'dance'),$Mark_Text);
			$Mark_Text=str_replace("[topic:slink]",LinkUrl('topic/lists','fav',$cid,$page,'dance'),$Mark_Text);
            echo $Mark_Text;
			$this->cache->end(); //由于前面不是直接输出，所以这里需要加入写缓存
	}

    //专题内容
	public function show($fid='id',$id=0)
	{
            $id    = (intval($fid)>0)?intval($fid):intval($id);   //ID
            //判断ID
            if($id==0) msg_url(L('dance_09'),Web_Path);
            //获取数据
		    $row=$this->CsdjDB->get_row_arr('dance_topic','*',$id);
		    if(!$row || $row['yid']>0){
                     msg_url(L('dance_23'),Web_Path);
		    }
            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['topic/show']['check']==1){
                //获取静态路径
				$Htmllink=LinkUrl('topic/show',$id,1,'dance');
				header("Location: ".$Htmllink);
				exit;
			}
			//装载模板并输出
			$ids['tid']=$id;
			$ids['singerid']=$row['singerid'];
			$ids['uid']=$row['uid'];
	        $Mark_Text=$this->CsdjTpl->plub_show('topic',$row,$ids,true,'topic-show.html',$row['name'].' - '.L('dance_22'),$row['name']);
			//评论
			$Mark_Text=str_replace("[topic:pl]",get_pl('dance',$id,1),$Mark_Text);
			$Mark_Text=hits_js($Mark_Text,hitslink('hits/ids/'.$id.'/topic','dance'));
            echo $Mark_Text;
			$this->cache->end(); //由于前面不是直接输出，所以这里需要加入写缓存
	}
}


