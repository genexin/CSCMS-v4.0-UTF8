<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2008-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-08-01
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Opt extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
			$this->load->library('user_agent');
			$this->load->library('pinyin');
		    $this->load->model('CsdjAdmin');
			$this->CsdjAdmin->Admin_Login();
	}

	public function head()
	{
        $sqlstr="select * from ".CS_SqlPrefix."plugins order by id asc";
		$result=$this->db->query($sqlstr);
        $data['plub']=$result->result();
        $this->load->view('head.html',$data);
	}

	public function menu()
	{
        $id = intval($this->input->get('id', TRUE));
		if($id==0) $id=-1;
        $sqlstr="select * from ".CS_SqlPrefix."plugins order by id asc";
		$result=$this->db->query($sqlstr);
        $data['plub']=$result->result();
		$data['id']=$id;
        $this->load->view('menu.html',$data);
	}

	public function main()
	{
		$this->load->library('ip');
		//判断初次安装
		if(!file_exists(FCPATH.'packs/install/plub_install.lock')){
		        admin_msg(L('plub_opt_01'),site_url('opt/init'),'no','red');             
		}
		$data['code']=cs_base64_encode(arraystring(array(
			           'self' => Web_Path.SELF,
			           'version' => CS_Version,
			           'charset' => CS_Charset,
			           'uptime' => CS_Uptime,
			    )));
        $this->load->view('main.html',$data);
	}

	public function bottom()
	{
		$data['code']=cs_base64_encode(arraystring(array(
			           'self' => Web_Path.SELF,
			           'version' => CS_Version,
			           'charset' => CS_Charset,
			           'uptime' => CS_Uptime,
			    )));
        $this->load->view('bottom.html',$data);
	}

	//清空数据库缓存
	public function del_dbcache()
	{
        deldir('./cache/'.CS_Cache_Dir.'/','no');
		admin_msg(L('plub_opt_02'),'javascript:history.back();');
	}

	//清空缩放图片缓存
	public function del_piccache()
	{
        deldir('./cache/suo_pic/','no');
		admin_msg(L('plub_opt_03'),'javascript:history.back();');
	}

	//清空页面库缓存
	public function del_cache()
	{
        $sqlstr="select name,dir from ".CS_SqlPrefix."plugins order by id asc";
		$result=$this->db->query($sqlstr);
        $data['plugins']=$result->result();
        $this->load->view('cache.html',$data);        
	}

    //初始化版块数据
	public function init()
	{
		$this->load->library('csapp');
		//判断安装
		if(file_exists(FCPATH.'packs/install/plub_install.lock')){
		        admin_msg(L('plub_opt_04'),site_url('opt/main'),'no','red');             
		}

        // 搜索本地模块
		$this->load->helper('directory');
        $local = directory_map(FCPATH.'plugins/',1);
        $module = array();
        if ($local) {
            foreach ($local as $dir) {
				if (is_dir(FCPATH.'plugins/'.$dir)){
				    $api_file=FCPATH.'plugins/'.$dir.'/config/setting.php';
                    if (is_file($api_file)) {
						 $API=require_once($api_file);
                         if ($API['mid']) {
                             $module[$dir] = $API['name'];
                             $modules[] = $API['mid'];
                         }
					}
				}
            }
            unset($local);
        }
        $data['plub']=$module;
        $data['mids']=$modules;
        $this->load->view('init.html',$data);
	}

    //信息提示框
	public function error()
	{
		$link=$this->input->get('link');
        foreach ($_GET as $k=>$v) {
			if($k!='link' && $k!='neir'){
		       $link.="&".$k."=".$v."";
			}
        }
		$neir = $this->input->get('neir');
        echo '<!doctype html><html><head><meta charset=utf-8><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><link type="text/css" rel="stylesheet" href="'.Web_Path.'packs/admin/css/style.css" /></head><body><div class="tipinfo"><span><img src="'.Web_Path.'packs/admin/images/ticon.png" /></span><div class="tipright"><p style="color:red;">'.$neir.'</p></div></div><div class="tipbtn"><input onclick="parent.tip_ok(\''.$link.'\');" type="button"  class="sure" value="'.L('plub_opt_05').'" />&nbsp;<input onclick="parent.tip_cokes();" type="button"  class="cancel" value="'.L('plub_opt_06').'" /></div></div></body></html>';
	}
}

