<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2008-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-08-01
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Sms extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
			$this->load->library('smstel');
			$this->lang->load('admin_sms');
	}

	public function index()
	{
            $this->load->view('sms_setting.html');
	}

	public function add()
	{
		    $CS_Sms_ID=CS_Sms_ID;
		    $CS_Sms_Key=CS_Sms_Key;
		    if(empty($CS_Sms_ID) || empty($CS_Sms_Key)){
                  admin_msg(L('plub_01'),site_url('sms'),'no');
			}
            $this->load->view('sms_add.html');
	}

	public function lists()
	{
		    $page = intval($this->input->get('page', TRUE));
			if($page==0) $page=1;
			$strs=$this->smstel->lists(12,$page);
			if(!empty($strs)){
			       $arr = unarraystring($strs);
			       $data['lists'] = $arr['lists'];
			       $data['pagejs'] = $arr['pagejs'];
			       $data['nums'] = $arr['nums'];
			       $data['page'] = $page;
			       $data['pages'] = get_admin_page(site_url('sms/lists').'?',$arr['pagejs'],$page);
			}else{
			       $data['lists'] = array();
			       $data['pagejs'] = 0;
			       $data['nums'] = 0;
			       $data['page'] = $page;
			       $data['pages'] = 0;
			}
            $this->load->view('sms_list.html',$data);
	}

	public function save()
	{
		    $CS_Sms_ID = trim($this->input->post('CS_Sms_ID', TRUE));
		    $CS_Sms_Key = trim($this->input->post('CS_Sms_Key', TRUE));
		    $CS_Sms_Name = $this->input->post('CS_Sms_Name', TRUE);
			if(substr(CS_Sms_Key,0,4).'********'==$CS_Sms_Key){
                  $CS_Sms_Key=CS_Sms_Key;
			}

	        $strs="<?php"."\r\n";
	        $strs.="define(\"CS_Sms_ID\",\"".$CS_Sms_ID."\");  //商户ID      \r\n";
	        $strs.="define(\"CS_Sms_Key\",\"".$CS_Sms_Key."\");  //商户KEY      \r\n";
	        $strs.="define(\"CS_Sms_Name\",\"".$CS_Sms_Name."\");  //短信签名    ";

            //写文件
            if (!write_file(CSCMS.'lib/Cs_Sms.php', $strs)){
                      admin_msg(L('plub_02'),site_url('sms'),'no');
            }else{
                      admin_msg(L('plub_03'),site_url('sms'));
            }
	}

	public function add_save()
	{
		    $sid = intval($this->input->post('sid', TRUE));
		    $tel = $this->input->post('tel', TRUE);
		    $tel2 =  nl2br($this->input->post('tel2'));
		    $neir = $this->input->post('neir');

		    if (empty($neir)){
			      admin_msg(L('plub_04'),'javascript:history.back();','no');
		    }

			if($sid==1){
                 $arr=$tel;
			}elseif($sid==2){
				 $arr=str_replace("<br />",",",$tel2);
				 $arr=str_replace("\r\n","",$arr);
			}
		    if (empty($arr)){
			      admin_msg(L('plub_05'),'javascript:history.back();','no');
		    }
			$res=$this->smstel->add($arr,$neir);
			if(intval($res)>0){
                   admin_msg(vsprintf(L('plub_06'),array($res)),'javascript:history.back();','ok');
			}else{
                   admin_msg(L('plub_07'),'javascript:history.back();','no');
			}
	}
}

