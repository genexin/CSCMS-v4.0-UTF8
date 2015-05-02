<?php if ( ! defined('IS_ADMIN')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-16
 */
class Pic extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->helper('pic');
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
	}

	public function index()
	{
            $cid  = intval($this->input->get_post('cid'));
            $sid  = intval($this->input->get_post('sid'));
            $yid  = intval($this->input->get_post('yid'));
            $zd   = $this->input->get_post('zd',true);
            $key  = $this->input->get_post('key',true);
 	        $page = intval($this->input->get('page'));
            if($page==0) $page=1;

	        $data['page'] = $page;
	        $data['cid'] = $cid;
	        $data['sid'] = $sid;
	        $data['yid'] = $yid;
	        $data['zd'] = $zd;
	        $data['key'] = $key;

            $sql_string = "SELECT * FROM ".CS_SqlPrefix."pic where 1=1";
			if($yid==1){
				 $sql_string.= " and yid=0";
			}
			if($yid==2){
				 $sql_string.= " and yid=1";
			}
			if($yid==3){
				 $sql_string.= " and hid=1";
			}else{
				 $sql_string.= " and hid=0";
			}
			if($cid>0){
	             $sql_string.= " and cid=".$cid."";
			}
			if($sid>0){
	             $sql_string.= " and sid=".$sid."";
			}
			if(!empty($key)){
				if($zd=='id' || $zd=='sid'){
				     $sql_string.= " and ".$zd."=".$key."";
				}else{
				     $sql_string.= " and ".$zd." like '%".$key."%'";
				}
			}
	        $sql_string.= " order by id desc";
			//exit($sql_string);
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('pic/admin/pic')."?yid=".$yid."&zd=".$zd."&key=".$key."&cid=".$cid."&sid=".$sid;
	        $per_page = 15; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['pic'] = $query->result();
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类

            $this->load->view('pic.html',$data);
	}

    //推荐、锁定操作
	public function init()
	{
            $ac  = $this->input->get_post('ac',true);
            $id   = intval($this->input->get_post('id'));
            $sid  = intval($this->input->get_post('sid'));
            if($ac=='zt'){ //锁定
                  $edit['yid']=$sid;
				  if($sid==0) $this->dt($id);
				  $str=($sid==0)?'<a title="点击锁定" href="javascript:get_cmd(\''.site_url('pic/admin/pic/init').'?sid=1\',\'zt\','.$id.');"><img align="absmiddle" src="'.Web_Path.'packs/admin/images/icon/ok.gif" /></a>':'<a title="点击解除锁定" href="javascript:get_cmd(\''.site_url('pic/admin/pic/init').'?sid=0\',\'zt\','.$id.');"><img align="absmiddle" src="'.Web_Path.'packs/admin/images/icon/no.gif" /></a>';
			}
            $this->CsdjDB->get_update('pic',$id,$edit);
            die($str);
	}

    //图片新增、修改
	public function edit()
	{
            $id   = intval($this->input->get('id'));
			if($id==0){
                $data['id']=0;
                $data['cid']=0;
                $data['sid']=0;
                $data['yid']=0;
                $data['pic']='';
                $data['user']='';
                $data['content']='';
			}else{
                $row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."pic where id=".$id."")->row(); 
			    if(!$row) admin_msg('该条记录不存在~!','javascript:history.back();','no');  //记录不存在

                $data['id']=$row->id;
                $data['cid']=$row->cid;
                $data['yid']=$row->yid;
                $data['sid']=$row->sid;
                $data['pic']=$row->pic;
                $data['user']=getzd('user','name',$row->uid);
                $data['content']=$row->content;
			}
            $this->load->view('pic_edit.html',$data);
	}

    //图片保存
	public function save()
	{
            $id   = intval($this->input->post('id'));
            $sid = intval($this->input->post('sid'));
            $cid = intval($this->input->post('cid'));
            $content = remove_xss($this->input->post('content'));
            $addtime = $this->input->post('addtime',true);
            $pic = $this->input->post('pic',true);
            $user = $this->input->post('user',true);

            if($sid==0 || $cid==0 || empty($pic)){
                   admin_msg('抱歉，所属相册、分类、图片地址不能为空~!','javascript:history.back();','no');
			}

            $data['cid']=$cid;
            $data['yid']=intval($this->input->post('yid'));
            $data['sid']=$sid;
            $data['pic']=$pic;
            $data['uid']=intval(getzd('user','id',$user,'name'));
            $data['content']=$content;

			if($id==0){ //新增
				 $data['addtime']=time();
                 $this->CsdjDB->get_insert('pic',$data);
			}else{
				 if($data['yid']==0) $this->dt($id);
                 if($addtime=='ok') $data['addtime']=time();
                 $this->CsdjDB->get_update('pic',$id,$data);
			}
            admin_msg('恭喜您，操作成功~!',site_url('pic/admin/pic'),'ok');  //操作成功
	}

    //图片删除
	public function del()
	{
            $yid = intval($this->input->get('yid'));
            $ids = $this->input->get_post('id');
            $ac = $this->input->get_post('ac');
			//回收站
			if($ac=='hui'){
			     $result=$this->db->query("SELECT id,pic FROM ".CS_SqlPrefix."pic where hid=1")->result();
			     $this->load->library('csup');
			     foreach ($result as $row) {
                     if(!empty($row->pic) && substr($row->pic,0,7)!='http://'){
					    $this->csup->del($row->pic,'pic'); //删除图片
				     }
					 $this->CsdjDB->get_del('pic',$row->id);
				 }
                 admin_msg('恭喜您，回收站清空成功~!','javascript:history.back();','ok');  //操作成功
			}
			if(empty($ids)) admin_msg('请选择要删除的数据~!','javascript:history.back();','no');
			if(is_array($ids)){
			     $idss=implode(',', $ids);
			}else{
			     $idss=$ids;
			}
			if($yid==3){
			     $result=$this->db->query("SELECT pic FROM ".CS_SqlPrefix."pic where id in(".$idss.")")->result();
			     $this->load->library('csup');
			     foreach ($result as $row) {
                     if(!empty($row->pic) && substr($row->pic,0,7)!='http://'){
					    $this->csup->del($row->pic,'pic'); //删除图片
				     }
				 }
			     $this->CsdjDB->get_del('pic',$ids);
                 admin_msg('恭喜您，删除成功~!','javascript:history.back();','ok');  //操作成功
			}else{
				 $data['hid']=1;
			     $this->CsdjDB->get_update('pic',$ids,$data);
                 admin_msg('恭喜您，数据已经移动至回收站~!','javascript:history.back();','ok');  //操作成功
			}
	}

    //图片还原
	public function hy()
	{
            $ids = $this->input->get_post('id');
			if(empty($ids)) admin_msg('请选择要还原的数据~!','javascript:history.back();','no');
			if(is_array($ids)){
			     $idss=implode(',', $ids);
			}else{
			     $idss=$ids;
			}
			$data['hid']=0;
			$this->CsdjDB->get_update('pic',$ids,$data);
            admin_msg('恭喜您，数据还原成功~!','javascript:history.back();','ok');  //操作成功
	}

    //选择相册列表
	public function res()
	{
 	        $page = intval($this->input->get('page'));
			$sid = intval($this->uri->segment(4));
            if($page==0) $page=1;
	        $data['page'] = $page;
	        $data['sid'] = $sid;
            $sql_string = "SELECT * FROM ".CS_SqlPrefix."pic_type where yid=0 and hid=0 order by id desc";
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('pic/admin/pic/res/'.$sid);
	        $per_page = 8; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['pic_type'] = $query->result();
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类
            $this->load->view('pic_res.html',$data);
	}

	//审核图片增加积分、经验、同时动态显示
	public function dt($id)
	{
		    $sid=getzd('pic','sid',$id);
			$dt=$this->db->query("SELECT id,name FROM ".CS_SqlPrefix."dt where link='".linkurl('show','id',$sid,1,'pic')."' and yid=1")->row();
			if($dt){
                  $uid=getzd('pic','uid',$id);
			      $addhits=getzd('user','addhits',$uid);
				  $str='';
				  if($addhits<User_Nums_Add){
                     $this->db->query("update ".CS_SqlPrefix."user set cion=cion+".User_Cion_Add.",jinyan=jinyan+".User_Jinyan_Add.",addhits=addhits+1 where id=".$uid."");
					 $str.='同时为您增加'.User_Cion_Add.'个金币，'.User_Jinyan_Add.'个经验值，';
				  }
                  $this->db->query("update ".CS_SqlPrefix."dt set yid=0,addtime='".time()."' where id=".$dt->id."");
				  //发送歌曲审核通知
				  $add['uida']=$uid;
				  $add['uidb']=0;
				  $add['name']='图片审核通知';
				  $add['neir']='恭喜您，您的图片《'.$dt->name.'》已经审核通过，'.$str.'感谢您的支持~~';
				  $add['addtime']=time();
            	  $this->CsdjDB->get_insert('msg',$add);
			}
	}
}
