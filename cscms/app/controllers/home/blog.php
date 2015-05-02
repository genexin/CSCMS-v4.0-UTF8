<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-17
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Blog extends Cscms_Controller {
	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjTpl');
		    $this->lang->load('home');
	}

	public function index()
	{
            $id = (int)$this->uri->segment(4);   //ID
			//模板
			$tpl='blog.html';
			//当前会员
			$uid=get_home_uid();
			if($uid==0) msg_url(L('home_01'),'http://'.Web_Url.Web_Path);
		    $row=$this->CsdjDB->get_row_arr('user','*',$uid);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=$row['nichen'].L('blog_01');
			$ids['uid']=$row['id'];
			$ids['uida']=$row['id'];
			$rowb=$this->db->query("SELECT * FROM ".CS_SqlPrefix."blog where uid=".$uid." and id=".$id."")->row_array();
			if(!$rowb){
                msg_url(L('blog_02'),'http://'.Web_Url.Web_Path);
			}
            $Mark_Text=$this->CsdjTpl->home_list($row,'blog',1,$tpl,$title,$ids,'','',true,FALSE);
			$Mark_Text=$this->skins->cscms_skins('blog',$Mark_Text,$Mark_Text,$rowb);//解析当前数据标签
			//评论
			$Mark_Text=str_replace("[blog:pl]",get_pl('blog',$id),$Mark_Text);
            $Mark_Text=$this->skins->template_parse($Mark_Text,true,true);
			echo $Mark_Text;
	}
}