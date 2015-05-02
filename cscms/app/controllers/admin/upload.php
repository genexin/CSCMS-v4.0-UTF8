<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2008-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-09-20
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Upload extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjAdmin');
			$this->load->helper('string');
			$this->lang->load('admin_upload');
	}

	//网站附件管理
	public function index()
	{
	        $this->CsdjAdmin->Admin_Login();
		    $this->load->helper('file');
 	        $path = $this->input->get('path',true);
 	        $page = $this->input->get('page',true);
            if(empty($page)) $page=1;
            if(empty($path)){
				$path=Web_Path."attachment/";
			    if(UP_Pan!=''){
			        $path = UP_Pan.$path;
				    $path = str_replace("//","/",$path);
				}
			}
			if(substr($path,0,1)!='/' && UP_Pan=='') $path="/".$path;
            if(substr(str_replace(array(UP_Pan,Web_Path),array("",""),$path),0,10)!='attachment'){
                    admin_msg(L('plub_01'),'javascript:history.back();','no');
            }
            $paths=(UP_Pan!='')?$path:str_replace(Web_Path."attachment/",FCPATH."attachment/",$path);
			if(preg_match("/[\x7f-\xff]/", $paths)){
                 $paths=get_bm($paths,'utf-8','gbk');
			}
            $showarr=get_dir_file_info($paths, $top_level_only = TRUE);
            $dirs=$list=array();
		    if ($showarr) {
			    foreach ($showarr as $t) {
					$t['name']=preg_match("/[\x7f-\xff]/", $t['name'])?get_bm($t['name']):$t['name'];
				    if (is_dir($t['server_path'])) {
					    $dirs[] = array(
						    'name' => $t['name'],
						    'date' => date('Y-m-d H:i:s',$t['date']),
						    'icon' => Web_Path.'packs/admin/images/ext/dir.gif',
						    'link' => site_url('upload')."?path=".$path.$t['name']."/",
						    'dellink' => site_url('upload/del')."?path=".$path.$t['name']."/",
					    );
				    } else {
						$exts = trim(strrchr($t['name'], '.'), '.');
                        if(UP_Pan!=''){
                             $link=UP_Url.str_replace(UP_Pan,"",$path.$t['name']);
						}else{
                             $link='http://'.Web_Url.$path.$t['name'];
						}
						$list[] = array(
							'name' => $t['name'],
							'ext' => get_extpic($exts),
						    'date' => date('Y-m-d H:i:s',$t['date']),
						    'size' => formatsize($t['size']),
							'icon' => Web_Path.'packs/admin/images/ext/'.get_extpic($exts).'.gif',
							'link' => $link,
						    'dellink' => site_url('upload/del')."?path=".$path.$t['name'],
						);
				    }
			    }
		    }
            $data['path']=preg_match("/[\x7f-\xff]/", $path)?get_bm($path):$path;
			$data['dirs']=preg_match("/[\x7f-\xff]/", $dirs)?get_bm($dirs):$dirs;
			$data['show']=$list;

            if(str_replace(array(UP_Pan,Web_Path),array("",""),$path)=="attachment"){
                $data['uppage']="###";
            }else{
                $data['uppage']="javascript:history.back();";
            }
            $this->load->view('upload_dir.html',$data);
	}

	//删除附件
	public function del()
	{
	        $this->CsdjAdmin->Admin_Login();
 	        $path = $this->input->get('path',true);
			if(empty($path)) admin_msg(L('plub_02'),'javascript:history.back();','no');
			$path=FCPATH.$path;
            if (is_dir($path)) {
                 deldir($path);
			}else{
				 @unlink($path);
			}
            admin_msg(L('plub_03'),'javascript:history.back();','ok');
	}

    //上传附件
	public function up()
	{
	        $this->CsdjAdmin->Admin_Login();
		    $nums=intval($this->input->get('nums')); //支持数量
		    $types=$this->input->get('type',true);  //支持格式
            $fhid=$this->input->get('fhid',true); //返回ID参数
            $data['fhid']=(empty($fhid))?"pic":$fhid;
            $data['sid']=intval($this->input->get('sid')); //返回输入框方法，0替换、1换行增加
            $data['dir']=$this->input->get('dir',true);   //上传目录
            $data['fid']=$this->input->get('fid',true);   //返回ID，一个页面多个返回可以用到
            $data['upsave']=site_url('upload/up_save');
            $data['size'] = UP_Size;
            $data['types'] =(empty($types))?"*.gif;*.png;*.jpg":$types;
            $data['nums']=($nums==0)?1:$nums;
			if($data['fid']=='undefined') $data['fid']='';
			$str['id']=$_SESSION['admin_id'];
			$str['name']=$_SESSION['admin_name'];
			$str['pass']=$_SESSION['admin_pass'];
            $data['key'] = sys_auth(addslashes(serialize($str)),'E');
            $this->load->view('upload.html',$data);
	}

    //保存附件
	public function up_save()
	{
            $key=$this->input->post('key',true);
	        $this->CsdjAdmin->Admin_Login($key);
            $dir=$this->input->post('dir',true);
			if(empty($dir) || !preg_match('/^[0-9a-zA-Z\_]*$/', $dir)) {  
                 $dir='other';
			}
			//上传目录
			if(UP_Mode==1 && UP_Pan!=''){
			    $path = UP_Pan.'/attachment/'.$dir.'/'.date('Ym').'/'.date('d').'/';
				$path = str_replace("//","/",$path);
			}else{
			    $path = FCPATH.'attachment/'.$dir.'/'.date('Ym').'/'.date('d').'/';
			}
			if (!is_dir($path)) {
                mkdirss($path);
            }
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$file_name = $_FILES['Filedata']['name'];
			$file_size = filesize($tempFile);
	        $file_ext = strtolower(trim(substr(strrchr($file_name, '.'), 1)));

	        //检查扩展名
			$ext_arr = explode("|", UP_Type);
	        if (in_array($file_ext,$ext_arr) === false) {
                exit(escape(L('plub_04')));
			}elseif($file_ext=='jpg' || $file_ext=='png' || $file_ext=='gif' || $file_ext=='bmp' || $file_ext=='jpge'){
				list($width, $height, $type, $attr) = getimagesize($tempFile);
				if ( intval($width) < 10 || intval($height) < 10 || $type == 4 ) {
                    exit(escape(L('plub_05')));
				}
			}
            //PHP上传失败
            if (!empty($_FILES['Filedata']['error'])) {
	            switch($_FILES['Filedata']['error']){
		            case '1':
			            $error = L('plub_06');
			            break;
		            case '2':
			            $error = L('plub_07');
			            break;
		            case '3':
			            $error = L('plub_08');
			            break;
		            case '4':
			            $error = L('plub_09');
			            break;
		            case '6':
			            $error = L('plub_10');
			            break;
		            case '7':
			            $error = L('plub_11');
			            break;
		            case '8':
			            $error = 'File upload stopped by extension。';
			            break;
		            case '999':
		            default:
			            $error = L('plub_12');
	            }
	            exit(escape($error));
            }
            //新文件名
            //$file_name=date("YmdHis") . rand(10000, 99999) . '.' . $file_ext;
			$file_name=random_string('alnum', 20). '.' . $file_ext;
			$file_path=$path.$file_name;
			if (move_uploaded_file($tempFile, $file_path) !== false) { //上传成功

                    $filepath=(UP_Mode==1)?'/'.date('Ym').'/'.date('d').'/'.$file_name : '/'.date('Ymd').'/'.$file_name;

                    //判断水印
                    if($dir!='links' && CS_WaterMark==1){
						if($file_ext=='jpg' || $file_ext=='png' || $file_ext=='gif' || $file_ext=='bmp' || $file_ext=='jpge'){
		                     $this->load->library('watermark');
                             $this->watermark->imagewatermark($file_path);
						}
                    }

					//判断上传方式
                    $this->load->library('csup');
					$res=$this->csup->up($file_path,$file_name);
					if($res){
						if(UP_Mode==0 && ($dir=='music' || $dir=='video')){
						    $filepath='attachment/'.$dir.'/'.$filepath;
						}
					    exit('ok=cscms='.$filepath);
					}else{
						@unlink($file_path);
	                    exit('no');
					}

			}else{ //上传失败
				  exit('no');
			}
	}

	//网站附件
	public function myattach()
	{
	        $this->CsdjAdmin->Admin_Login();
		    $this->load->helper('directory');
 	        $path = $this->input->get('path',true);
 	        $ext = $this->input->get('ext',true);

            if(empty($ext)) $ext='jpg|png|gif';

            if(empty($path)){
				$path=Web_Path."attachment/";
			    if(UP_Pan!=''){
			        $path = UP_Pan.$path;
				    $path = str_replace("//","/",$path);
				}
			}
			if(substr($path,0,1)!='/' && UP_Pan=='') $path="/".$path;
            if(substr(str_replace(array(UP_Pan,Web_Path),array("",""),$path),0,10)!='attachment'){
                    admin_msg(L('plub_01'),'javascript:history.back();','no');
            }
            $paths=(UP_Pan!='')?$path:str_replace(Web_Path."attachment/",FCPATH."attachment/",$path);

			$dirs = $list = array();
			$ext2 = explode('|', $ext);
			$path=str_replace('//','/',$path);
            $arrs=directory_map($paths, 1);
		    if ($arrs) {
			    foreach ($arrs as $t) {
				    if (is_dir($paths.$t)) {
					    $name = trim($t, DIRECTORY_SEPARATOR);
					    $dirs[] = array(
						    'name' => $name,
						    'icon' => Web_Path.'packs/admin/images/ext/dir.gif',
						    'link' => site_url('upload/myattach')."?ext=".$ext."&path=".$path.$name."/",
					    );
				    } else {
					    $exts = trim(strrchr($t, '.'), '.');
					    if ($ext != 'php' && in_array($exts, $ext2)) {
                            if(UP_Pan!=''){
                                 $link=UP_Url.str_replace(UP_Pan,"",$path.$t);
						    }else{
                                 $link='http://'.Web_Url.$path.$t;
						    }
						    $list[] = array(
							    'name' => $t,
							    'ext'  => get_extpic($exts),
							    'icon' => Web_Path.'packs/admin/images/ext/'.get_extpic($exts).'.gif',
							    'link' => $link,
						    );
					    }
				    }
			    }
		    }
            $data['path']=$path;
            $data['ext']=$ext;
            $data['url']=site_url('upload/myattach')."?ext=".$ext."&path=".$path;
            $data['dirs']=$dirs;
            $data['show']=$list;

            if(str_replace(array(UP_Pan,Web_Path),array("",""),$path)=="attachment"){
                $data['uppage']=site_url('upload/myattach');
            }else{
                $data['uppage']="javascript:history.back();";
            }
            $this->load->view('myattach.html',$data);
	}
}
