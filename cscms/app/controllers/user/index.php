<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-04-27
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Index extends Cscms_Controller {
	function __construct(){
		    parent::__construct();
	}
	public function index()
	{
		    $template=$this->load->view('index.html','',true);
			$Mark_Text=str_replace("{cscms:title}","会员 - ".Web_Name,$template);
			$Mark_Text=str_replace("{cscms:keywords}",Web_Keywords,$Mark_Text);
			$Mark_Text=str_replace("{cscms:description}",Web_Description,$Mark_Text);
            $Mark_Text=$this->skins->template_parse($Mark_Text,true);
			echo $Mark_Text;
	}
}
