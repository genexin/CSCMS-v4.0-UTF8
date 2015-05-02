<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-11-24
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Index extends Cscms_Controller {
	function __construct(){
		    parent::__construct();
		    $this->load->helper('dance');
		    $this->load->model('CsdjTpl');
	}

    //主页
	public function index()
	{
            //判断运行模式,生成则跳转至静态页面
			$uri=config('Html_Uri');
	        if(config('Web_Mode')==3 && $uri['index']['check']==1){
				$index_url=$uri['index']['url'];
				header("Location: ".$index_url);
				exit;
			}
			//装载模板并输出
	        $this->CsdjTpl->plub_index('dance');
	}
}
