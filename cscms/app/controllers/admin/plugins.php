<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2008-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-09-11
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Plugins extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
			$this->load->helper('file');
			$this->lang->load('admin_plugins');
			$this->load->library('csapp');
	}

	//获取版块目录
	public function index()
	{
 	        $page = intval($this->input->get('page'));
            if($page==0) $page=1;
 	        $arrs = get_dir_file_info("./plugins/");
            $data['pagesize']=10;
            $data['page']=$page;
            $data['pagejs']=ceil((count($arrs)-1) / $data['pagesize']); //总页数
            $data['url']=site_url('admin/plugins')."?";
            $data['dirs']=array_slice($arrs,$data['pagesize']*(($page>$data['pagejs']?$data['pagejs']:$page)-1),$data['pagesize']);
            $this->load->view('plugins.html',$data);
	}

	//配置
	public function setting()
	{
 	        $dir = $this->input->get('dir',true);
			$data['SITE'] = require_once(FCPATH.'plugins/'.$dir.'/config/site.php');
		    $data['model']= require_once(FCPATH.'plugins/'.$dir.'/config/setting.php');
		    $data['dir']=$dir;
            $row=$this->db->query("SELECT ak,name FROM ".CS_SqlPrefix."plugins where dir='".$dir."'")->row();
			$arrs=unarraystring(sys_auth($row->ak,'D'));
		    $data['key']=(empty($arrs) || empty($arrs['key']) || $arrs['key']=='0')?'':$arrs['key'];
            if($data['model']['name']!=$row->name) $data['model']['name']=$row->name;
            //获取所有模板
			$this->load->helper('directory');
            $path=FCPATH.'plugins/'.$dir.'/tpl/skins/';
            $dir_arr=directory_map($path, 1);
            $dirs=array();
		    if ($dir_arr) {
			    foreach ($dir_arr as $t) {
				    if (is_dir($path.$t)) {
						$confiles=$path.$t.'/config.php';
						if (file_exists($confiles)){
							$config=require_once($confiles);
					        $dirs[] = array(
						        'name' => $config['name'],
						        'path' => $config['path'],
					        );
						}
				    }
			    }
		    }
	        $data['skins'] = $dirs;

            //获取所有会员中心模板
            $path=FCPATH.'plugins/'.$dir.'/tpl/user/';
            $dir_arr=directory_map($path, 1);
            $mobiledirs=array();
		    if ($dir_arr) {
			    foreach ($dir_arr as $t) {
				    if (is_dir($path.$t)) {
						$confiles=$path.$t.'/config.php';
						if (file_exists($confiles)){
							$config=require_once($confiles);
					        $mobiledirs[] = array(
						        'name' => $config['name'],
						        'path' => $config['path'],
					        );
						}
				    }
			    }
		    }
	        $data['user_skins'] = $mobiledirs;

            //获取所有手机模板
            $path=FCPATH.'plugins/'.$dir.'/tpl/mobile/';
            $dir_arr=directory_map($path, 1);
            $mobiledirs=array();
		    if ($dir_arr) {
			    foreach ($dir_arr as $t) {
				    if (is_dir($path.$t)) {
						$confiles=$path.$t.'/config.php';
						if (file_exists($confiles)){
							$config=require_once($confiles);
					        $mobiledirs[] = array(
						        'name' => $config['name'],
						        'path' => $config['path'],
					        );
						}
				    }
			    }
		    }
	        $data['mobile_skins'] = $mobiledirs;
            $this->load->view('plugins_setting.html',$data);
	}

	//配置保存
	public function setting_save()
	{
 	        $name = $this->input->post('name',true);
 	        $dir = $this->input->post('dir',true);
 	        $Web_Mode = intval($this->input->post('Web_Mode',true));
			$Mobile_Is = intval($this->input->post('Mobile_Is',true));
 	        $Skins_Dir = $this->input->post('Skins_Dir',true);
 	        $User_Dir = $this->input->post('User_Dir',true);
 	        $Mobile_Dir = $this->input->post('Mobile_Dir',true);
 	        $Ym_Mode = intval($this->input->post('Ym_Mode',true));
 	        $Cache_Is = intval($this->input->post('Cache_Is',true));
 	        $Cache_Time = intval($this->input->post('Cache_Time',true));
 	        $Ym_Url = $this->input->post('Ym_Url',true);
 	        $User_Qx = $this->input->post('user',true);
 	        $User_Dj_Qx = $this->input->post('user_dj',true);
 	        $rewrite = $this->input->post('rewrite',true);
 	        $html = $this->input->post('html',true);
 	        $seo = $this->input->post('seo',true);
 	        $key = $this->input->post('key',true);
			if($Web_Mode==0) $Web_Mode=1;
			if($Cache_Time==0) $Cache_Time=1800;

			if(empty($Skins_Dir)) admin_msg(L('plub_01'),'javascript:history.back();','no');  //模板路径不能为空
			if($Ym_Mode>0 && empty($Ym_Url)) admin_msg(L('plub_02'),'javascript:history.back();','no'); //模板路径不能为空

            $row=$this->db->query("SELECT ak,name FROM ".CS_SqlPrefix."plugins where dir='".$dir."'")->row();
			if(!empty($name) && $name!=$row->name){
                  $this->db->query("update ".CS_SqlPrefix."plugins set name='".$name."' where dir='".$dir."'");
			}
			$arrs=unarraystring(sys_auth($row->ak,'D'));
			if(empty($key)) $key='0';
            if(empty($arrs) || empty($arrs['md5']) || $key!=$arrs['key']){
                     $app['dir']=$dir;
                     $app['key']=$key;
			         $ak=$this->csapp->keys($app);
			         if(empty($ak)){
                            admin_msg(L('plub_03'),'javascript:history.back();','no');
			         }elseif($ak=='0'){
                            admin_msg(L('plub_04'),'javascript:history.back();','no');
				     }
                     $this->db->query("update ".CS_SqlPrefix."plugins set ak='".$ak."' where dir='".$dir."'");
			}

            if(is_dir(FCPATH.'plugins/'.$dir)){

                 $data['Web_Mode']=$Web_Mode;
                 $data['Skins_Dir']=$Skins_Dir;
                 $data['User_Dir']=$User_Dir;
                 $data['Mobile_Dir']=$Mobile_Dir;
				 $data['Mobile_Is']=$Mobile_Is;
                 $data['Cache_Is']=$Cache_Is;
                 $data['Cache_Time']=$Cache_Time;
                 $data['Ym_Mode']=$Ym_Mode;
                 $data['Ym_Url']=$Ym_Url;
                 $data['User_Qx']=empty($User_Qx)?'':implode(',', $User_Qx);
                 $data['User_Dj_Qx']=empty($User_Dj_Qx)?'':implode(',', $User_Dj_Qx);
				 $data['Rewrite_Uri']=$rewrite;
				 $data['Html_Uri']=$html;
				 $data['Seo']=$seo;

				 //判断开启二级域名
				 global $_CS_Domain;
				 if($Ym_Mode==1){
                        $_CS_Domain[$dir]=$Ym_Url;
                        arr_file_edit($_CS_Domain);
				 }else{
						if(arr_key_value($_CS_Domain,$dir)){
                               unset($_CS_Domain[$dir]);
                               arr_file_edit($_CS_Domain);
						}
				 }

				 //伪静态模式，写入URL路由
				 if($Web_Mode==2){
			            foreach ($rewrite as $key => $val) {
							list($preg, $value) = $this->_rule_preg_value($rewrite[$key]['url']);

                            if (!$preg || !$value) {
                                  $preg=$rewrite[$key]['url'];
								  $rewrite_uri=$rewrite[$key]['uri'];
                            }else{
								  $rewrite_uri=$rewrite[$key]['uri'];
								  if (!empty($value['{ji}'])){ $rewrite_uri=str_replace("{ji}",'$'.$value['{ji}'],$rewrite_uri);}
								  if (!empty($value['{zu}'])){ $rewrite_uri=str_replace("{zu}",'$'.$value['{zu}'],$rewrite_uri);}
								  if (!empty($value['{id}'])){ $rewrite_uri=str_replace("{id}",'$'.$value['{id}'],$rewrite_uri);}
								  if (!empty($value['{page}'])){ $rewrite_uri=str_replace("{page}",'$'.$value['{page}'],$rewrite_uri);}
								  if (!empty($value['{sort}'])){ $rewrite_uri=str_replace("{sort}",'$'.$value['{sort}'],$rewrite_uri);}
							}
                            $_data[$preg] = $rewrite_uri;
							$_note[$preg]['name'] = $rewrite[$key]['title'];
							$_note[$preg]['url'] = $rewrite[$key]['url'];
			            }
						$this->_route_file(FCPATH.'plugins/'.$dir.'/config/rewrite.php', $_data, $_note, $dir);
				 }else{
						$this->_route_file(FCPATH.'plugins/'.$dir.'/config/rewrite.php');
				 }
                 arr_file_edit($data,FCPATH.'plugins/'.$dir.'/config/site.php');
                 admin_msg(L('plub_05'),site_url('plugins'),'ok');  //修改完成

			}else{
                 admin_msg(L('plub_06'),site_url('plugins'),'no');  //板块不存在
			}
	}

	//安装
	public function install()
	{
 	        $dir = $this->input->get_post('dir',true);
            if(is_dir(FCPATH.'plugins/'.$dir)){
		         $model=require_once(FCPATH.'plugins/'.$dir.'/config/setting.php');
				 $SQLDB = require_once(FCPATH.'plugins/'.$dir.'/config/install.php');
				 if (!file_exists(FCPATH.'plugins/'.$dir.'/config/install.php') || $model['mid']=='') admin_msg(L('plub_07'),'javascript:history.back();');
                 $row=$this->db->query("SELECT id FROM ".CS_SqlPrefix."plugins where dir='".$dir."'")->row(); 
                 if($row){
                      admin_msg(L('plub_08'),site_url('plugins'),'no');  //板块已经安装
				 }else{

					  //判断数据库
					  if(is_array($SQLDB)){
	                       foreach ($SQLDB as $sql) {
	                           $this->CsdjDB->get_table(str_replace('{prefix}', CS_SqlPrefix, $sql));
	                       }
					  }else{
                           $this->CsdjDB->get_table(str_replace('{prefix}', CS_SqlPrefix, $SQLDB));
					  }

                      $add['dir']=$dir;
                      $add['name']=$model['name'];
                      $add['author']=$model['author'];
                      $add['version']=$model['version'];
                      $add['description']=$model['description'];
					  $this->CsdjDB->get_insert('plugins',$add);

                      $data['dir']=$dir;
                      $data['mid']=$model['mid'];
					  $installurl=$this->csapp->url('plub/installs',$data);
            		  echo '<script src="'.$installurl.'"></script>';
            		  admin_msg(L('plub_09'),site_url('plugins'),'ok'); //板块安装成功
		         }
			}else{
                 admin_msg(L('plub_06'),site_url('plugins'),'no');  //板块不存在
			}	 
	}

	//卸载
	public function uninstall()
	{
 	        $dir = $this->input->get('dir',true);
            if(is_dir(FCPATH.'plugins/'.$dir)){
                 $row=$this->db->query("SELECT id FROM ".CS_SqlPrefix."plugins where dir='".$dir."'")->row(); 
                 if(!$row){
                      admin_msg(L('plub_10'),site_url('plugins'),'no');  //板块还没有安装
				 }else{
					  if (is_file(FCPATH.'plugins/'.$dir.'/config/uninstall.php')) {
				            $SQLDB = require_once(FCPATH.'plugins/'.$dir.'/config/uninstall.php');
					  }else{
				            $SQLDB = '';
					  }
					  if (is_file(FCPATH.'plugins/'.$dir.'/config/setting.php')) {
				            $model = require_once(FCPATH.'plugins/'.$dir.'/config/setting.php');
					  }else{
				            $model['mid'] = 0;
					  }
					  //判断数据库
					  if(is_array($SQLDB)){
	                       foreach ($SQLDB as $sql) {
	                           $this->CsdjDB->get_table(str_replace('{prefix}', CS_SqlPrefix, $sql));
	                       }
					  }else{
                           $this->CsdjDB->get_table(str_replace('{prefix}', CS_SqlPrefix, $SQLDB));
					  }
					  //删除数据库记录
                      $this->db->query("delete from ".CS_SqlPrefix."plugins where dir='".$dir."'");

                      $data['dir']=$dir;
                      $data['mid']=$model['mid'];
					  $uninstallurl=$this->csapp->url('plub/uninstall',$data);
            		  echo '<script src="'.$uninstallurl.'"></script>';
            		  admin_msg(L('plub_11'),site_url('plugins'),'ok'); //板块卸载成功

		         }
			}else{
                 admin_msg(L('plub_06'),site_url('plugins'),'no');  //板块不存在
			}	
	}

	//清空数据库
	public function clear()
	{
 	        $dir = $this->input->get('dir',true);
            if(is_dir(FCPATH.'plugins/'.$dir)){

                 $row=$this->db->query("SELECT id FROM ".CS_SqlPrefix."plugins where dir='".$dir."'")->row(); 
                 if(!$row){
                      admin_msg(L('plub_10'),site_url('plugins'),'no');  //板块还没有安装
				 }else{

					  //先删除数据表
					  if (is_file(FCPATH.'plugins/'.$dir.'/config/uninstall.php')) {
				            $SQLDB1 = require_once(FCPATH.'plugins/'.$dir.'/config/uninstall.php');
					  }else{
				            $SQLDB1 = '';
					  }
					  if(is_array($SQLDB1)){
	                       foreach ($SQLDB1 as $sql) {
	                           $this->CsdjDB->get_table(str_replace('{prefix}', CS_SqlPrefix, $sql));
	                       }
					  }else{
                           $this->CsdjDB->get_table(str_replace('{prefix}', CS_SqlPrefix, $SQLDB1));
					  }

					  //在重新安装数据表
					  if (is_file(FCPATH.'plugins/'.$dir.'/config/install.php')) {
				            $SQLDB2 = require_once(FCPATH.'plugins/'.$dir.'/config/install.php');
					  }else{
				            $SQLDB2 = '';
					  }
					  if(is_array($SQLDB2)){
	                       foreach ($SQLDB2 as $sql) {
	                           $this->CsdjDB->get_table(str_replace('{prefix}', CS_SqlPrefix, $sql));
	                       }
					  }else{
                           $this->CsdjDB->get_table(str_replace('{prefix}', CS_SqlPrefix, $SQLDB2));
					  }
            		  admin_msg(L('plub_12'),site_url('plugins'),'ok'); //版块数据清空成功
		         }
			}else{
                 admin_msg(L('plub_06'),site_url('plugins'),'no');  //板块不存在
			}
	}

	//删除
	public function del()
	{
 	        $dir = $this->input->get('dir',true);
            deldir(FCPATH.'plugins/'.$dir);
            admin_msg(L('plub_13'),site_url('plugins'),'ok'); //版块删除成功
	}

    //云平台
	public function yun()
	{
			header("Location: ".$this->csapp->url('plub')."");
	}

	//在线安装
	public function down()
	{
            $dir=$this->input->get_post('dir');
            $mid=$this->input->get_post('mid');
            $key=$this->input->get_post('key');

	        if (empty($mid) || empty($dir)){

                  admin_msg(L('plub_14'),site_url('plugins'),'no');

			}else{  //下载
                  $data['key']=$key;
                  $zip=$this->csapp->url('plub/down/'.$mid,$data);
                  $zippath=FCPATH."attachment/other/plugins_".$dir.".zip";
                  $files_file=$this->csapp->down($zip,$zippath);
    	          if($files_file=='-1') admin_msg(L('plub_15'),site_url('plugins'),'no');
    	          if($files_file=='-2') admin_msg(L('plub_16'),site_url('plugins'),'no');
    	          if($files_file=='-3') admin_msg(L('plub_17'),site_url('plugins'),'no');
    	          if($files_file=='10001') admin_msg(L('plub_18'),site_url('plugins'),'no');
    	          if($files_file=='10002') admin_msg(L('plub_19'),site_url('plugins'),'no');
	              if(filesize($zippath) == 0) admin_msg(L('plub_20'),site_url('plugins'),'no');

		          //解压缩
	              $this->load->library('cszip');
				  $plub_path = FCPATH."plugins/"; 
		          $this->cszip->PclZip($zippath);
				  //尝试解压覆盖
		          if ($this->cszip->extract(PCLZIP_OPT_PATH, $plub_path, PCLZIP_OPT_REPLACE_NEWER) == 0) {
                       die(L('plub_21').$zippath);
		          }else{
		               if (!file_exists($plub_path.$dir.'/config/setting.php')) {
			                @unlink($zippath);
							deldir($plub_path.$dir);
                            admin_msg(L('plub_22'),site_url('plugins'),'no');
					   }
			           @unlink($zippath);
                       admin_msg(L('plub_23'),site_url('plugins'));
				  }
			}
	}

	//在线升级
	public function update()
	{
            $mold=$this->input->get_post('mold');
            $dir=$this->input->get_post('dir');
            $markid=$this->input->get_post('mid');
            $key=$this->input->get_post('key');

	        if (empty($key) || empty($dir)){

                  $data['mid']=$markid;
                  header("Location: ".$this->csapp->url('plub/update',$data)."");

			}else{  //下载

                  $zip=$this->csapp->url('plub/update').'&code=gbk&key='.$key;
                  $zippath=FCPATH."attachment/other/plugins_".$dir."_update.zip";
                  $files_file=$this->csapp->down($zip,$zippath);
    	          if($files_file=='-1') admin_msg(L('plub_15'),site_url('plugins'),'no');
    	          if($files_file=='-2') admin_msg(L('plub_16'),site_url('plugins'),'no');
    	          if($files_file=='-3') admin_msg(L('plub_17'),site_url('plugins'),'no');
    	          if($files_file=='10001') admin_msg(L('plub_24'),site_url('plugins'),'no');
    	          if($files_file=='10002') admin_msg(L('plub_25'),site_url('plugins'),'no');
    	          if($files_file=='10003') admin_msg(L('plub_26'),site_url('plugins'),'no');
	              if(filesize($zippath) == 0) admin_msg(L('plub_27'),site_url('plugins'),'no');

				  //先备份原始板块
	              $this->load->library('cszip');
				  $zip_path=FCPATH."attachment/other/plugins_".$dir."_backup_".date('Ymd').".zip";
				  $plub_path = "./plugins/".$dir."/"; 
		          $this->cszip->PclZip($zip_path); //创建压缩包
                  $this->cszip->create($plub_path); //增加增加目录
		          //解压缩
		          $this->cszip->PclZip($zippath);
				  //尝试解压覆盖
		          if ($this->cszip->extract(PCLZIP_OPT_PATH, $plub_path, PCLZIP_OPT_REPLACE_NEWER) == 0) {
                       die(vsprintf(L('plub_28'),array($dir)).$zippath);
		          }else{
			           @unlink($zippath);
                       admin_msg(L('plub_29'),site_url('plugins'));
				  }
			}
	}

	//板块后台权限划分
	public function role()
	{
 	        $id = intval($this->input->get('id',true));
 	        $dir = $this->input->get('dir',true);
            if(is_dir(FCPATH.'plugins/'.$dir)){
				 $data['link']=require_once(FCPATH.'plugins/'.$dir.'/config/menu.php');
				 $data['dir']=$dir;
				 $row=$this->db->query("SELECT app FROM ".CS_SqlPrefix."adminzu where id='".$id."'")->row(); 
				 $apparr=unarraystring($row->app);
				 $data['app']=(!empty($apparr[$dir]))?$apparr[$dir]:'';
				 $data['id']=$id;
                 $this->load->view('plugins_role.html',$data);
			}else{
                 admin_msg(L('plub_06'),'###','no');  //板块不存在
			}
	}

	//板块后台权限修改
	public function role_save()
	{
 	        $app = $this->input->post('sys',true);
 	        $dir = $this->input->post('dir',true);
 	        $id = intval($this->input->post('id',true));
		    if(!empty($app)){
                 $apps=implode(',', $app);
			}else{
                 $apps='';
			}
			$row=$this->db->query("SELECT app FROM ".CS_SqlPrefix."adminzu where id='".$id."'")->row();
		    $apparr=unarraystring($row->app);
            $apparr[$dir]=$apps;
			$data['app']=arraystring($apparr);
            $this->CsdjDB->get_update('adminzu',$id,$data);
			die("<script>alert('".L('plub_30')."');parent.$('.webox').css({display:'none'});parent.$('.background').css({display:'none'});parent.parent.web_box(2);</script>");
	}

	//URL规则说明
	public function rule()
	{
            $this->load->view('rule.html');
	}

	//将路由规则生成至文件
	public function _route_file($file, $data=array(), $note=array(), $dir=array()) {
		
		$string = '<?php'.PHP_EOL.PHP_EOL;
		$string.= 'if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');'.PHP_EOL.PHP_EOL;
		$string.= '// 当生成伪静态时此文件会被系统覆盖；如果发生页面指向错误，可以调整下面的规则顺序；越靠前的规则优先级越高。'.PHP_EOL.PHP_EOL;
		
		if ($data) {
		
			arsort($data);
			foreach ($data as $key => $val) {
				$string.= '$route[\''.$key.'\']'.$this->_space($key).'= \''.$val.'\'; // '.$note[$key]['name'].' 对应规则：'.$note[$key]['url'].PHP_EOL;
			}
		}
		write_file($file, $string);
	}

	//正则解析
	private function _rule_preg_value($rule) {

		$rule = trim(trim($rule, '/'));
		if (preg_match_all('/\{(.*)\}/U', $rule, $match)) {

			$value = array();
			foreach ($match[0] as $k => $v) {
				$value[$v] = $k + 1;
			}
			
			$preg = preg_replace(
				array(
					'#\{id\}#U',
					'#\{page\}#U',
					'#\{sort\}#Ui',
					'#\{zu\}#U',
					'#\{ji\}#U',
					'#\{.+}#U',
					'#/#'
				),
				array(
					'(\d+)',
					'(\d+)',
					'(\w+)',
					'(\d+)',
					'(\d+)',					
					'(.+)',
					'\/'
				),
				$rule
			);
			
			return array($preg, $value);
		}
		
		return array(0, 0);
	}

	//补空格
	private function _space($name) {
		$len = strlen($name) + 2;
	    $cha = 40 - $len;
	    $str = '';
	    for ($i = 0; $i < $cha; $i ++) $str .= ' ';
	    return $str;
	}
}

