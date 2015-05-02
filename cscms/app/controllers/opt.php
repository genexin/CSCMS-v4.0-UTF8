<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-11-24
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Opt extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjTpl');
	}

	public function index()
	{
            $name = $this->uri->segment(2);   //页面标示
            if(empty($name)){
                    msg_url(L('opt_01'),Web_Path);
            }
            $this->CsdjTpl->opt($name);
	}
}
