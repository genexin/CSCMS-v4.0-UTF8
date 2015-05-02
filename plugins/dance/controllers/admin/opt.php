<?php if ( ! defined('IS_ADMIN')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-08
 */
class Opt extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->helper('dance');
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
	}

    //收藏列表
	public function fav()
	{
            $kstime = $this->input->get_post('kstime',true);
            $jstime = $this->input->get_post('jstime',true);
            $zd  = intval($this->input->get_post('zd'));
            $sid  = intval($this->input->get_post('sid'));
            $key  = $this->input->get_post('key',true);
 	        $page = intval($this->input->get('page'));
            if($page==0) $page=1;
			$kstimes=empty($kstime)?0:strtotime($kstime)-86400;
			$jstimes=empty($jstime)?0:strtotime($jstime)+86400;
			if($kstimes>$jstimes) $kstimes=strtotime($kstime);

	        $data['zd'] = $zd;
	        $data['sid'] = $sid;
	        $data['key'] = $key;
	        $data['page'] = $page;
	        $data['kstime'] = $kstime;
	        $data['jstime'] = empty($jstime)?date('Y-m-d'):$jstime;

            $sql_string = "SELECT * FROM ".CS_SqlPrefix."dance_fav where 1=1";
			if($sid>0){
				 $sql_string.= " and sid=".($sid-1)."";
			}
			if(!empty($key)){
				if($zd=='user'){
					$uid=getzd('user','id',$key,'name');
				    $sql_string.= " and uid='".intval($uid)."'";
				}else{
				    $sql_string.= " and name like '%".$key."%'";
				}
			}
			if($kstimes>0){
	             $sql_string.= " and addtime>".$kstimes."";
			}
			if($jstimes>0){
	             $sql_string.= " and addtime<".$jstimes."";
			}
	        $sql_string.= " order by addtime desc";
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('dance/admin/opt/fav')."?sid=".$sid."&zd=".$zd."&key=".$key."&kstime=".$kstime."&jstime=".$jstime;
	        $per_page = 15; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['fav'] = $query->result();
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类

            $this->load->view('fav.html',$data);
	}

    //收藏记录删除
	public function fav_del()
	{
            $ids = $this->input->get_post('id');
			if(empty($ids)) admin_msg(L('plub_73'),'javascript:history.back();','no');
			if(is_array($ids)){
			     $idss=implode(',', $ids);
			}else{
			     $idss=$ids;
			}
			$this->CsdjDB->get_del('dance_fav',$ids);
            admin_msg(L('plub_74'),'javascript:history.back();','ok');  //操作成功
	}

    //收藏记录批量删除
	public function fav_pldel()
	{
            $id = intval($this->input->get('id'));
            if($id==3){
				 $times=time()-86400*90;
                 $this->db->query("delete from ".CS_SqlPrefix."dance_fav where addtime<".$times."");
			}else{
			     $this->db->query("delete from ".CS_SqlPrefix."dance_fav");
				 $this->db->query("TRUNCATE TABLE ".CS_SqlPrefix."dance_fav");
			}
            admin_msg(L('plub_74'),'javascript:history.back();','ok');  //操作成功
	}

    //视听记录列表
	public function play()
	{
            $kstime = $this->input->get_post('kstime',true);
            $jstime = $this->input->get_post('jstime',true);
            $zd  = $this->input->get_post('zd',true);
            $key  = $this->input->get_post('key',true);
 	        $page = intval($this->input->get('page'));
            if($page==0) $page=1;
			$kstimes=empty($kstime)?0:strtotime($kstime)-86400;
			$jstimes=empty($jstime)?0:strtotime($jstime)+86400;
			if($kstimes>$jstimes) $kstimes=strtotime($kstime);

	        $data['zd'] = $zd;
	        $data['key'] = $key;
	        $data['page'] = $page;
	        $data['kstime'] = $kstime;
	        $data['jstime'] = empty($jstime)?date('Y-m-d'):$jstime;

            $sql_string = "SELECT * FROM ".CS_SqlPrefix."dance_play where 1=1";
			if(!empty($key)){
				if($zd=='user'){
					$uid=getzd('user','id',$key,'name');
				    $sql_string.= " and uid='".intval($uid)."'";
				}else{
				    $sql_string.= " and name like '%".$key."%'";
				}
			}
			if($kstimes>0){
	             $sql_string.= " and addtime>".$kstimes."";
			}
			if($jstimes>0){
	             $sql_string.= " and addtime<".$jstimes."";
			}
	        $sql_string.= " order by addtime desc";
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('dance/admin/opt/play')."?zd=".$zd."&key=".$key."&kstime=".$kstime."&jstime=".$jstime;
	        $per_page = 15; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['play'] = $query->result();
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类

            $this->load->view('play.html',$data);
	}

    //视听记录删除
	public function play_del()
	{
            $ids = $this->input->get_post('id');
			if(empty($ids)) admin_msg(L('plub_73'),'javascript:history.back();','no');
			if(is_array($ids)){
			     $idss=implode(',', $ids);
			}else{
			     $idss=$ids;
			}
			$this->CsdjDB->get_del('dance_play',$ids);
            admin_msg(L('plub_74'),'javascript:history.back();','ok');  //操作成功
	}

    //视听记录批量删除
	public function play_pldel()
	{
            $id = intval($this->input->get('id'));
            if($id==3){
				 $times=time()-86400*90;
                 $this->db->query("delete from ".CS_SqlPrefix."dance_play where addtime<".$times."");
			}else{
			     $this->db->query("delete from ".CS_SqlPrefix."dance_play");
				 $this->db->query("TRUNCATE TABLE ".CS_SqlPrefix."dance_play");
			}
            admin_msg(L('plub_74'),'javascript:history.back();','ok');  //操作成功
	}

    //下载记录列表
	public function down()
	{
            $kstime = $this->input->get_post('kstime',true);
            $jstime = $this->input->get_post('jstime',true);
            $zd  = $this->input->get_post('zd',true);
            $key  = $this->input->get_post('key',true);
 	        $page = intval($this->input->get('page'));
            if($page==0) $page=1;
			$kstimes=empty($kstime)?0:strtotime($kstime)-86400;
			$jstimes=empty($jstime)?0:strtotime($jstime)+86400;
			if($kstimes>$jstimes) $kstimes=strtotime($kstime);

	        $data['zd'] = $zd;
	        $data['key'] = $key;
	        $data['page'] = $page;
	        $data['kstime'] = $kstime;
	        $data['jstime'] = empty($jstime)?date('Y-m-d'):$jstime;

            $sql_string = "SELECT * FROM ".CS_SqlPrefix."dance_down where 1=1";
			if(!empty($key)){
				if($zd=='user'){
					$uid=getzd('user','id',$key,'name');
				    $sql_string.= " and uid='".intval($uid)."'";
				}else{
				    $sql_string.= " and name like '%".$key."%'";
				}
			}
			if($kstimes>0){
	             $sql_string.= " and addtime>".$kstimes."";
			}
			if($jstimes>0){
	             $sql_string.= " and addtime<".$jstimes."";
			}
	        $sql_string.= " order by addtime desc";
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('dance/admin/opt/down')."?zd=".$zd."&key=".$key."&kstime=".$kstime."&jstime=".$jstime;
	        $per_page = 15; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['down'] = $query->result();
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类

            $this->load->view('down.html',$data);
	}

    //观看记录删除
	public function down_del()
	{
            $ids = $this->input->get_post('id');
			if(empty($ids)) admin_msg(L('plub_73'),'javascript:history.back();','no');
			if(is_array($ids)){
			     $idss=implode(',', $ids);
			}else{
			     $idss=$ids;
			}
			$this->CsdjDB->get_del('dance_down',$ids);
            admin_msg(L('plub_74'),'javascript:history.back();','ok');  //操作成功
	}

    //观看记录批量删除
	public function down_pldel()
	{
            $id = intval($this->input->get('id'));
            if($id==3){
				 $times=time()-86400*90;
                 $this->db->query("delete from ".CS_SqlPrefix."dance_down where addtime<".$times."");
			}else{
			     $this->db->query("delete from ".CS_SqlPrefix."dance_down");
				 $this->db->query("TRUNCATE TABLE ".CS_SqlPrefix."dance_down");
			}
            admin_msg(L('plub_74'),'javascript:history.back();','ok');  //操作成功
	}

    //同步远程图片到本地
	public function downpic()
	{
            $page = intval($this->input->get('page'));
            $pagejs = intval($this->input->get('pagejs'));

            $sql_string = "SELECT id,pic FROM ".CS_SqlPrefix."dance where hid=0 and yid=0 and Lower(Left(pic,7))='http://' order by addtime desc";
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            if($page > $pagejs || $total==0){
                   admin_msg(L('plub_75'),site_url('dance/admin/dance'),'ok');  //操作完成
			}

            if($page==0) $page = 1;
	        $per_page = 20; 
            $totalPages = ceil($total / $per_page); // 总页数
            if($total<$per_page){
               $per_page=$total;
            }
			if($pagejs==0) $pagejs=$totalPages;
            $sql_string.=' limit 20';
	        $query = $this->db->query($sql_string); 

			//保存目录
			if(UP_Mode==1 && UP_Pan!=''){
			    $pathpic = UP_Pan.'/attachment/dance/'.date('Ym').'/'.date('d').'/';
				$pathpic = str_replace("//","/",$pathpic);
			}else{
			    $pathpic = FCPATH.'attachment/dance/'.date('Ym').'/'.date('d').'/';
			}
			if (!is_dir($pathpic)) {
                mkdirss($pathpic);
            }

			$this->load->library('watermark');
            $this->load->library('csup');

            echo '<LINK href="'.Web_Path.'packs/admin/css/style.css" type="text/css" rel="stylesheet"><br>';
	        echo "<div style='font-size:14px;'>&nbsp;&nbsp;&nbsp;<b>".L('plub_76')."<font style='color:red; font-size:12px; font-style:italic'>".$page."</font>".L('plub_77')."，".L('plub_78')."<font style='color:red; font-size:12px; font-style:italic'>".$pagejs."</font>".L('plub_77')."，".L('plub_79')."<font style='color:red; font-size:12px; font-style:italic'>".$totalPages."</font>".L('plub_77')."</b><br><br>";
           
            foreach ($query->result() as $row) {
				$up='no';
				if(!empty($row->pic)){
                       $picdata=htmlall($row->pic);
					   $file_ext = strtolower(trim(substr(strrchr($row->pic, '.'), 1)));
                       if($file_ext!='jpg' && $file_ext!='png' && $file_ext!='gif'){
					       $file_ext = 'jpg';
					   }
                       //新文件名
                       $file_name=date("YmdHis") . rand(10000, 99999) . '.' . $file_ext;
			           $file_path=$pathpic.$file_name;
                       if(!empty($picdata)){
						   //保存图片
                           if(write_file($file_path, $picdata)){
                                   $up='ok';
                                   //判断水印
                                   if(CS_WaterMark==1){
                                         $this->watermark->imagewatermark($file_path);
                                   }
					               //判断上传方式
					               $res=$this->csup->up($file_path,$file_name);
					               if(!$res){
					                   $up='no';
					               }
						   }
					   }
				}
				//成功
				if($up=='ok'){
                       //修改数据库
                       $this->db->query("update ".CS_SqlPrefix."dance set pic='/".date('Ym')."/".date('d')."/".$file_name."' where id=".$row->id."");
                       echo "&nbsp;&nbsp;&nbsp;&nbsp;".L('plub_80')."<font color=red>".$row->pic."</font>&nbsp;".L('plub_81')."&nbsp;&nbsp;".L('plub_82')."<a href=\"".piclink('dance','/'.date('Ym').'/'.date('d').'/'.$file_name)."\" target=_blank>".$file_name."</a></br>";
				}else{
                       //修改数据库
                       $this->db->query("update ".CS_SqlPrefix."dance set pic='' where id=".$row->id."");
                       echo "&nbsp;&nbsp;&nbsp;&nbsp;<font color=red>".$row->pic."</font>".L('plub_83')."</br>";
				}
				ob_flush();flush();
			}
	        echo "&nbsp;&nbsp;&nbsp;&nbsp;".vsprintf(L('plub_84'),array($page))."<script language='javascript'>setTimeout('ReadGo();',".(3000).");function ReadGo(){location.href='".site_url('dance/admin/opt/downpic')."?page=".($page+1)."&pagejs=".$pagejs."';}</script></div>";
	}
}

