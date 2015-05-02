<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-16
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Music extends Cscms_Controller {
	function __construct(){
		    parent::__construct();
			$this->load->helper('singer');
		    $this->load->model('CsdjTpl');
	}

    //内容
	public function index($fid='id', $id = 0, $page=1)
	{
            $id = intval($id);   //ID
            $page = intval($page);   //ID
			if(preg_match("/^\d*$/",$fid)){
                $id = intval($fid);
                $page = intval($id);
				$fid = 'id';
			}
			if($page==0) $page=1;
            //判断ID
            if($id==0) msg_url('出错了，ID不能为空！',Web_Path);
            //获取数据
		    $row=$this->CsdjDB->get_row_arr('singer','*',$id);
		    if(!$row || $row['yid']>0 || $row['hid']>0){
                     msg_url('出错了，该歌手不存在！',Web_Path);
		    }
            //判断运行模式,生成则跳转至静态页面
			$html=config('Html_Uri');
	        if(config('Web_Mode')==3 && $html['show']['check']==1){
                //获取静态路径
				$Htmllink=LinkUrl('music','',$id,0,'singer');
				header("Location: ".$Htmllink);
				exit;
			}
			$arr['cid']=getChild($row['cid']);
			$arr['tags']=$row['tags'];
			$arr['singerid']=$id;
			//摧毁部分需要超级链接字段数组
			$rows=$row; //先保存数组保留下面使用
			unset($row['tags']);
			//装载模板并输出
			$Mark_Text=$this->CsdjTpl->plub_list($row, $id, '', $page, $arr, TRUE, 'music.html', 'music', 'singer', $row['name'],$row['name']);
			//评论
			$Mark_Text=str_replace("[singer:pl]",get_pl('singer',$id),$Mark_Text);
			//分类地址、名称
			$Mark_Text=str_replace("[singer:link]",LinkUrl('show','id',$row['id'],1,'singer'),$Mark_Text);
			$Mark_Text=str_replace("[singer:classlink]",LinkUrl('lists','id',$row['cid'],1,'singer'),$Mark_Text);
			$Mark_Text=str_replace("[singer:classname]",$this->CsdjDB->getzd('singer_list','name',$row['cid']),$Mark_Text);
			//标签加超级连接
			$Mark_Text=str_replace("[singer:tags]",SearchLink($rows['tags']),$Mark_Text);
            echo $Mark_Text;
			$this->cache->end(); //由于前面不是直接输出，所以这里需要加入写缓存
	}
}


