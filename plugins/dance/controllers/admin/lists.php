<?php if ( ! defined('IS_ADMIN')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-16
 */
class Lists extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->helper('dance');
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
	}

    //歌曲分类
	public function index()
	{
            $sql_string = "SELECT * FROM ".CS_SqlPrefix."dance_list where fid=0 order by xid asc";
	        $query = $this->db->query($sql_string); 
	        $data['dance_list'] = $query->result();
            $this->load->view('dance_list.html',$data);
	}

    //显示、隐藏操作
	public function init()
	{
            $ac  = $this->input->get_post('ac',true);
            $id   = intval($this->input->get_post('id'));
            $sid  = intval($this->input->get_post('sid'));
            if($ac=='zt'){ //显示
                  $edit['yid']=$sid;
				  $str=($sid==0)?'<a title="'.L('plub_67').'" href="javascript:get_cmd(\''.site_url('dance/admin/lists/init').'?sid=1\',\'zt\','.$id.');"><img align="absmiddle" src="'.Web_Path.'packs/admin/images/icon/ok.gif" /></a>':'<a title="'.L('plub_68').'" href="javascript:get_cmd(\''.site_url('dance/admin/lists/init').'?sid=0\',\'zt\','.$id.');"><img align="absmiddle" src="'.Web_Path.'packs/admin/images/icon/no.gif" /></a>';
			}
            $this->CsdjDB->get_update('dance_list',$id,$edit);
            die($str);
	}

    //批量修改分类
	public function plsave()
	{
            $ids=$this->input->post('id', TRUE); 
            if(empty($ids)){
                    admin_msg(L('plub_69'),'javascript:history.back();','no');
			}
			foreach ($ids as $id) {
		            $data['name']=$this->input->post('name_'.$id, TRUE);
		            $data['bname']=$this->input->post('bname_'.$id, TRUE);
		            $data['skins']=$this->input->post('skins_'.$id, TRUE);
		            $data['xid']=intval($this->input->post('xid_'.$id, TRUE)); 
                    $this->CsdjDB->get_update('dance_list',$id,$data);
			}
            admin_msg(L('plub_70'),site_url('dance/admin/lists'),'ok');  //操作成功
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
            $this->db->query("update ".CS_SqlPrefix."dance set cid=".$cid." where cid in (".$ids.")");
            admin_msg(L('plub_70'),site_url('dance/admin/lists'),'ok');  //操作成功
	}

    //分类新增、修改
	public function edit()
	{
            $id   = intval($this->input->get('id'));
            $fid  = intval($this->input->get('fid'));
			if($id==0){
                $data['id']=0;
                $data['fid']=$fid;
                $data['yid']=0;
                $data['xid']=0;
                $data['name']='';
                $data['bname']='';
                $data['skins']='list.html';
                $data['title']='';
                $data['keywords']='';
                $data['description']='';
			}else{
                $row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."dance_list where id=".$id."")->row(); 
			    if(!$row) admin_msg(L('plub_72'),'javascript:history.back();','no');  //记录不存在

                $data['id']=$row->id;
                $data['fid']=$row->fid;
                $data['yid']=$row->yid;
                $data['xid']=$row->xid;
                $data['name']=$row->name;
                $data['bname']=$row->bname;
                $data['skins']=$row->skins;
                $data['title']=$row->title;
                $data['keywords']=$row->keywords;
                $data['description']=$row->description;
			}
            $this->load->view('list_edit.html',$data);
	}

    //分类保存
	public function save()
	{
            $id   = intval($this->input->post('id'));
            $data['yid']=intval($this->input->post('yid'));
            $data['fid']=intval($this->input->post('fid'));
            $data['xid']=intval($this->input->post('xid'));
            $data['name']=$this->input->post('name',true);
            $data['bname']=$this->input->post('bname',true);
            $data['skins']=$this->input->post('skins',true);
            $data['title']=$this->input->post('title',true);
            $data['keywords']=$this->input->post('keywords',true);
            $data['description']=$this->input->post('description',true);

			if($id==0){ //新增
                 $this->CsdjDB->get_insert('dance_list',$data);
			}else{
                 $this->CsdjDB->get_update('dance_list',$id,$data);
			}
            admin_msg(L('plub_70'),site_url('dance/admin/lists'),'ok');  //操作成功
	}

    //分类删除
	public function del()
	{
            $ids = $this->input->get_post('id');
			if(empty($ids)) admin_msg(L('plub_73'),'javascript:history.back();','no');
			if(is_array($ids)){
			     $idss=implode(',', $ids);
			}else{
			     $idss=$ids;
			}
			$this->CsdjDB->get_del('dance_list',$ids,'fid');
			$this->CsdjDB->get_del('dance_list',$ids);
            admin_msg(L('plub_74'),'javascript:history.back();','ok');  //操作成功
	}
}

