<?php if ( ! defined('HOMEPATH')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-18
 */
class Album extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
			$this->load->helper('dance');
		    $this->load->model('CsdjTpl');
	}

	public function index()
	{
            $page = (int)$this->uri->segment(6);   //页数
			//模板
			$tpl='album.html';
			//当前会员
			$uid=get_home_uid();
		    $row=$this->CsdjDB->get_row_arr('user','*',$uid);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=$row['nichen'].'的专辑';
			$ids['uid']=$row['id'];
			$ids['uida']=$row['id'];
            $this->CsdjTpl->home_list($row,'dance/album',$page,$tpl,$title,$ids,'index');
	}
}

