<?php
/**
 * @Cscms 3.5 open source management system
 * @copyright 2009-2013 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2013-04-27
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

 class CsdjBackup extends CI_Model
 {
    function __construct (){
	       parent:: __construct ();
    }

    //导出数据表结构
    function repair($table){
            $output="";
		    $query = $this->db->query("SHOW CREATE TABLE `".$this->db->database.'`.`'.$table.'`');
		    $i = 0;
		    $result = $query->result_array();
		    foreach ($result[0] as $val){
			    if ($i++ % 2){
				      $output .= $val.';';
			    }
			}
			return $output;
	}

    //备份数据表结构
    function backup($tables){
        $output="";
        $newline="\r\n";

	    $bkfile = "./attachment/backup/Cscms_v4_".date('Ymd');
        if(is_dir($bkfile)){
            deldir($bkfile);  //如果今天的备份存在则先删除原备份
        }
	    $bkfile.= "/tables_".substr(md5(time().mt_rand(1000,5000)),0,16).".sql"; //名称
	    $tables=$this->db->list_tables();   
        foreach ((array)$tables as $table)
	    { 
			if(strpos($table,CS_SqlPrefix) !== FALSE){
		          $query = $this->db->query("SHOW CREATE TABLE `".$this->db->database.'`.`'.$table.'`');
		          if ($query === FALSE)
		          {
			          continue;
		          }
		          $output .= '#'.$newline.'# TABLE STRUCTURE FOR: '.$table.$newline.'#'.$newline.$newline;
		          $output .= 'DROP TABLE IF EXISTS '.$table.';'.$newline.$newline;
		          $i = 0;
		          $result = $query->result_array();
		          foreach ($result[0] as $val)
		          {
			          if ($i++ % 2)
			          {
				          $output .= $val.';'.$newline.$newline;
			          }
		          }
			}
		}
        //写文件
        if (!write_file($bkfile, $output)){
                  return FALSE;
        }else{
                  return TRUE;
        }
    }

    //备份数据内容
    function bkdata($size='1024'){
        $output = '';
        $newline="\r\n";
	    $tables=$this->db->list_tables();   
        foreach ((array)$tables as $table)
	    { 
            if(strpos($table,CS_SqlPrefix) !== FALSE){
		           $query = $this->db->query("SELECT * FROM $table");
		           if ($query->num_rows() == 0)
		           {
		                 continue;
		           }
		           $i = 0;
		           $field_str = '';
		           $is_int = array();
		           while ($field = mysql_fetch_field($query->result_id))
		           {
			           $is_int[$i] = (in_array(
							strtolower(mysql_field_type($query->result_id, $i)),
							array('tinyint', 'smallint', 'mediumint', 'int', 'bigint'), //, 'timestamp'),
							TRUE)
							) ? TRUE : FALSE;
				            $field_str .= '`'.$field->name.'`, ';
				            $i++;
		           }
			       $field_str = preg_replace( "/, $/" , "" , $field_str);
			       foreach ($query->result_array() as $row)
			       {
				           $val_str = '';
				           $i = 0;
				           foreach ($row as $v){
					           if ($v === NULL){
						           $val_str .= 'NULL';
					           }else{
						           if ($is_int[$i] == FALSE)
						           {
						    	       $val_str .= $this->db->escape($v);
						           }else{
							            $val_str .= $v;
						           }
					           }
					           $val_str .= ', ';
					           $i++;
						   }
				           $val_str = preg_replace( "/, $/" , "" , $val_str);
				           $output.= 'INSERT INTO '.$table.' ('.$field_str.') VALUES ('.$val_str.');'.$newline;
                           if(strlen($output)>($size*1024)){
                                    $bkfile = "./attachment/backup/Cscms_v4_".date('Ymd')."/datas_".substr(md5(time().mt_rand(1000,5000)),0,16).".sql"; //名称
                        	         //写文件
                        	         write_file($bkfile, $output);
                           	         $output="";
						   }
				   }
			       $output.= $newline.$newline;
            }
		}
        if(!empty($output)){
               $bkfile = "./attachment/backup/Cscms_v4_".date('Ymd')."/datas_".substr(md5(time().mt_rand(1000,5000)),0,16).".sql"; 
               //写文件
               write_file($bkfile, $output);
        }
        return TRUE;
    }

    //还原数据
    function restore($name)
    {
		$this->load->helper('file');
        $lnk=@mysql_connect(CS_Sqlserver,CS_Sqluid,CS_Sqlpwd);
	    @mysql_select_db(CS_Sqlname,$lnk);
	    @mysql_query("SET NAMES ".CS_Sqlcharset."", $lnk);
        $path="./attachment/backup/".$name."/";
        $strs=get_dir_file_info($path, $top_level_only = TRUE);
        foreach ($strs as $value) {
            if(!is_dir($path.$value['name'])){
			     $fullpath=$path.$value['name'];
			     //还原表结构
			     if(substr($value['name'],0,7)=="tables_"){
					    $tabel_stru=file_get_contents($fullpath);
					    $tabelarr=explode(";",$tabel_stru);
						for($i=0;$i<count($tabelarr)-1;$i++){
						    @mysql_query($tabelarr[$i]);					  							 
						}
			     } 
      			 if(substr($value['name'],0,6)=="datas_"){
         		 	    //数据列表
				        $filearr[]=$fullpath;
     			 } 
			}
		}
        //开始还原数据
	    for($i=0;$i<count($filearr);$i++){
		    $tabel_datas=file_get_contents(trim($filearr[$i]));
		    $dataarr=explode("\n",$tabel_datas);
		    for($j=0;$j<count($dataarr);$j++){
			    @mysql_query($dataarr[$j]);
		    }
	    }
        @mysql_close();
        return TRUE;
    }
}

