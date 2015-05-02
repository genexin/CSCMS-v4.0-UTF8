<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-09-20
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Count extends Cscms_Controller {

	function __construct(){
		  parent::__construct();
          $this->load->library('user_agent');
          if(!$this->agent->is_referral()) show_error('您访问的页面不存在~!',404,Web_Name.'提醒您');
	      //关闭数据库缓存
          $this->db->cache_off();
	}

    //版块数据统计
	public function index()
	{
		  $count=0;
		  $param=$this->input->get('param',true,true);
		  if(!empty($param)){
		       $str=explode('|', $param);
			   $table=$str[0];
			   if(!empty($table) && $this->db->table_exists(CS_SqlPrefix.$table)){
				    $sql="";
			        for($j=1;$j<count($str);$j++){	
				         $v=explode('=', $str[$j]);
					     if($v[0]=='times'){
                             $k=explode('-', $v[1]);
						     $fidel=$k[0];
						     $day=intval($k[1]);
						     $times=strtotime(date('Y-m-d 0:0:0'))-$day*86400;
                             $sql.="and ".$fidel.">".$times." ";
					     }else{
							 if(!empty($v[1])){
                                 $sql.="and ".$v[0]."='".$v[1]."' ";
							 }else{
                                 $sql.="and ".$str[$j]." ";
							 }
					     }
					}
					if(substr($sql,0,3)=='and') $sql=substr($sql,3);
					if(!empty($sql)) $sql=" where".$sql;
					$sql="select id from `".CS_SqlPrefix.$table."` ".$sql;
					$count=$this->db->query($sql)->num_rows();
			   }
		  }
          echo 'document.writeln("'.$count.'")';
	}
}
