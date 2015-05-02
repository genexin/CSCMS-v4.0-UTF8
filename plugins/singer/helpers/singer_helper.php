<?php
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-08-21
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

//解析多个分类ID  如 cid=1,2,3,4,5,6
function getChild($CID,$type='singer_list',$zd='fid'){
	 $ci = &get_instance();
	 if (!isset($ci->db)){
        $ci->load->database();
	 }
     if(!empty($CID)){
            $ClassArr=explode(',',$CID);
            for($i=0;$i<count($ClassArr);$i++){
				$sql="select id from ".CS_SqlPrefix.$type." where ".$zd."='$ClassArr[$i]'";//sql语句的组织返回
				$result=$ci->db->query($sql);
	        	$total = $result->num_rows();
				if($total>0){
                       foreach ($result->result() as $row) {
                            $ClassArr[]=$row->id;
                       }
                }
          		$CID=implode(',',$ClassArr);
       		}
	 }
	 return $CID;
}
//获取数据总数
function getcount($table='singer',$sid=0){
	 $ci = &get_instance();
	 if (!isset($ci->db)){
        $ci->load->database();
	 }
	 $sql="select id from ".CS_SqlPrefix.$table." where yid=0 and hid=0";
     if($sid>0){
		$time=strtotime(date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y"))));
	    $sql.=" and addtime>".$time."";
     }
	 $query=$ci->db->query($sql);
     $nums=$query->num_rows();
	 return $nums;
}

//标签解析
function SearchLink($Search_Key,$fid='tags'){
	 $Search_link='http://'.Web_Url.Web_Path.'index.php/singer/'; //板块地址
     $Search_List=$Search_Key1="";
     $Search_Key=trim($Search_Key);
     $Str=" @,@，@|@/@_";
     $StrArr=explode('@',$Str);
     for($i=0;$i<=5;$i++){
            if(stristr($Search_Key,$StrArr[$i])){
                 $Search_Key1=explode($StrArr[$i],$Search_Key);
            }
	 }
     if(is_array($Search_Key1)){
            for($j=0;$j<count($Search_Key1);$j++){
                 $Search_List.="<A target=\"tags\" HREF=\"".$Search_link."search?".$fid."=".urlencode($Search_Key1[$j])."\">".$Search_Key1[$j]."</A> ";
			}
	 }else{
                 $Search_List="<A target=\"tags\" HREF=\"".$Search_link."search?".$fid."=".urlencode($Search_Key)."\">".$Search_Key."</A> ";
	 }
	 //开启二级域名
	 $Ym_Mode=config('Ym_Mode','singer'); //二级域名状态
	 $Ym_Url=config('Ym_Url','singer');   //二级域名地址
	 if($Ym_Mode==1){
	       $Search_List  = str_replace(Web_Url.Web_Path."index.php/singer/",$Ym_Url.Web_Path,$Search_List);
	 }
     return $Search_List;
}
