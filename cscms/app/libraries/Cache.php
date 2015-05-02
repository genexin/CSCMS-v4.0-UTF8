<?php
/**
 * @Cscms 3.5 open source management system
 * @copyright 2009-2013 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2013-04-27
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 文件缓存类
 */
class Cache {

    function __construct ()
	{
            $dir = (!defined('PLUBPATH')) ? 'index' : PLUBPATH;
			$this->_dir  = FCPATH."cache/".$dir."/";
			$this->_time = (!defined('PLUBPATH')) ? Cache_Time : config('Cache_Time');;
			$this->_is   = (!defined('PLUBPATH')) ? Cache_Is : config('Cache_Is');
	}

    //读取缓存
	function get($cacheid){
		$this->_id = md5($cacheid);
		if(file_exists($this->_dir.$this->_id) && ((time() - filemtime($this->_dir.$this->_id)) < $this->_time)){
			$data = @file_get_contents($this->_dir.$this->_id);
			return $data;
		}else{
			return false;
		}
	}  

    //写入缓存
	function save($data){
		if(!is_writable($this->_dir)){
			if(!@mkdir($this->_dir,0777,true)){
				echo 'Cache directory not writable';
				exit;
			}
		}
		@file_put_contents($this->_dir.$this->_id,$data);
		return true;
	}

    //获取缓存
	function start($id){
		$data = $this->get($id);
		
		if($this->_is==1 && $data !== false){
			exit($data);
			return true;
		}
		ob_start();
		ob_implicit_flush(false);
		return false;
	}

	function end(){
		$data = ob_get_contents();
		ob_end_clean();
		if($this->_is==1) $this->save($data);
		echo($data);
	}

}

