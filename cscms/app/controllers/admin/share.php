<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2008-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-11-20
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Share extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjAdmin');
			$this->lang->load('admin_share');
	        $this->CsdjAdmin->Admin_Login();
	}

    //分享记录列表
	public function index()
	{
            $zd   = $this->input->get_post('zd',true);
            $key  = $this->input->get_post('key',true,true);
 	        $page = intval($this->input->get('page'));
            if($page==0) $page=1;

	        $this->load->library('ip');

	        $data['page'] = $page;
	        $data['zd'] = $zd;
	        $data['key'] = $key;

            $sql_string = "SELECT * FROM ".CS_SqlPrefix."share where 1=1";
			if(!empty($key)){
				 if($zd=='name'){
                     $uid=$this->CsdjDB->getzd('user','id',$key,'name');
				 }else{
                     $uid=$key;
				 }
				 $sql_string.= " and uid=".intval($uid)."";
			}

	        $sql_string.= " order by addtime desc";
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('share')."?zd=".$zd."&key=".$key;
	        $per_page = 15; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['share'] = $query->result();
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类

            $this->load->view('share.html',$data);
	}

    //删除
	public function del()
	{
            $id = $this->input->get_post('id',true,true);
			if(empty($id)) admin_msg(L('plub_01'),'javascript:history.back();','no');  //数据不完成
			$this->CsdjDB->get_del('blog',$id);
            admin_msg(L('plub_02'),site_url('share'),'ok');  //操作成功
	}
}

