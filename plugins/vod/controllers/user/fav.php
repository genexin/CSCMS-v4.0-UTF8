<?php if ( ! defined('CSCMS')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-08
 */
class Fav extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
			$this->load->helper('vod');
		    $this->load->model('CsdjTpl');
		    $this->load->model('CsdjUser');
	        $this->CsdjUser->User_Login();
	}

    //收藏视频
	public function index()
	{
		    $cid=intval($this->uri->segment(4)); //分类ID
		    $page=intval($this->uri->segment(5)); //分页
			//模板
			$tpl='fav.html';
			//URL地址
		    $url='fav/index/'.$cid;
            $sqlstr = "select {field} from ".CS_SqlPrefix."vod_fav where uid=".$_SESSION['cscms__id'];
            if($cid>0){
				$cids=getChild($cid);
                $sqlstr.= " and cid in(".$cids.")";
			}
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='我收藏的视频 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,'',$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[vod:cid]",$cid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

	//删除
	public function del()
	{
		    $id=intval($this->uri->segment(4));
		    $callback = $this->input->get('callback',true);
            $row=$this->db->query("select uid from ".CS_SqlPrefix."vod_fav where id=".$id."")->row();
			if($row){
                     if($row->uid!=$_SESSION['cscms__id']){
						  $err=1002;
						  if(empty($callback)) msg_url('没有权限操作','javascript:history.back();');
					 }else{
                          $this->db->query("DELETE FROM ".CS_SqlPrefix."vod_fav where id=".$id."");
						  $err=1001;
						  if(empty($callback)) msg_url('删除成功~!','javascript:history.back();');
					 }
			}else{
				      $err=1002;
					  if(empty($callback)) msg_url('数据不存在','javascript:history.back();');
			}
			echo $callback."({error:".$err."})";
	}
}

