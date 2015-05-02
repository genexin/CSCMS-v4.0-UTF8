<?php
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-11-18
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 上传操作类
 */
class Csup {

    function __construct ()
	{
		error_reporting(0); //关闭错误
		set_time_limit(0); //超时时间
		require_once CSCMS.'lib/Cs_Qiniu.php';
		require_once CSCMS.'lib/Cs_Kpan.php';
	    require_once CSCMS.'lib/Cs_Oss.php';
	    require_once CSCMS.'lib/Cs_Upyun.php';
	}

    //上传方式
	function init(){
        $str = array(
			'1'=>array(
			      'name'=>'本地空间',
			      'type'=>'web',
			      'file'=>''
		     ),
			'2'=>array(
			      'name'=>'远程FTP',
			      'type'=>'ftp',
			      'file'=>''
		     ),
			'3'=>array(
			      'name'=>'七牛网盘',
			      'type'=>'qiniu',
			      'file'=>'Cs_Qiniu.php'
		     ),
			'4'=>array(
			      'name'=>'金山快盘',
			      'type'=>'kdisk',
			      'file'=>'Cs_Kpan.php'
		     ),
			'5'=>array(
			      'name'=>'阿里云存储',
			      'type'=>'oss',
			      'file'=>'Cs_Oss.php'
		     ),
			'6'=>array(
			      'name'=>'又拍云',
			      'type'=>'upyun',
			      'file'=>'Cs_Upyun.php'
		     )
		);
		return $str;
    }

    //判断上传方式
	function up($file_path,$file_name=''){
		if(empty($file_path)) return false;
        $types=$this->init();
		if(UP_Mode>1){
			  $mode=$types[UP_Mode]['type']; //方法
              return $this->$mode($file_path,$file_name);
        }else{
		      return true;
		}
    }

	//获取下载地址
	function down($ids){
        if($ids==3){  //七牛网盘
			$linkurl=CS_Qn_Url;
			if($linkurl!=''){
                if(substr($linkurl,0,7)!='http://') $linkurl="http://".$linkurl;
			}else{
                $linkurl="http://".CS_Qn_Bk.".qiniudn.com";
			}
        }elseif($ids==4){  //快盘
              $linkurl="http://".Web_Url.Web_Path."packs/uploads/kdisk/acc.php?filename=";
        }elseif($ids==5){  //阿里云
              $linkurl="http://".CS_Os_Bk.".oss-cn-hangzhou.aliyuncs.com";
        }elseif($ids==6){  //又拍云
              $linkurl="http://".Web_Url.Web_Path."packs/uploads/upyun/down.php";
		}else{
              $linkurl="";
		}
        return $linkurl;
	}

	//修改配置文件
	function edit($id){
        $str = $this->init();
		$files = $str[$id]['file'];

        if($id<3 || empty($files)) return false;
        $ci = &get_instance();

		//七牛存储
		if($id==3){ 
		    $CS_Qn_Bk = $ci->input->post('CS_Qn_Bk', TRUE);
		    $CS_Qn_Ak = $ci->input->post('CS_Qn_Ak', TRUE);
		    $CS_Qn_Sk = $ci->input->post('CS_Qn_Sk', TRUE);
		    $CS_Qn_Url = $ci->input->post('CS_Qn_Url', TRUE);
		    if (empty($CS_Qn_Bk)||empty($CS_Qn_Ak)||empty($CS_Qn_Sk)){
			    admin_msg('七牛空间名称、AccessKey、SecretKey都不能为空','javascript:history.back();','no');
		    }
	        $strs="<?php"."\r\n";
	        $strs.="define(\"CS_Qn_Bk\",\"".$CS_Qn_Bk."\"); //空间名称 \r\n";
	        $strs.="define(\"CS_Qn_Ak\",\"".$CS_Qn_Ak."\"); //AK   \r\n";
	        $strs.="define(\"CS_Qn_Sk\",\"".$CS_Qn_Sk."\"); //SK  \r\n";
	        $strs.="define(\"CS_Qn_Url\",\"".$CS_Qn_Url."\"); //七牛下载地址  ";
		}

        //快盘存储
		if($id==4){
		    $CS_Kp_Ck = $ci->input->post('CS_Kp_Ck', TRUE);
		    $CS_Kp_Cs = $ci->input->post('CS_Kp_Cs', TRUE);
		    $CS_Kp_Acc = $ci->input->post('CS_Kp_Acc', TRUE);
		    $CS_Kp_Acc_Key = $ci->input->post('CS_Kp_Acc_Key', TRUE);

		    if (empty($CS_Kp_Ck) || empty($CS_Kp_Cs)){
			      admin_msg('快盘应用目录、consumer_key、consumer_secret都不能为空','javascript:history.back();','no');
		    }

	        $strs="<?php"."\r\n";
	        $strs.="define(\"CS_Kp_Ck\",\"".$CS_Kp_Ck."\"); //CK   \r\n";
	        $strs.="define(\"CS_Kp_Cs\",\"".$CS_Kp_Cs."\"); //Cs  \r\n";
	        $strs.="define(\"CS_Kp_Acc\",\"".$CS_Kp_Acc."\"); //AC  \r\n";
	        $strs.="define(\"CS_Kp_Acc_Key\",\"".$CS_Kp_Acc_Key."\"); //AK  ";
		}

        //阿里云存储
	    if($id==5){
		    $CS_Os_Bk = $ci->input->post('CS_Os_Bk', TRUE);
		    $CS_Os_Ai = $ci->input->post('CS_Os_Ai', TRUE);
		    $CS_Os_Ak = $ci->input->post('CS_Os_Ak', TRUE);
		    if (empty($CS_Os_Bk)||empty($CS_Os_Ai)||empty($CS_Os_Ak)){
			      admin_msg('BUCKET、ACCESS_ID、ACCESS_KEY都不能为空','javascript:history.back();','no');
		    }
	        $strs="<?php"."\r\n";
	        $strs.="define(\"CS_Os_Bk\",\"".$CS_Os_Bk."\"); //BK  \r\n";
	        $strs.="define(\"CS_Os_Ai\",\"".$CS_Os_Ai."\"); //AI   \r\n";
	        $strs.="define(\"CS_Os_Ak\",\"".$CS_Os_Ak."\"); //AK  ";
		}

        //又拍云存储
	    if($id==6){
		    $CS_Upy_Name = $ci->input->post('CS_Upy_Name', TRUE);
		    $CS_Upy_Pwd = $ci->input->post('CS_Upy_Pwd', TRUE);
		    $CS_Upy_Bucket = $ci->input->post('CS_Upy_Bucket', TRUE);
		    if (empty($CS_Upy_Name)||empty($CS_Upy_Pwd)||empty($CS_Upy_Bucket)){
			      admin_msg('授权账号、授权密码、空间名都不能为空','javascript:history.back();','no');
		    }
			if($CS_Upy_Pwd==substr(CS_Upy_Pwd,0,3).'******'){
                  $CS_Upy_Pwd=CS_Upy_Pwd;
			}
	        $strs="<?php"."\r\n";
	        $strs.="define(\"CS_Upy_Name\",\"".$CS_Upy_Name."\"); //又拍云授权账号  \r\n";
	        $strs.="define(\"CS_Upy_Pwd\",\"".$CS_Upy_Pwd."\"); //又拍云授权密码   \r\n";
	        $strs.="define(\"CS_Upy_Bucket\",\"".$CS_Upy_Bucket."\"); //又拍云空间名 ";
		}

		//修改配置文件
        write_file(CSCMS.'lib/'.$files, $strs);
		return true;
	}

    //判断删除方式
	function del($file_path,$dir=''){
		if(empty($file_path)) return false;
		if(UP_Mode==2){ //FTP
              return $this->ftpdel($file_path);
		}elseif(UP_Mode==3){ //七牛
              return $this->qiniudel($file_path);
		}elseif(UP_Mode==4){ //快盘
              return $this->kdiskdel($file_path);
		}elseif(UP_Mode==5){ //阿里云
              return $this->ossdel($file_path);
		}elseif(UP_Mode==6){ //又拍云
              return $this->upyundel($file_path);
        }else{
			  if(UP_Pan!=''){
			      $path = UP_Pan.'/attachment/'.$dir.'/'.$file_path;
				  $path = str_replace("//","/",$path);
			  }else{
			      $path = FCPATH.'attachment/'.$dir.'/'.$file_path;
			  }
			  unlink($path);
		      return true;
		}
    }

    //FTP上传
	function ftp($file_path,$file_name){
		$this->load->library('ftp');
		if ($this->ftp->connect(array(
				'port' => FTP_Port,
				'debug' => FALSE,
				'passive' => FTP_Ive,
				'hostname' => FTP_Server,
				'username' => FTP_Name,
				'password' => FTP_Pass,
		))) { // 连接ftp成功
				$Dirs=FTP_Dir;
				if(substr($Dirs,-1)=='/') $Dirs=substr($Dirs,0,-1);
				$dir = $Dirs.'/'.date('Ymd').'/';
				$this->ftp->mkdir($dir);
				if ($this->ftp->upload($file_path, $dir.$file_name, SITE_ATTACH_MODE, 0775)) {
				     unlink($file_path);
				}else{
                     return false;
				}
				$this->ftp->close();
		        return true;
		}
    }

    //删除远程FTP文件
    function ftpdel($file_path){
		if(substr($file_path,0,1)=='/') $file_path=substr($file_path,1);
		if ($this->ftp->connect(array(
				'port' => FTP_Port,
				'debug' => FALSE,
				'passive' => FTP_Ive,
				'hostname' => FTP_Server,
				'username' => FTP_Name,
				'password' => FTP_Pass,
		))) { // 连接ftp成功
				$Dirs=FTP_Dir;
				if(substr($Dirs,-1)=='/') $Dirs=substr($Dirs,0,-1);
				$path = $Dirs.'/'.$file_path;
				$res=$this->ftp->delete_file(".".$path);
				$this->ftp->close();
                if (!$res) {  //失败
                    return FALSE;
                } else {  //成功
                    return TRUE;
                }
		}
    }

    //七牛上传
	function qiniu($file_path,$file_name){
		require_once CSCMSPATH.'uploads/qiniu/io.php';
		require_once CSCMSPATH.'uploads/qiniu/rs.php';

		$key1 = date('Ymd').'/'.$file_name;
		Qiniu_SetKeys(CS_Qn_Ak, CS_Qn_Sk);
		$putPolicy = new Qiniu_RS_PutPolicy(CS_Qn_Bk);
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		list($ret, $err) = Qiniu_PutFile($upToken, $key1, $file_path, $putExtra);
		if ($err !== null) {
   		    return false;
		} else {
			unlink($file_path);
    		return true;
		}
    }

    //删除七牛文件
    function qiniudel($file_path){
		require_once CSCMSPATH.'uploads/qiniu/io.php';
		require_once CSCMSPATH.'uploads/qiniu/rs.php';
		Qiniu_SetKeys(CS_Qn_Ak, CS_Qn_Sk);
        $client = new Qiniu_MacHttpClient(null);
		if(substr($file_path,0,1)=='/') $file_path=substr($file_path,1);
        $err = Qiniu_RS_Delete($client, CS_Qn_Bk, $file_path);
        if ($err !== null) {  //失败
            return FALSE;
        } else {  //成功
            return TRUE;
        }
    }

	//快盘上传
	function kdisk($file_path,$file_name){
		require_once CSCMSPATH.'uploads/kdisk/kuaipan.class.php';
        $access_token_arr['oauth_token']=CS_Kp_Acc;
        $access_token_arr['oauth_token_secret']=CS_Kp_Acc_Key;
        $kp = new Kuaipan ( CS_Kp_Ck, CS_Kp_Cs);
        $params = array (
            'root' => 'app_folder',
            'path' => date('Ymd')
        );
        $ret = $kp->api ( 'fileops/create_folder', '', $params , 'GET', '' ,$access_token_arr);
        if (false === $ret) {
   		    return false;
        }

        $upload = $kp->api ( 'fileops/upload_locate', '', array (), 'GET', '' ,$access_token_arr);
        if (false !== $upload) {
            $params = array (
                'root' => 'app_folder',
                'overwrite' => 'true',
                'path' => date('Ymd').'/'.$file_name 
            );
            // 传入upload的绝对地址
            $ret = $kp->api ( sprintf ( '%s/1/fileops/upload_file', rtrim ( $upload ['url'], '/' ) ), '', $params, 'POST', $file_path ,$access_token_arr );
            if (false === $ret) {
   		        return false;
            }
			unlink($file_path);
            return true;
        } else {
   		    return false;
		}
    }

    //删除快盘文件
    function kdiskdel($file_path){
		require_once CSCMSPATH.'uploads/kdisk/kuaipan.class.php';
		if(substr($file_path,0,1)=='/') $file_path=substr($file_path,1);
        $kp = new Kuaipan ( CS_Kp_Ck, CS_Kp_Cs);
        $params = array (
            'root' => 'app_folder',
            'path' => $file_path
        );
        $access_token_arr['oauth_token']=CS_Kp_Acc;
        $access_token_arr['oauth_token_secret']=CS_Kp_Acc_Key;
        $ret = $kp->api ( 'fileops/delete', '', $params  , 'GET', '' ,$access_token_arr);
        if (false === $ret) {
    		return false;
		}else{
            return true;
		}
    }

	//阿里云上传
	function oss($file_path,$file_name){
		require_once CSCMSPATH.'uploads/oss/sdk.class.php';
        $obj = new ALIOSS();
	    $dir=date('Ymd');
        $response  = $obj->create_object_dir(BUCKET,$dir);
		if ($response->status != '200') {
    		return false;
		}
        $object=date('Ymd').'/'.$file_name;
	    $response = $obj->upload_file_by_file(BUCKET,$object,$file_path);
		if ($response->status == 200) {
			unlink($file_path);
            return true;
		}else{
    		return false;
		}
    }

    //删除阿里云文件
    function ossdel($file_path){
		require_once CSCMSPATH.'uploads/oss/sdk.class.php';
		if(substr($file_path,0,1)=='/') $file_path=substr($file_path,1);
        $obj = new ALIOSS();
	    $response = $obj->delete_object(BUCKET,$file_path);
		if ($response->status == 200) {
            return true;
		}else{
    		return false;
		}
    }

	//又拍云上传
	function upyun($file_path,$file_name){
		require_once CSCMSPATH.'uploads/upyun/upyun.class.php';
        $upyun = new UpYun(CS_Upy_Bucket, CS_Upy_Name, CS_Upy_Pwd);
        $object='/'.date('Ymd').'/'.$file_name;
		$data = @file_get_contents($file_path);
        $response = $upyun->writeFile($object, $data, True);
		if ($response) {
			unlink($file_path);
            return true;
		}else{
    		return false;
		}
    }

    //删除又拍云文件
    function upyundel($file_path){
		require_once CSCMSPATH.'uploads/upyun/upyun.class.php';
        $upyun = new UpYun(CS_Upy_Bucket, CS_Upy_Name, CS_Upy_Pwd);
        $response = $upyun->delete($file_path);
		if ($response) {
            return true;
		}else{
    		return false;
		}
    }
}


