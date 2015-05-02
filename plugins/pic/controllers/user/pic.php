<?php if ( ! defined('CSCMS')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-08
 */
class Pic extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
			$this->load->helper('pic');
		    $this->load->model('CsdjTpl');
		    $this->load->model('CsdjUser');
	        $this->CsdjUser->User_Login();
			$this->load->helper('string');
	}

    //所有图片
	public function index()
	{
		    $cid=intval($this->uri->segment(4)); //分类ID
		    $yid=intval($this->uri->segment(5)); //审核ID
		    $page=intval($this->uri->segment(6)); //分页
			//模板
			$tpl='pic.html';
			//URL地址
		    $url='pic/index/'.$cid.'/'.$yid;
            $sqlstr = "select {field} from ".CS_SqlPrefix."pic where yid=".$yid." and uid=".$_SESSION['cscms__id'];
            if($cid>0){
				$cids=getChild($cid);
                $sqlstr.= " and cid in(".$cids.")";
			}
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='我的图片 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,$cid,$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[pic:cid]",$cid,$Mark_Text);
			$Mark_Text=str_replace("[pic:yid]",$yid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

    //已审核先相册
	public function type()
	{
		    $cid=intval($this->uri->segment(4)); //分类ID
		    $page=intval($this->uri->segment(5)); //分页
			//模板
			$tpl='pic-type.html';
			//URL地址
		    $url='pic/type/'.$cid;
            $sqlstr = "select {field} from ".CS_SqlPrefix."pic_type where yid=0 and uid=".$_SESSION['cscms__id'];
            if($cid>0){
				$cids=getChild($cid);
                $sqlstr.= " and cid in(".$cids.")";
			}
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='我的相册 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,$cid,$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[pic:cid]",$cid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

    //待审核
	public function verify()
	{
		    $cid=intval($this->uri->segment(4)); //分类ID
		    $page=intval($this->uri->segment(5)); //分页
			//模板
			$tpl='pic-verify.html';
			//URL地址
		    $url='pic/verify/'.$cid;
            $sqlstr = "select {field} from ".CS_SqlPrefix."pic_type where yid=1 and uid=".$_SESSION['cscms__id'];
            if($cid>0){
				$cids=getChild($cid);
                $sqlstr.= " and cid in(".$cids.")";
			}
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='待审相册 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,$cid,$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[pic:cid]",$cid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

	//上传相册
	public function addtype()
	{
			//模板
			$tpl='add-type.html';
			//URL地址
		    $url='pic/addtype';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];

			//检测发表权限
			$rowz=$this->CsdjDB->get_row('userzu','aid,sid',$row['zid']);
			if(!$rowz || $rowz->aid==0){
                 msg_url('您所在会员组没有权限制作相册~!','javascript:history.back();');
			}
			
			//装载模板
			$title='制作相册 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,1,$tpl,$title,'','',$ids,true,false);
			//token
			$token=random_string('alnum',10);
			$_SESSION['token']=$token;
			$Mark_Text=str_replace("[user:token]",$token,$Mark_Text);
			//提交地址
			$Mark_Text=str_replace("[user:pictypesave]",spacelink('pic,typesave','pic'),$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

	//制作相册保存
	public function typesave()
	{
			$token=$this->input->post('token', TRUE);
			if($token!=$_SESSION['token']) msg_url('非法提交~!','javascript:history.back();');

			//检测发表权限
			$zuid=getzd('user','zid',$_SESSION['cscms__id']);
			$rowu=$this->CsdjDB->get_row('userzu','aid,sid',$zuid);
			if(!$rowu || $rowu->aid==0){
                 msg_url('您所在会员组没有权限制作相册~!','javascript:history.back();');
			}
			//检测发表数据是否需要审核
			$pic['yid']=($rowu->sid==1)?0:1;
			//选填字段
			$pic['tags']=$this->input->post('tags', TRUE, TRUE);
			$pic['uid']=$_SESSION['cscms__id'];
			$pic['addtime']=time();

            //必填字段
			$pic['name']=$this->input->post('name', TRUE, TRUE);
			$pic['cid']=intval($this->input->post('cid'));
			$pic['pic']=$this->input->post('pic', TRUE, TRUE);

            //检测必须字段
			if($pic['cid']==0) msg_url('请选择相册分类~!','javascript:history.back();');
			if(empty($pic['name'])) msg_url('相册标题不能为空~!','javascript:history.back();');
			if(empty($pic['pic'])) msg_url('相册封面不能为空~!','javascript:history.back();');
			$singer=$this->input->post('singer', TRUE, TRUE);

			//判断歌手是否存在
			if(!empty($singer)){
			     $row=$this->CsdjDB->get_row('singer','id',$singer,'name');
				 if($row){
                       $pic['singerid']=$row->id;
				 }
			}

            //增加到数据库
            $did=$this->CsdjDB->get_insert('pic_type',$pic);
			if(intval($did)==0){
				 msg_url('相册制作失败，请稍候再试~!','javascript:history.back();');
			}

            //摧毁token
            unset($_SESSION['token']);
			//增加动态
		    $dt['dir'] = 'pic';
		    $dt['uid'] = $_SESSION['cscms__id'];
		    $dt['did'] = $did;
		    $dt['yid'] = $pic['yid'];
		    $dt['title'] = '制作了相册';
		    $dt['name'] = $pic['name'];
		    $dt['link'] = linkurl('show','id',$did,1,'pic');
		    $dt['addtime'] = time();
            $this->CsdjDB->get_insert('dt',$dt);
			//如果免审核，则给会员增加相应金币、积分
			if($pic['yid']==0){
			     $addhits=getzd('user','addhits',$_SESSION['cscms__id']);
				 if($addhits<User_Nums_Add){
                     $this->db->query("update ".CS_SqlPrefix."user set cion=cion+".User_Cion_Add.",jinyan=jinyan+".User_Jinyan_Add.",addhits=addhits+1 where id=".$_SESSION['cscms__id']."");
				 }
				 msg_url('恭喜您，相册制作成功~!',spacelink('pic,type','pic'));
			}else{
				 msg_url('恭喜您，相册制作成功,请等待管理员审核~!',spacelink('pic/verify','pic'));
			}
	}

    //上传图片
	public function add()
	{
		    $id=intval($this->uri->segment(4));
			$name='';
			if($id>0){
			    $rowc=$this->CsdjDB->get_row('pic_type','uid,name',$id);
				if(!$rowc || $rowc->uid!=$_SESSION['cscms__id']) msg_url('相册不存在~!','javascript:history.back();');
				$name=$rowc->name;
			}
			//模板
			$tpl='add.html';
			//URL地址
		    $url='pic/add';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];

			//检测发表权限
			$rowz=$this->CsdjDB->get_row('userzu','aid,sid',$row['zid']);
			if(!$rowz || $rowz->aid==0){
                 msg_url('您所在会员组没有权限制作相册~!','javascript:history.back();');
			}
			
			//装载模板
			$title='上传图片 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,1,$tpl,$title,'','',$ids,true,false);
			$Mark_Text=str_replace("[pic:sid]",$id,$Mark_Text);
			$Mark_Text=str_replace("[pic:name]",$name,$Mark_Text);
			//token
			$token=random_string('alnum',10);
			$_SESSION['token']=$token;
			$Mark_Text=str_replace("[user:token]",$token,$Mark_Text);
			//提交地址
			$Mark_Text=str_replace("[user:picsave]",spacelink('pic,save','pic'),$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

	//上传图片保存
	public function save()
	{
			$token=$this->input->post('token', TRUE);
			if($token!=$_SESSION['token']) msg_url('非法提交~!','javascript:history.back();');

			//检测发表权限
			$zuid=getzd('user','zid',$_SESSION['cscms__id']);
			$rowu=$this->CsdjDB->get_row('userzu','aid,sid',$zuid);
			if(!$rowu || $rowu->aid==0){
                 msg_url('您所在会员组没有权限上传图片~!','javascript:history.back();');
			}
			//检测发表数据是否需要审核
			$pic['yid']=($rowu->sid==1)?0:1;
			//选填字段
			$pic['content']=str_replace("\r\n","<br>",str_encode($_POST['content']));
			$pic['uid']=$_SESSION['cscms__id'];
			$pic['addtime']=time();
			$name=$this->input->post('name', TRUE, TRUE);
            //必填字段
			$pic['sid']=intval($this->input->post('sid'));
			$pic['cid']=intval($this->input->post('cid'));
			$pic['pic']=$this->input->post('pic', TRUE, TRUE);
            //检测必须字段
			if($pic['cid']==0) msg_url('请选择图片分类~!','javascript:history.back();');
			if($pic['sid']==0) msg_url('请选择图片所属相册~!','javascript:history.back();');
			if(empty($pic['pic'])) msg_url('图片地址不能为空~!','javascript:history.back();');
            //增加到数据库
            $did=$this->CsdjDB->get_insert('pic',$pic);
			if(intval($did)==0){
				 msg_url('图片上传失败，请稍候再试~!','javascript:history.back();');
			}
            //摧毁token
            unset($_SESSION['token']);
			//增加动态
		    $dt['dir'] = 'pic';
		    $dt['uid'] = $_SESSION['cscms__id'];
		    $dt['did'] = $pic['sid'];
		    $dt['yid'] = $pic['yid'];
		    $dt['title'] = '上传了图片到'.$name;
		    $dt['name'] = $name;
		    $dt['link'] = linkurl('show','id',$pic['sid'],1,'pic');
		    $dt['addtime'] = time();
            $this->CsdjDB->get_insert('dt',$dt);
			//如果免审核，则给会员增加相应金币、积分
			if($pic['yid']==0){
			     $addhits=getzd('user','addhits',$_SESSION['cscms__id']);
				 if($addhits<User_Nums_Add){
                     $this->db->query("update ".CS_SqlPrefix."user set cion=cion+".User_Cion_Add.",jinyan=jinyan+".User_Jinyan_Add.",addhits=addhits+1 where id=".$_SESSION['cscms__id']."");
				 }
				 msg_url('恭喜您，图片上传成功~!',spacelink('pic','pic'));
			}else{
				 msg_url('恭喜您，图片上传成功,请等待管理员审核~!',spacelink('pic','pic').'/index/0/1');
			}
	}

    //删除消息
	public function del()
	{
		    $id=intval($this->uri->segment(4));
			if($id==0){ //选择删除
				$id=$this->input->post('id', TRUE);
				if(empty($id)) msg_url('请选择要删除的图片~!','javascript:history.back();');
				$ids=implode(',', $id);
                $this->db->query("delete from ".CS_SqlPrefix."pic where uid=".$_SESSION['cscms__id']." and id in(".$ids.")");
			}else{ //按ID
                $this->db->query("delete from ".CS_SqlPrefix."pic where uid=".$_SESSION['cscms__id']." and id=".$id."");
			}
            msg_url('图片删除成功~!',$_SERVER['HTTP_REFERER']);
	}

    //选择相册列表
	public function res()
	{
		    $sid=intval($this->uri->segment(4)); //分页
		    $page=intval($this->uri->segment(5)); //分页
			//模板
			$tpl='pic-res.html';
			//URL地址
		    $url='pic/res/'.$sid;
            $sqlstr = "SELECT * FROM ".CS_SqlPrefix."pic_type where yid=0 and hid=0 and uid=".$_SESSION['cscms__id'];
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='选择相册 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,'',$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[pic:sid]",$sid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}
}

