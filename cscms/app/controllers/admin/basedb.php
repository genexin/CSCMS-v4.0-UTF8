<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2008-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-10-31
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Basedb extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjAdmin');
			$this->load->model('CsdjBackup');
			$this->lang->load('admin_basedb');
	        $this->CsdjAdmin->Admin_Login();
	}

	public function index()
	{
			$data['tables']=$this->db->query("SHOW TABLE STATUS FROM `".CS_Sqlname."`")->result();
            $this->load->view('basedb.html',$data);
	}

    //还原数据库
	public function restore()
	{
			$this->load->helper('directory');
            $data['map'] = directory_map(FCPATH.'attachment/backup/', 1);
            $this->load->view('basedb_hy.html',$data);
	}

    //优化表
	public function optimize()
	{
			$error=array();
		    $this->load->dbutil();
		    $tables = $this->input->get('table',true);
			if(empty($tables)){
		        $tables = $this->input->post('id',true);
			    if(empty($tables)) admin_msg(L('plub_01'),'javascript:history.back();','no');
			    foreach($tables as $table) {
				    if(!$this->dbutil->optimize_table($table)){
                         $error[]=$table;
				    }
			    }
			}else{
				if(!$this->dbutil->optimize_table($tables)){
                     $error[]=$tables;
				}
			}
			if(!empty($error)){
                admin_msg(vsprintf(L('plub_02'),array(implode(',', $error))),site_url('basedb'),'no');
			}else{
                admin_msg(L('plub_03'),site_url('basedb'));
			}
	}

    //修复表
	public function repair()
	{
			$error=array();
		    $this->load->dbutil();
		    $tables = $this->input->get('table',true);
			if(empty($tables)){
		        $tables = $this->input->post('id',true);
			    if(empty($tables)) admin_msg(L('plub_01'),'javascript:history.back();','no');
			    foreach($tables as $table) {
				    if(!$this->dbutil->repair_table($table)){
                         $error[]=$table;
				    }
			    }
			}else{
				if(!$this->dbutil->repair_table($tables)){
                     $error[]=$tables;
				}
			}
			if(!empty($error)){
                admin_msg(vsprintf(L('plub_04'),array(implode(',', $error))),'javascript:history.back();','no');
			}else{
                admin_msg(L('plub_05'),site_url('basedb'));
			}
	}

    //备份数据库
	public function backup()
	{
 	        $size = $this->input->post('size',true);
			$tables = $this->input->post('id',true);
			if(empty($tables)) admin_msg(L('plub_06'),'javascript:history.back();','no');
            $res=$this->CsdjBackup->backup($tables);
			if($res){
                 $this->CsdjBackup->bkdata($size); 
                 admin_msg(L('plub_07'),site_url('basedb/restore'));
			}else{
                 admin_msg(L('plub_08'),'javascript:history.back();','no');
			}
	}

	//还原数据库
	public function restore_save()
	{
			$dirs = $this->input->get('dir',true);
			if(empty($dirs)) admin_msg(L('plub_09'),'javascript:history.back();','no');
			$this->CsdjBackup->restore($dirs);
            admin_msg(L('plub_10'),site_url('basedb/restore'));
	}

	//备份打包下载
	public function zip()
	{
			$dirs = $this->input->get('dir',true);
			if(empty($dirs)) admin_msg(L('plub_11'),'javascript:history.back();','no');
		    $this->load->library('zip');
            $path = FCPATH.'attachment/backup/'.$dirs.'/';
		    $this->zip->read_dir($path, FALSE);
		    $this->zip->download($dirs.'.zip'); 
	}

	//查看表结构
	public function fields()
	{
			$table = $this->input->get('table',true);
			$data['table'] = $this->CsdjBackup->repair($table);
            $this->load->view('basedb_field.html',$data);
	}

	//备份删除
	public function del()
	{
			$dir = $this->input->get('dir',true);
			if(empty($dir)){
			    $dirs = $this->input->post('id',true);
			}else{
			    $dirs[]=$dir;
			}
			if(empty($dirs)) admin_msg(L('plub_12'),'javascript:history.back();','no');
			foreach($dirs as $dir) {
                    deldir(FCPATH.'attachment/backup/'.$dir);
			}
            admin_msg(L('plub_13'),site_url('basedb/restore'));
	}
}

