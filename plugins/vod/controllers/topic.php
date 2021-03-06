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
			$this->load->helper('vod');
		    $this->load->model('CsdjTpl'); //装载视图模型
	}

    //专题首页
	public function index($sort='id',$page = 0)
	{
            $page  = intval($page);   //页数
            if($page==0) $page=1;

            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['topic/lists']['check']==1 && !defined('MOBILE')){
                //获取静态路径
				$Htmllink=LinkUrl('topic/lists','id',0,$page,'vod');
				header("Location: ".$Htmllink);
				exit;
			}
			$row=array();
			//装载模板并输出
	        $this->CsdjTpl->plub_list($row,0,$sort,$page,'',FALSE,'topic.html','topic/lists','','视频专题');
	}

    //专题列表
	public function lists($sort='id',$page = 0)
	{
            $page  = intval($page);   //页数
            if($page==0) $page=1;

            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['topic/lists']['check']==1 && !defined('MOBILE')){
                //获取静态路径
				$Htmllink=LinkUrl('topic/lists','id',0,$page,'vod');
				header("Location: ".$Htmllink);
				exit;
			}
			$row=array();
			//装载模板并输出
	        $this->CsdjTpl->plub_list($row,0,$sort,$page,'',FALSE,'topic.html','topic/lists','','视频专题');
	}

    //专题内容
	public function show($id=0)
	{
            $id    = intval($id);   //ID
            //判断ID
            if($id==0) msg_url('出错了，ID不能为空！',Web_Path);
            //获取数据
		    $row=$this->CsdjDB->get_row_arr('vod_topic','*',$id);
		    if(!$row || $row['yid']>0){
                     msg_url('出错了，该专题不存在！',Web_Path);
		    }
            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['topic/show']['check']==1 && !defined('MOBILE')){
                //获取静态路径
				$Htmllink=LinkUrl('topic','show',$id,1,'vod');
				header("Location: ".$Htmllink);
				exit;
			}
			//装载模板并输出
			$ids['tid']=$id;
	        $Mark_Text=$this->CsdjTpl->plub_show('topic',$row,$ids,true,'topic-show.html',$row['name'],$row['name']);
			//评论
			$Mark_Text=str_replace("[topic:pl]",get_pl('vod',$id,1),$Mark_Text);
			//增加人气
			$Mark_Text=hits_js($Mark_Text,hitslink('hits/ids/'.$id.'/topic','vod'));
            echo $Mark_Text;
			$this->cache->end(); //由于前面不是直接输出，所以这里需要加入写缓存
	}
}


