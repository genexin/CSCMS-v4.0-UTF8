<?php if ( ! defined('CSCMS')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-08
 */
class Down extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
			$this->load->helper('dance');
		    $this->load->model('CsdjTpl');
		    $this->load->model('CsdjUser');
	        $this->CsdjUser->User_Login();
	}

    //歌曲
	public function index()
	{
		    $cid=intval($this->uri->segment(4));
		    $page=intval($this->uri->segment(5));
			//模板
			$tpl='down.html';
			//URL地址
		    $url='down/index';
            $sqlstr = "select {field} from ".CS_SqlPrefix."dance_down where uid=".$_SESSION['cscms__id'];
            if($cid>0){
                $sqlstr.= " and cid=".$cid."";
			}
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='我下载的歌曲 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,'',$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[dance:cid]",$cid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}
}

