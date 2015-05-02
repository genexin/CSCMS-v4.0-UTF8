<?php if ( ! defined('CSCMS')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-08
 */
class Album extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
			$this->load->helper('dance');
		    $this->load->model('CsdjTpl');
		    $this->load->model('CsdjUser');
	        $this->CsdjUser->User_Login();
			$this->load->helper('string');
	}

    //已审核
	public function index()
	{
		    $page=intval($this->uri->segment(4)); //分页
			//模板
			$tpl='album.html';
			//URL地址
		    $url='album/index';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='我的专辑 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $sqlstr = "select {field} from ".CS_SqlPrefix."dance_topic where yid=0 and uid=".$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,'','',$ids,true,false);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

    //待审核
	public function verify()
	{
		    $page=intval($this->uri->segment(4)); //分页
			//模板
			$tpl='album-verify.html';
			//URL地址
		    $url='album/verify';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='待审专辑 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $sqlstr = "select {field} from ".CS_SqlPrefix."dance_topic where yid=1 and uid=".$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,'','',$ids,true,false);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

	//创建专辑
	public function add()
	{
			//模板
			$tpl='album-add.html';
			//URL地址
		    $url='album/add';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];

			//检测发表权限
			$rowz=$this->CsdjDB->get_row('userzu','aid,sid',$row['zid']);
			if($rowz->aid==0){
                 msg_url('您所在会员组没有权限创建专辑~!','javascript:history.back();');
			}
			
			//装载模板
			$title='创建专辑 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,1,$tpl,$title,'','',$ids,true,false);
			//token
			$token=random_string('alnum',10);
			$_SESSION['token']=$token;
			$Mark_Text=str_replace("[user:token]",$token,$Mark_Text);
			//提交地址
			$Mark_Text=str_replace("[user:albumsave]",spacelink('album,save','dance'),$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

	//上传歌曲保存
	public function save()
	{
			$token=$this->input->post('token', TRUE);
			if($token!=$_SESSION['token']) msg_url('非法提交~!','javascript:history.back();');

			//检测发表权限
			$zuid=getzd('user','zid',$_SESSION['cscms__id']);
			$rowu=$this->CsdjDB->get_row('userzu','aid,sid',$zuid);
			if($rowu->aid==0){
                 msg_url('您所在会员组没有权限创建专辑~!','javascript:history.back();');
			}
			//检测发表数据是否需要审核
			$album['yid']=($rowu->sid==1)?0:1;

            //必填字段
			$album['name']=$this->input->post('name', TRUE, TRUE);
			$album['cid']=intval($this->input->post('cid'));
			$album['pic']=$this->input->post('pic', TRUE, TRUE);
			$album['neir']=str_replace("\r\n","<br>",str_encode(trim($_POST['neir'])));

            //检测必须字段
			if($album['cid']==0) msg_url('请选择专辑分类~!','javascript:history.back();');
			if(empty($album['name'])) msg_url('专辑名称不能为空~!','javascript:history.back();');
			if(empty($album['pic'])) msg_url('专辑图片不能为空~!','javascript:history.back();');
			if(empty($album['neir'])) msg_url('专辑介绍不能为空~!','javascript:history.back();');

			//选填字段
			$album['yuyan']=$this->input->post('yuyan', TRUE, TRUE);
			$album['diqu']=$this->input->post('diqu', TRUE, TRUE);
			$album['tags']=$this->input->post('tags', TRUE, TRUE);
			$album['fxgs']=$this->input->post('fxgs', TRUE, TRUE);
			$album['year']=$this->input->post('year', TRUE, TRUE);
			$album['uid']=$_SESSION['cscms__id'];
			$album['addtime']=time();

			$singer=$this->input->post('singer', TRUE, TRUE);
			//判断歌手是否存在
			if(!empty($singer)){
			     $row=$this->CsdjDB->get_row('singer','id',$singer,'name');
				 if($row){
                       $album['singerid']=$row->id;
				 }
			}
            //增加到数据库
            $did=$this->CsdjDB->get_insert('dance_topic',$album);
			if(intval($did)==0){
				 msg_url('专辑制作失败，请稍候再试~!','javascript:history.back();');
			}

            //摧毁token
            unset($_SESSION['token']);

			//增加动态
		    $dt['dir'] = 'dance';
		    $dt['uid'] = $_SESSION['cscms__id'];
		    $dt['did'] = $did;
		    $dt['yid'] = $album['yid'];
		    $dt['title'] = '制作了专辑';
		    $dt['name'] = $album['name'];
		    $dt['link'] = linkurl('topic/show','id',$did,1,'dance');
		    $dt['addtime'] = time();
            $this->CsdjDB->get_insert('dt',$dt);

			//如果免审核，则给会员增加相应金币、积分
			if($album['yid']==0){
			     $addhits=getzd('user','addhits',$_SESSION['cscms__id']);
				 if($addhits<User_Nums_Add){
                     $this->db->query("update ".CS_SqlPrefix."user set cion=cion+".User_Cion_Add.",jinyan=jinyan+".User_Jinyan_Add.",addhits=addhits+1 where id=".$_SESSION['cscms__id']."");
				 }
				 msg_url('恭喜您，专辑制作成功~!',spacelink('album','dance'));
			}else{
				 msg_url('恭喜您，专辑制作成功,请等待管理员审核~!',spacelink('album/verify','dance'));
			}
	}
}

