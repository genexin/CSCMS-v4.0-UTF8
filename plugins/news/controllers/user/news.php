<?php if ( ! defined('CSCMS')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-08
 */
class News extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
			$this->load->helper('news');
		    $this->load->model('CsdjTpl');
		    $this->load->model('CsdjUser');
	        $this->CsdjUser->User_Login();
			$this->load->helper('string');
	}

    //已审核
	public function index()
	{
		    $cid=intval($this->uri->segment(4)); //分类ID
		    $page=intval($this->uri->segment(5)); //分页
			//模板
			$tpl='news.html';
			//URL地址
		    $url='news/index/'.$cid;
            $sqlstr = "select {field} from ".CS_SqlPrefix."news where yid=0 and uid=".$_SESSION['cscms__id'];
            if($cid>0){
				$cids=getChild($cid);
                $sqlstr.= " and cid in(".$cids.")";
			}
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='我的文章 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,$cid,$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[news:cid]",$cid,$Mark_Text);
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
			$tpl='verify.html';
			//URL地址
		    $url='news/verify/'.$cid;
            $sqlstr = "select {field} from ".CS_SqlPrefix."news where yid=1 and uid=".$_SESSION['cscms__id'];
            if($cid>0){
				$cids=getChild($cid);
                $sqlstr.= " and cid in(".$cids.")";
			}
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='待审文章 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,$cid,$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[news:cid]",$cid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

	//分享文章
	public function add()
	{
			//模板
			$tpl='add.html';
			//URL地址
		    $url='news/add';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];

			//检测发表权限
			$rowz=$this->CsdjDB->get_row('userzu','aid,sid',$row['zid']);
			if(!$rowz || $rowz->aid==0){
                 msg_url('您所在会员组没有权限发表文章~!','javascript:history.back();');
			}
			
			//装载模板
			$title='文章投稿 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,1,$tpl,$title,'','',$ids,true,false);
			//token
			$token=random_string('alnum',10);
			$_SESSION['token']=$token;
			$Mark_Text=str_replace("[user:token]",$token,$Mark_Text);
			//提交地址
			$Mark_Text=str_replace("[user:newssave]",spacelink('news,save','news'),$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

	//上传文章保存
	public function save()
	{
			$token=$this->input->post('token', TRUE);
			if($token!=$_SESSION['token']) msg_url('非法提交~!','javascript:history.back();');
			//检测发表权限
			$zuid=getzd('user','zid',$_SESSION['cscms__id']);
			$rowu=$this->CsdjDB->get_row('userzu','aid,sid',$zuid);
			if(!$rowu || $rowu->aid==0){
                 msg_url('您所在会员组没有权限发表文章~!','javascript:history.back();');
			}
			//检测发表数据是否需要审核
			$news['yid']=($rowu->sid==1)?0:1;
			//选填字段
			$news['cion']=intval($this->input->post('cion'));
			$news['pic']=$this->input->post('pic', TRUE, TRUE);
			$news['tags']=$this->input->post('tags', TRUE, TRUE);
			$news['info']=$this->input->post('info', TRUE, TRUE);
			$news['uid']=$_SESSION['cscms__id'];
			$news['addtime']=time();
            //必填字段
			$news['name']=$this->input->post('name', TRUE, TRUE);
			$news['cid']=intval($this->input->post('cid'));
			$news['content']=remove_xss($this->input->post('content'));
            //检测必须字段
			if($news['cid']==0) msg_url('请选择文章分类~!','javascript:history.back();');
			if(empty($news['name'])) msg_url('文章名称不能为空~!','javascript:history.back();');
			if(empty($news['content'])) msg_url('文章内容不能为空~!','javascript:history.back();');
            //截取概述
			$news['info'] = sub_str(str_checkhtml($news['content']),120);
            //增加到数据库
            $did=$this->CsdjDB->get_insert('news',$news);
			if(intval($did)==0){
				 msg_url('文章发布失败，请稍候再试~!','javascript:history.back();');
			}
            //摧毁token
            unset($_SESSION['token']);
			//增加动态
		    $dt['dir'] = 'news';
		    $dt['uid'] = $_SESSION['cscms__id'];
		    $dt['did'] = $did;
		    $dt['yid'] = $news['yid'];
		    $dt['title'] = '发布了文章';
		    $dt['name'] = $news['name'];
		    $dt['link'] = linkurl('show','id',$did,1,'news');
		    $dt['addtime'] = time();
            $this->CsdjDB->get_insert('dt',$dt);
			//如果免审核，则给会员增加相应金币、积分
			if($news['yid']==0){
			     $addhits=getzd('user','addhits',$_SESSION['cscms__id']);
				 if($addhits<User_Nums_Add){
                     $this->db->query("update ".CS_SqlPrefix."user set cion=cion+".User_Cion_Add.",jinyan=jinyan+".User_Jinyan_Add.",addhits=addhits+1 where id=".$_SESSION['cscms__id']."");
				 }
				 msg_url('恭喜您，文章发布成功~!',spacelink('news','news'));
			}else{
				 msg_url('恭喜您，文章发布成功,请等待管理员审核~!',spacelink('news/verify','news'));
			}
	}
}

