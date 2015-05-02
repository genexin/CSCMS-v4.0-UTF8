<?php if ( ! defined('IS_ADMIN')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-16
 */
class Server extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->helper('dance');
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
	}

    //歌曲服务器组
	public function index()
	{
            $sql_string = "SELECT * FROM ".CS_SqlPrefix."dance_server order by id asc";
	        $query = $this->db->query($sql_string); 
	        $data['dance_server'] = $query->result();
            $this->load->view('dance_server.html',$data);
	}

    //批量修改
	public function plsave()
	{
            $ids=$this->input->post('id', TRUE); 
            if(empty($ids)){
                    admin_msg(L('plub_69'),'javascript:history.back();','no');
			}
			foreach ($ids as $id) {
		            $data['name']=$this->input->post('name_'.$id, TRUE);
		            $data['purl']=$this->input->post('purl_'.$id, TRUE);
		            $data['durl']=$this->input->post('durl_'.$id, TRUE);
                    $this->CsdjDB->get_update('dance_server',$id,$data);
			}
            admin_msg(L('plub_70'),site_url('dance/admin/server'),'ok');  //操作成功
	}

    //批量转移分类
	public function zhuan()
	{
            $ids = $this->input->get_post('id', TRUE);
            $cid = intval($this->input->get_post('cid')); 
            if(empty($ids)){
                    admin_msg(L('plub_69'),'javascript:history.back();','no');
			}
            if($cid==0){
                    admin_msg(L('plub_71'),'javascript:history.back();','no');
			}
			$ids=implode(',', $ids);
            $this->db->query("update ".CS_SqlPrefix."dance set fid=".$fid." where fid in (".$ids.")");
            admin_msg(L('plub_70'),site_url('dance/admin/server'),'ok');  //操作成功
	}

    //新增保存
	public function save()
	{

            $data['name']=$this->input->get('name',true);
            $data['purl']=$this->input->get('purl',true);
            $data['durl']=$this->input->get('durl',true);
			$this->CsdjDB->get_insert('dance_server',$data);
            admin_msg(L('plub_70'),site_url('dance/admin/server'),'ok');  //操作成功
	}

    //删除
	public function del()
	{
            $ids = $this->input->get_post('id');
			if(empty($ids)) admin_msg(L('plub_73'),'javascript:history.back();','no');
			if(is_array($ids)){
			     $idss=implode(',', $ids);
			}else{
			     $idss=$ids;
			}
			$this->CsdjDB->get_del('dance_server',$ids);
            admin_msg(L('plub_74'),'javascript:history.back();','ok');  //操作成功
	}
}

