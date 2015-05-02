<?php if ( ! defined('IS_ADMIN')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-03
 */
class Type extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->helper('vod');
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
	}

    //视频剧情
	public function index()
	{
            $sql_string = "SELECT id,name FROM ".CS_SqlPrefix."vod_list where fid=0 order by xid asc";
	        $query = $this->db->query($sql_string); 
	        $data['vod_type'] = $query->result();
            $this->load->view('vod_type.html',$data);
	}

    //显示、隐藏操作
	public function init()
	{
            $ac  = $this->input->get_post('ac',true);
            $id   = intval($this->input->get_post('id'));
            $sid  = intval($this->input->get_post('sid'));
            if($ac=='zt'){ //显示
                  $edit['yid']=$sid;
				  $str=($sid==0)?'<a title="点击隐藏" href="javascript:get_cmd(\''.site_url('vod/admin/lists/init').'?sid=1\',\'zt\','.$id.');"><img align="absmiddle" src="'.Web_Path.'packs/admin/images/icon/ok.gif" /></a>':'<a title="点击显示" href="javascript:get_cmd(\''.site_url('vod/admin/lists/init').'?sid=0\',\'zt\','.$id.');"><img align="absmiddle" src="'.Web_Path.'packs/admin/images/icon/no.gif" /></a>';
			}
            $this->CsdjDB->get_update('vod_type',$id,$edit);
            die($str);
	}

    //批量修改
	public function plsave()
	{
            $ids=$this->input->post('id', TRUE); 
            if(empty($ids)){
                    admin_msg('请选择要操作的数据~!','javascript:history.back();','no');
			}
			foreach ($ids as $id) {
		            $data['name']=$this->input->post('name_'.$id, TRUE);
		            $data['xid']=intval($this->input->post('xid_'.$id, TRUE)); 
                    $this->CsdjDB->get_update('vod_type',$id,$data);
			}
            admin_msg('恭喜您，操作成功~!',site_url('vod/admin/type'),'ok');  //操作成功
	}

    //剧情新增、修改
	public function edit()
	{
            $id   = intval($this->input->get('id'));
            $cid  = intval($this->input->get('cid'));
			if($id==0){
                $data['id']=0;
                $data['cid']=$cid;
                $data['yid']=0;
                $data['xid']=0;
                $data['name']='';
			}else{
                $row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."vod_type where id=".$id."")->row(); 
			    if(!$row) admin_msg('该条记录不存在~!','javascript:history.back();','no');  //记录不存在

                $data['id']=$row->id;
                $data['cid']=$row->cid;
                $data['yid']=$row->yid;
                $data['xid']=$row->xid;
                $data['name']=$row->name;

			}
            $this->load->view('type_edit.html',$data);
	}

    //剧情保存
	public function save()
	{
            $id   = intval($this->input->post('id'));
            $data['yid']=intval($this->input->post('yid'));
            $data['cid']=intval($this->input->post('cid'));
            $data['xid']=intval($this->input->post('xid'));
            $data['name']=$this->input->post('name',true);

			if($id==0){ //新增
                 $this->CsdjDB->get_insert('vod_type',$data);
			}else{
                 $this->CsdjDB->get_update('vod_type',$id,$data);
			}
            admin_msg('恭喜您，操作成功~!',site_url('vod/admin/type'),'ok');  //操作成功
	}

    //剧情删除
	public function del()
	{
            $ids = $this->input->get_post('id');
			if(empty($ids)) admin_msg('请选择要删除的数据~!','javascript:history.back();','no');
			if(is_array($ids)){
			     $idss=implode(',', $ids);
			}else{
			     $idss=$ids;
			}
			$this->CsdjDB->get_del('vod_type',$ids);
            admin_msg('恭喜您，删除成功~!','javascript:history.back();','ok');  //操作成功
	}
}

