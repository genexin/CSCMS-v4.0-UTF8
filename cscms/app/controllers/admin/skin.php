<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2008-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-11-07
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Skin extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
		    $this->load->helper('directory');
            $this->load->library('csapp');
			$this->lang->load('admin_skin');
	}

	public function index()
	{
 	        $ac = $this->input->get('ac',true);
 	        $op = $this->input->get('op',true);
 	        $page = intval($this->input->get('page'));
            if($page==0) $page=1;
			if($op!='home' && $op!='user' && $op!='mobile') $op='skins';

            if(empty($ac)){
			     $path=CSCMS.'tpl/'.$op.'/';
			}else{
				 if(!empty($op)){
			          $path=FCPATH.'plugins/'.$ac.'/tpl/'.$op.'/';
				 }else{
			          $path=FCPATH.'plugins/'.$ac.'/tpl/skins/';
				 }
			}

            $arrs=directory_map($path, 1);
            $per_page=10;
            $totalPages=ceil((count($arrs)-1) / $per_page); //总页数
            $base_url=site_url('skin').'?ac='.$ac.'&op='.$op;
            $dir_arr=array_slice($arrs,$per_page*(($page>$totalPages?$totalPages:$page)-1),$per_page);
            $dirs=array();
		    if ($dir_arr) {
			    foreach ($dir_arr as $t) {
				    if (is_dir($path.$t)) {
						$confiles=$path.$t.'/config.php';
						if (file_exists($confiles)){
							$config=require_once($confiles);
							if(empty($ac)){
								 if($op=='user'){
								      $plub['User_Dir'] = User_Skins;
								 }elseif($op=='home'){
								      $plub['Skins_Dir'] = Home_Skins;
								 }elseif($op=='mobile'){
								      $plub['Mobile_Dir'] = Mobile_Skins;
								 }else{
								      $plub['Skins_Dir'] = Web_Skins;
								 }
								 $pic = Web_Path.'cscms/tpl/'.$op.'/'.$t.'/preview.jpg';
							}else{
								 $plub = require FCPATH.'plugins/'.$ac.'/config/site.php';
								 $pdir = $op;
								 $pic = Web_Path.'plugins/'.$ac.'/tpl/'.$pdir.'/'.$t.'/preview.jpg';
							}
							$pics=(Web_Path!='/')?str_replace(Web_Path,'',$pic):$pic;
                            if (!file_exists(FCPATH.$pics)) $pic=Web_Path.'packs/images/skins.jpg';
							if($op=='mobile'){
							     $clas=($plub['Mobile_Dir']==$config['path'])?'selected':'';
							}elseif($op=='user'){
							     $clas=($plub['User_Dir']==$config['path'])?'selected':'';
							}elseif($op=='home'){
							     $clas=(Home_Skins==$config['path'])?'selected':'';
							}else{
							     $clas=($plub['Skins_Dir']==$config['path'])?'selected':'';
							}
					        $dirs[] = array(
						        'clas' => $clas,
						        'pic'  => $pic,
						        'name' => $config['name'],
						        'path' => $config['path'],
						        'mid'  => $config['mid'],
						        'author' => $config['author'],
						        'version' => $config['version'],
						        'description' => $config['description'],
						        'link' => site_url('skin/show')."?ac=".$ac."&op=".$op."&dirs=".$t,
						        'ulink' => site_url('skin/look')."?ac=".$ac."&op=".$op."&dirs=".$t,
						        'mrlink' => site_url('skin/init')."?ac=".$ac."&op=".$op."&dirs=".$t,
						        'dellink' => site_url('skin/del')."?ac=".$ac."&op=".$op."&dirs=".$t,
					        );
						}
				    }
			    }
		    }
	        $data['skins'] = $dirs;
	        $data['nums'] = count($arrs);
	        $data['page'] = $page;
	        $data['op'] = $op;
	        $data['ac'] = $ac;
			$data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类
			$data['uplink']=site_url('skin/upload')."?ac=".$ac."&op=".$op;
            $this->load->view('skin.html',$data);
	}

    //设置默认模板
	public function init()
	{
 	        $ac = $this->input->get('ac',true);
 	        $op = $this->input->get('op',true);
 	        $dir = $this->input->get('dirs',true);
			if($op!='home' && $op!='user' && $op!='mobile') $op='skins';

			if(empty($dir)) admin_msg(L('plub_01'),'javascript:history.back();','no');  //模板路径不能为空

            if(empty($ac)){
			     $confiles=CSCMS.'tpl/'.$op.'/'.$dir.'/config.php';
			}else{
				 if(!empty($op)){
			         $confiles=FCPATH.'plugins/'.$ac.'/tpl/'.$op.'/'.$dir.'/config.php';
				 }else{
			         $confiles=FCPATH.'plugins/'.$ac.'/tpl/skins/'.$dir.'/config.php';
				 }
			}
			if (file_exists($confiles)){
					$config=require_once($confiles);
			}else{
                   admin_msg(L('plub_02'),'javascript:history.back();','no');  //模板配置文件不存在
			}
			if(empty($config['path']) || $config['mid']==''){
                   admin_msg(L('plub_03'),'javascript:history.back();','no');  //模板配置文件不存在
			}
            
			//设置默认
            if(empty($ac)){
				 $this->load->helper('file');
				 if($op=='user'){
	                    $users=read_file("./cscms/lib/Cs_User.php");
	                    $users=preg_replace('/"User_Skins","(.*?)"/','"User_Skins","'.$config['path'].'"',$users);
	                    $res=write_file('./cscms/lib/Cs_User.php', $users);
				 }elseif($op=='home'){
	                    $users=read_file("./cscms/lib/Cs_Home.php");
	                    $users=preg_replace('/"Home_Skins","(.*?)"/','"Home_Skins","'.$config['path'].'"',$users);
	                    $res=write_file('./cscms/lib/Cs_Home.php', $users);
				 }else{
	                    $users=read_file("./cscms/lib/Cs_Config.php");
	                    $users=preg_replace('/"Web_Skins","(.*?)"/','"Web_Skins","'.$config['path'].'"',$users);
	                    $res=write_file('./cscms/lib/Cs_Config.php', $users);
				 }
			}else{
                 $arr=require FCPATH.'plugins/'.$ac.'/config/site.php';
				 if($op=='mobile'){
                      $arr['Mobile_Dir']=$config['path'];
				 }elseif($op=='user'){
                      $arr['User_Dir']=$config['path'];
				 }elseif($op=='home'){
                      $arr['Home_Dir']=$config['path'];
				 }else{
                      $arr['Skins_Dir']=$config['path'];
				 }
			     $res=arr_file_edit($arr,FCPATH.'plugins/'.$ac.'/config/site.php');
			}
			if($res){
                   admin_msg(L('tpl_04'),'javascript:history.back();');  //设置默认模板成功
			}else{
                   admin_msg(L('tpl_05'),'javascript:history.back();','no');  //设置默认模板失败
			}
	}

    //模板作者信息
	public function look()
	{
 	        $ac = $this->input->get('ac',true);
 	        $op = $this->input->get('op',true);
 	        $dir = $this->input->get('dirs',true);
			if($op!='home' && $op!='user' && $op!='mobile') $op='skins';

			if(empty($dir)) admin_msg(L('plub_01'),'###','no');  //模板路径不能为空

            if(empty($ac)){
			     $confiles=CSCMS.'tpl/'.$op.'/'.$dir.'/config.php';
			}else{
				 if(!empty($op)){
			         $confiles=FCPATH.'plugins/'.$ac.'/tpl/'.$op.'/'.$dir.'/config.php';
				 }else{
			         $confiles=FCPATH.'plugins/'.$ac.'/tpl/skins/'.$dir.'/config.php';
				 }
			}

			if (file_exists($confiles)){
					$config=require_once($confiles);
			}else{
                   admin_msg(L('plub_02'),'###','no');  //模板配置文件不存在
			}

	        $data['skin'] = $config;
	        $data['ac'] = $ac;
	        $data['op'] = $op;
	        $data['dir'] = $dir;
            $this->load->view('skin_look.html',$data);
	}

    //模板配置修改
	public function look_save()
	{
 	        $vip = intval($this->input->post('vip'));
 	        $level = intval($this->input->post('level'));
 	        $cion = intval($this->input->post('cion'));
 	        $name = $this->input->post('name',true);
 	        $dir = $this->input->post('dir',true);
			if(empty($dir)) admin_msg(L('tpl_06'),'javascript:history.back();','no');
            $confiles=CSCMS.'tpl/home/'.$dir.'/config.php';
			if (file_exists($confiles)){
					$skins=require_once($confiles);
			}else{
                   admin_msg(L('plub_02'),'###','no');  //模板配置文件不存在
			}
			$skins['vip']=$vip;
			$skins['level']=$level;
			$skins['cion']=$cion;
			$skins['name']=$name;

            //修改
			arr_file_edit($skins,$confiles);
			exit('<script type="text/javascript">
			alert("'.L('tpl_07').'");
			parent.location.href=parent.location.href;
			parent.tip_cokes();
			</script>');  //操作成功
	}

    //模板文件修改
	public function edit()
	{
 	        $ac = $this->input->get('ac',true);
 	        $op = $this->input->get('op',true);
 	        $do = $this->input->get('do',true);
 	        $dir = $this->input->get('dirs',true);
 	        $file = $this->input->get('file');
			$exts = strtolower(trim(strrchr($file, '.'), '.'));
			if($op!='home' && $op!='user' && $op!='mobile') $op='skins';

			if(empty($dir) || empty($file)) admin_msg(L('plub_01'),'javascript:history.back();','no'); 
			if($exts!='html' && $exts!='css' && $exts!='js') admin_msg(L('tpl_08'),'javascript:history.back();','no'); 
            if(empty($ac)){
			     $skin_dir=CSCMS.'tpl/'.$op.'/'.$dir.'/';
			}else{
				 if(!empty($op)){
			         $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/'.$op.'/'.$dir.'/';
				 }else{
			         $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/skins/'.$dir.'/';
				 }
			}
			$skin_dir.=$file;
			$skin_dir=str_replace("//","/",$skin_dir);

	        if(!file_exists($skin_dir))  admin_msg(L('tpl_09'),'javascript:history.back();','no');

            $this->load->helper('file');

            if($do=='add'){
 	             $html = $this->input->post('html');
                 //写文件
                 if (!write_file($skin_dir, $html)){
                      admin_msg(L('tpl_10'),'javascript:history.back();','no');
                 }else{
					  $parr=explode('/',$file);
					  $path='';
                      for($j=0;$j<count($parr)-1;$j++){	
                            $path.=$parr[$j].'/';
					  }
			          if(substr($path,-1)=='/') $path=substr($path,0,-1);
                      admin_msg(L('tpl_11'),site_url('skin/show')."?ac=".$ac."&op=".$op."&dirs=".$dir."&path=".$path);
                 }
			}else{
                 $html=get_bm(read_file($skin_dir));
			     $data['savelink']=site_url('skin/edit')."?ac=".$ac."&do=add&op=".$op."&dirs=".$dir."&file=".$file;
			     $data['path']=empty($ac)?str_replace(CSCMS.'tpl','',$skin_dir):str_replace(FCPATH.'plugins/'.$ac,'',$skin_dir);
			     $data['html']=str_replace('</textarea>','&lt;/textarea&gt;',$html);

                 $this->load->view('skin_edit.html',$data);
			}
	}

    //模板文件浏览
	public function show()
	{
 	        $ac = $this->input->get('ac',true);
 	        $op = $this->input->get('op',true);
 	        $dir = $this->input->get('dirs',true);
 	        $path = $this->input->get('path');
			if($op!='home' && $op!='user' && $op!='mobile') $op='skins';

			if(empty($dir)) admin_msg(L('plub_01'),'javascript:history.back();','no');  //模板路径不能为空

            if(empty($ac)){
			     $skin_dir=CSCMS.'tpl/'.$op.'/'.$dir.'/';
			}else{
				 if(!empty($op)){
			         $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/'.$op.'/'.$dir.'/';
				 }else{
			         $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/skins/'.$dir.'/';
				 }
			}

			//模板文件说明
			$skinfiles=$skin_dir.'skins.php';
			if (file_exists($skinfiles)){
					$skin_arr=require_once($skinfiles);
			}else{
                    $skin_arr=array();
			}

			$skin_dir=(!empty($path))?$skin_dir.$path.'/':$skin_dir;
			$skin_dir=str_replace("//","/",$skin_dir);

			if (!is_dir($skin_dir)) {
                  admin_msg(L('tpl_12'),'javascript:history.back();','no');  //模板路径不能为空
			}

            $this->load->helper('file');
			$showarr=get_dir_file_info($skin_dir, $top_level_only = TRUE);
            $dirs=$list=array();
		    if ($showarr) {
			    foreach ($showarr as $t) {
				    if (is_dir($t['server_path'])) {
						if($t['name']!='install'){
					        $dirs[] = array(
						       'name' => $t['name'],
						       'date' => date('Y-m-d H:i:s',$t['date']),
						       'size' => '--',
						       'icon' => Web_Path.'packs/admin/images/ext/dir.gif',
						       'link' => site_url('skin/show')."?ac=".$ac."&op=".$op."&dirs=".$dir."&path=".$path."/".$t['name'],
						       'dellink' => site_url('skin/del')."?ac=".$ac."&op=".$op."&dirs=".$dir."&file=".$path."/".$t['name'],
					        );
						}
				    } else {
						$exts = strtolower(trim(strrchr($t['name'], '.'), '.'));
						if($exts=='css'){
							$title=L('tpl_13');
						}elseif($exts=='js'){
							$title=L('tpl_14');
						}else{
							if($op=='mobile'){
                                if(strpos($path.'/','/user/') !== FALSE){
                                     $skin_f=(empty($skin_arr['user']))?array():$skin_arr['user'];
							    }elseif(strpos($path.'/','/home/') !== FALSE){
                                     $skin_f=(empty($skin_arr['home']))?array():$skin_arr['home'];
							    }else{
                                     $skin_f=(empty($skin_arr['index']))?array():$skin_arr['index'];
							    }
							}else{
                                $skin_f=$skin_arr;
							}
						    $title=(!arr_key_value($skin_f,$t['name']))?L('tpl_15'):arr_key_value($skin_f,$t['name']);
						}
						if($exts=='html' || $exts=='css' || $exts=='js'){
							$times=date('Y-m-d H:i:s',$t['date']);
						    $list[] = array(
							    'name' => $t['name'],
								'title'=> $title,
							    'ext' => get_extpic($exts),
						        'date' => (date('Y-m-d',$t['date'])==date('Y-m-d'))?'<font color=red>'.$times.'<font>':$times,
						        'size' => formatsize($t['size']),
							    'icon' => Web_Path.'packs/admin/images/ext/'.get_extpic($exts).'.gif',
							    'link' => site_url('skin/edit')."?ac=".$ac."&op=".$op."&dirs=".$dir."&file=".$path."/".$t['name'],
							    'blink' => site_url('skin/copyt')."?ac=".$ac."&op=".$op."&dirs=".$dir."&file=".$path."/".$t['name'],
						        'dellink' => site_url('skin/del')."?ac=".$ac."&op=".$op."&dirs=".$dir."&file=".$path."/".$t['name'],
						    );
						}
				    }
			    }
		    }
			$data['addlink']=site_url('skin/add')."?ac=".$ac."&op=".$op."&dirs=".$dir.$path;
			$data['path']=empty($ac)?str_replace(CSCMS.'tpl','',$skin_dir):str_replace(FCPATH.'plugins/'.$ac,'',$skin_dir);
			$data['dirs']=$dirs;
			$data['show']=$list;
			$data['uplink']=site_url('skin/upload')."?ac=".$ac."&op=".$op;

			$parr=explode('/',$path);
			$spath='';
            for($j=0;$j<count($parr)-1;$j++){	
                 $spath.=$parr[$j].'/';
			}
			if(substr($spath,-1)=='/') $spath=substr($spath,0,-1);
			$data['slink']=empty($path)?site_url('skin')."?ac=".$ac."&op=".$op:site_url('skin/show')."?ac=".$ac."&op=".$op."&dirs=".$dir."&path=".$spath;

            $this->load->view('skin_show.html',$data);
	}

    //新增
	public function add()
	{
 	        $ac = $this->input->get('ac',true);
 	        $op = $this->input->get('op',true);
 	        $do = $this->input->get('do',true);
 	        $dir = $this->input->get('dirs',true);
 	        $file = $this->input->post('file',true);
			if($op!='home' && $op!='user' && $op!='mobile') $op='skins';

			if(empty($dir)) admin_msg(L('plub_01'),'javascript:history.back();','no');  //模板路径不能为空

            if(empty($ac)){
			     $skin_dir=CSCMS.'tpl/'.$op.'/'.$dir.'/';
			}else{
				 if(!empty($op)){
			         $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/'.$op.'/'.$dir.'/';
				 }else{
			         $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/skins/'.$dir.'/';
				 }
			}
			$skin_dir=str_replace("//","/",$skin_dir);

			if (!is_dir($skin_dir)) {
                  admin_msg(L('plub_12'),'javascript:history.back();','no');  //模板路径不能为空
			}
			if($do=='add'){
				if(empty($file)) exit('<script>alert("'.L('plub_16').'");javascript:history.back();</script>');
				$path=$skin_dir.$file;
                $exts = strtolower(trim(strrchr($file, '.'), '.'));
				if($exts=='html' || $exts=='js' || $exts=='css'){ //增加文件
                       if(!write_file($path, ' ')){
							echo '<script type="text/javascript">alert("'.L('plub_17').'");top.web_box(2);tparent.document.location.reload();</script>';
					   }else{
                            echo '<script type="text/javascript">top.web_box(2);parent.document.location.reload();</script>';
					   }
				}else{  //增加目录
                       if(!mkdirss(str_replace(".","",$path), ' ')){
							echo '<script type="text/javascript">alert("'.L('plub_18').'");top.web_box(2);parent.document.location.reload();</script>';
					   }else{
                            echo '<script type="text/javascript">top.web_box(2);parent.document.location.reload();</script>';
					   }
				}
                exit;
			}else{
                $data['savelink']=site_url('skin/add')."?ac=".$ac."&op=".$op."&do=add&dirs=".$dir;
                $this->load->view('skin_add.html',$data);
			}
	}

    //文件备份
	public function copyt()
	{
 	        $ac = $this->input->get('ac',true);
 	        $op = $this->input->get('op',true);
 	        $dir = $this->input->get('dirs',true);
 	        $file = $this->input->get('file');
			if($op!='home' && $op!='user' && $op!='mobile') $op='skins';

			if(empty($dir) || empty($file)) admin_msg(L('plub_01'),'javascript:history.back();','no');  //路径不能为空

            if(empty($ac)){
			     $skin_dir=CSCMS.'tpl/'.$op.'/'.$dir.'/'.$file;
			}else{
				 if(!empty($op)){
			         $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/'.$op.'/'.$dir.'/'.$file;
				 }else{
			         $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/skins/'.$dir.'/'.$file;
				 }
			}

			$old_skin_dir=str_replace("//","/",$skin_dir);
			$exts = '.'.strtolower(trim(strrchr($skin_dir, '.'), '.'));
			$new_skin_dir=str_replace($exts,' - '.L('plub_19').$exts,$old_skin_dir);

			if(copy($old_skin_dir,$new_skin_dir)){
                   admin_msg(L('plub_20'),'javascript:history.back();','no');
			}else{
                   admin_msg(L('plub_21'),'javascript:history.back();','no');  //模板路径错误
			}
	}

    //文件删除
	public function del()
	{
 	        $ac = $this->input->get('ac',true);
 	        $op = $this->input->get('op',true);
 	        $dir = $this->input->get('dirs',true);
 	        $file = $this->input->get('file');
			if($op!='home' && $op!='user' && $op!='mobile') $op='skins';

			if(empty($dir)) admin_msg(L('plub_01'),'javascript:history.back();','no');  //路径不能为空

            if(empty($ac)){
			     $skin_dir=CSCMS.'tpl/'.$op.'/'.$dir.'/'.$file;
			}else{
				 if(!empty($op)){
			         $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/'.$op.'/'.$dir.'/'.$file;
				 }else{
			         $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/skins/'.$dir.'/'.$file;
				 }
			}

			$skin_dir=str_replace("//","/",$skin_dir);

			if (!is_dir($skin_dir)) {  //文件
                  $res=unlink($skin_dir);
			}else{  //目录
                  $res=deldir($skin_dir);
			}

			if($res){
                   admin_msg(L('plub_22'),'javascript:history.back();');
			}else{
                   admin_msg(L('plub_23'),'javascript:history.back();','no');  //模板路径错误
			}
	}

    //模板压缩包上传
	public function upload()
	{
 	        $ac = $this->input->get('ac',true);
 	        $op = $this->input->get('op',true);
 	        $do = $this->input->get('do',true);
 	        $dir = $this->input->get('dirs',true);
			if($op!='home' && $op!='user' && $op!='mobile') $op='skins';

			if($do=='add'){
                 $config['upload_path'] = FCPATH.'attachment/other/';
                 $config['allowed_types'] = 'zip';
                 $config['encrypt_name'] = TRUE;  //重命名
				 $this->load->library('upload', $config);
                 if ( ! $this->upload->do_upload()){
                            $error = array('error' => $this->upload->display_errors('<b>','</b>'));
							admin_msg(L('plub_24').$error['error'],'javascript:history.back();','no');
                 } else{
                            $data = $this->upload->data();
							$filename=$data['file_name'];
            				if(empty($ac)){
			     				$skin_dir=CSCMS.'tpl/'.$op.'/';
							}else{
				 				if(!empty($op)){
			     				     $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/'.$op.'/';
				 				}else{
			        				 $skin_dir=FCPATH.'plugins/'.$ac.'/tpl/skins/';
				 				}
							}

							//解压模板
	                        $this->load->library('cszip');
		                    $this->cszip->PclZip($config['upload_path'].$filename);
							if ($this->cszip->extract(PCLZIP_OPT_PATH, $skin_dir, PCLZIP_OPT_REPLACE_NEWER) == 0) {
								    //删除压缩包
									@unlink($config['upload_path'].$filename);
                                    admin_msg(L('plub_25'),'javascript:history.back();','no');
							}else{
								    //删除压缩包
									@unlink($config['upload_path'].$filename);
                                    admin_msg(L('plub_26'),site_url('skin')."?ac=".$ac."&op=".$op);
							}
				 }

			}else{
				 $data['savelink']=site_url('skin/upload')."?ac=".$ac."&op=".$op."&do=add";
                 $this->load->view('skin_upload.html',$data);
			}
	}

    //云平台
	public function yun()
	{
			header("Location: ".$this->csapp->url('skins')."");
	}

    //下载模板
	public function down()
	{
            $mold=$this->input->get_post('mold');
            $dir=$this->input->get_post('dir');
            $mid=(int)$this->input->get_post('mid');
            $sid=intval($this->input->get_post('sid'));
            $fid=intval($this->input->get_post('fid'));
            $key=$this->input->get_post('key');
	        if (empty($key) || empty($dir) || empty($mid) || empty($sid)){
				admin_msg('<font color=red>'.L('plub_27').'</font>',site_url('skin/yun'),'no');
			}
			if($fid==1){  //板块模板
                if($sid==4){ //手机模板
                      $skins_path=FCPATH.'plugins/'.$mold.'/tpl/mobile/';
                }elseif($sid==3){ //会员中心模板
                      $skins_path=FCPATH.'plugins/'.$mold.'/tpl/user/';
                }elseif($sid==2){  //会员主页模板
	                  $skins_path=FCPATH.'plugins/'.$mold.'/tpl/home/';
                }else{   //单主页模板
	                  $skins_path=FCPATH.'plugins/'.$mold.'/tpl/skins/';
                }
			}else{
                if($sid==4){ //手机模板
                      $skins_path=CSCMS.'tpl/mobile/';
                }elseif($sid==3){ //会员中心模板
                      $skins_path=CSCMS.'tpl/user/';
                }elseif($sid==2){  //会员主页模板
	                  $skins_path=CSCMS.'tpl/home/';
                }else{   //单主页模板
	                  $skins_path=CSCMS.'tpl/skins/';
                }
			}

            $data['key']=$key;
            $zip=$this->csapp->url('skins/down/'.$mid,$data);
            $zippath=FCPATH."attachment/other/skins_".$dir.".zip";
            $files_file=$this->csapp->down($zip,$zippath);
    	    if($files_file=='-1') admin_msg(L('plub_28'),site_url('skin/yun'),'no');
    	    if($files_file=='-2') admin_msg(L('plub_29'),site_url('skin/yun'),'no');
    	    if($files_file=='-3') admin_msg(L('plub_30'),site_url('skin/yun'),'no');
    	    if($files_file=='10001') admin_msg(L('plub_31'),site_url('skin/yun'),'no');
    	    if($files_file=='10002') admin_msg(L('plub_32'),site_url('skin/yun'),'no');
	        if(filesize($zippath) == 0) admin_msg(L('plub_31'),site_url('skin/yun'),'no');

		    //解压缩
	        $this->load->library('cszip');
		    $this->cszip->PclZip($zippath);
		    if ($this->cszip->extract(PCLZIP_OPT_PATH, $skins_path, PCLZIP_OPT_REPLACE_NEWER) == 0) {
                    	@unlink($zippath);
                        admin_msg(L('plub_33'),site_url('skin/yun'),'no');
		    }else{

		               if (!file_exists($skins_path.$dir.'/config.php')) {
			                @unlink($zippath);
                            admin_msg(L('plub_34'),site_url('skin/yun'),'no');
					   }
			           @unlink($zippath);
                       admin_msg(L('plub_35'),site_url('skin'));
			}
	}

    //标签向导
	public function tags()
	{      
		    $data['ac']='';
            $data['dir']=$this->input->get('dir',true);
			$this->load->view('skin_tags.html',$data);
	}

    //生成标签代码
	public function tags_save()
	{      
            $data['mx']=$this->input->get('mx');
            $data['loop']=$this->input->get('loop');
            $data['order']=$this->input->get('order');
			if(empty($data['loop'])) $data['loop']='20';
		    $data['ac']='save';

            //获得当前模型字段信息
		    $query = $this->db->query("SHOW FULL FIELDS FROM ".CS_SqlPrefix.$data['mx']."");
		    $data['ziduan'] = $query->result_array();
			$this->load->view('skin_tags.html',$data);
	}

	//在线升级
	public function update()
	{
            $mold=$this->input->get_post('mold');
            $dir=$this->input->get_post('dir');
            $mid=(int)$this->input->get_post('mid');
            $key=$this->input->get_post('key');
            $sid=intval($this->input->get_post('sid'));
            $fid=intval($this->input->get_post('fid'));

			if($fid==1){  //板块模板
                if($sid==4){ //手机模板
                      $skins_path=FCPATH.'plugins/'.$mold.'/tpl/mobile/';
                }elseif($sid==3){ //会员中心模板
                      $skins_path=FCPATH.'plugins/'.$mold.'/tpl/user/';
                }elseif($sid==2){  //会员主页模板
	                  $skins_path=FCPATH.'plugins/'.$mold.'/tpl/home/';
                }else{   //单主页模板
	                  $skins_path=FCPATH.'plugins/'.$mold.'/tpl/skins/';
                }
			}else{
                if($sid==4){ //手机模板
                      $skins_path=CSCMS.'tpl/mobile/';
                }elseif($sid==3){ //会员中心模板
                      $skins_path=CSCMS.'tpl/user/';
                }elseif($sid==2){  //会员主页模板
	                  $skins_path=CSCMS.'tpl/home/';
                }else{   //单主页模板
	                  $skins_path=CSCMS.'tpl/skins/';
                }
			}

	        if (empty($key) || empty($dir)){

                  $data['mid']=$mid;
                  header("Location: ".$this->csapp->url('skins/update',$data)."");

			}else{  //下载

                  $data['key']=$key;
                  $zip=$this->csapp->url('skins/update/'.$mid,$data);
                  $zippath=FCPATH."attachment/other/skins_".$dir."_update.zip";
                  $files_file=$this->csapp->down($zip,$zippath);
    	          if($files_file=='-1') admin_msg(L('plub_28'),site_url('skin/yun'),'no');
    	          if($files_file=='-2') admin_msg(L('plub_29'),site_url('skin/yun'),'no');
    	          if($files_file=='-3') admin_msg(L('plub_30'),site_url('skin/yun'),'no');
    	          if($files_file=='10001') admin_msg(L('plub_36'),site_url('skin/yun'),'no');
    	          if($files_file=='10002') admin_msg(L('plub_37'),site_url('skin/yun'),'no');
    	          if($files_file=='10003') admin_msg(L('plub_38'),site_url('skin/yun'),'no');
	              if(filesize($zippath) == 0) admin_msg(L('plub_39'),site_url('skin/yun'),'no');


				  //先备份原始板块
	              $this->load->library('cszip');
				  $zip_path=FCPATH."attachment/other/skins_".$dir."_backup_".date('Ymd').".zip";
				  $plub_path = $skins_path.$dir; 
		          $this->cszip->PclZip($zip_path); //创建压缩包
                  $this->cszip->create($plub_path); //增加目录
		          //解压缩
		          $this->cszip->PclZip($zippath);
				  //尝试解压覆盖
		          if ($this->cszip->extract(PCLZIP_OPT_PATH, $plub_path, PCLZIP_OPT_REPLACE_NEWER) == 0) {
                       die(vsprintf(L('plub_40'),array($plub_path)).$zippath);
		          }else{
			           @unlink($zippath);
                       admin_msg(L('plub_41'),site_url('skin'));
				  }
			}
	}
}
