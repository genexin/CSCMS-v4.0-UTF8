<?php if ( ! defined('CSCMS')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-08
 */
class Dance extends Cscms_Controller {

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
		    $cid=intval($this->uri->segment(4)); //分类ID
		    $page=intval($this->uri->segment(5)); //分页
			//模板
			$tpl='dance.html';
			//URL地址
		    $url='dance/index/'.$cid;
            $sqlstr = "select {field} from ".CS_SqlPrefix."dance where yid=0 and uid=".$_SESSION['cscms__id'];
            if($cid>0){
				$cids=getChild($cid);
                $sqlstr.= " and cid in(".$cids.")";
			}
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='我的歌曲 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,$cid,$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[dance:cid]",$cid,$Mark_Text);
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
		    $url='dance/verify/'.$cid;
            $sqlstr = "select {field} from ".CS_SqlPrefix."dance where yid=1 and uid=".$_SESSION['cscms__id'];
            if($cid>0){
				$cids=getChild($cid);
                $sqlstr.= " and cid in(".$cids.")";
			}
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title='待审歌曲 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,$page,$tpl,$title,$cid,$sqlstr,$ids,true,false);
			$Mark_Text=str_replace("[dance:cid]",$cid,$Mark_Text);
			//会员版块导航
			$Mark_Text=$this->skins->cscmsumenu($Mark_Text,$_SESSION['cscms__id']);
            $Mark_Text=$this->skins->labelif($Mark_Text);
			echo $Mark_Text;
	}

	//上传歌曲
	public function add()
	{
			//模板
			$tpl='add.html';
			//URL地址
		    $url='dance/add';
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$_SESSION['cscms__id']);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];

			//检测发表权限
			$rowz=$this->CsdjDB->get_row('userzu','aid,sid',$row['zid']);
			if(!$rowz || $rowz->aid==0){
                 msg_url('您所在会员组没有权限发表歌曲~!','javascript:history.back();');
			}
			
			//装载模板
			$title='上传歌曲 - 会员中心';
			$ids['uid']=$_SESSION['cscms__id'];
			$ids['uida']=$_SESSION['cscms__id'];
            $Mark_Text=$this->CsdjTpl->user_list($row,$url,1,$tpl,$title,'','',$ids,true,false);
			//token
			$token=random_string('alnum',10);
			$_SESSION['token']=$token;
			$Mark_Text=str_replace("[user:token]",$token,$Mark_Text);
			//提交地址
			$Mark_Text=str_replace("[user:dancesave]",spacelink('dance,save','dance'),$Mark_Text);
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
			if(!$rowu || $rowu->aid==0){
                 msg_url('您所在会员组没有权限发表歌曲~!','javascript:history.back();');
			}
			//检测发表数据是否需要审核
			$music['yid']=($rowu->sid==1)?0:1;

            //必填字段
			$music['name']=$this->input->post('name', TRUE, TRUE);
			$music['cid']=intval($this->input->post('cid'));
			$music['purl']=$this->input->post('purl', TRUE, TRUE);

            //检测必须字段
			if($music['cid']==0) msg_url('请选择歌曲分类~!','javascript:history.back();');
			if(empty($music['name'])) msg_url('歌曲名称不能为空~!','javascript:history.back();');
			if(empty($music['purl'])) msg_url('歌曲地址不能为空~!','javascript:history.back();');

			//选填字段
            if(substr($music['purl'],0,7)!='http://'){
			    $music['purl']=str_replace('//','/','/attachment/music/'.$music['purl']);
			}
			$music['tid']=intval($this->input->post('tid'));
			$music['cion']=intval($this->input->post('cion'));
			$music['text']=str_replace("\r\n","<br>",str_encode($_POST['text']));
			$music['lrc']=$this->input->post('lrc', TRUE, TRUE);
			$music['pic']=$this->input->post('pic', TRUE, TRUE);
			$music['tags']=$this->input->post('tags', TRUE, TRUE);
			$music['zc']=$this->input->post('zc', TRUE, TRUE);
			$music['zq']=$this->input->post('zq', TRUE, TRUE);
			$music['bq']=$this->input->post('bq', TRUE, TRUE);
			$music['hy']=$this->input->post('hy', TRUE, TRUE);
			$music['durl']=$music['purl'];
			$music['uid']=$_SESSION['cscms__id'];
			$music['addtime']=time();

			$singer=$this->input->post('singer', TRUE, TRUE);
			//判断歌手是否存在
			if(!empty($singer)){
			     $row=$this->CsdjDB->get_row('singer','id',$singer,'name');
				 if($row){
                       $music['singerid']=$row->id;
				 }
			}
			//获取大小、音质、时长
			if(substr($music['purl'],0,7)!='http://' && UP_Mode==1){
				 if(UP_Pan==''){
                      $params['filename']=FCPATH.$music['purl'];
				 }else{
                      $params['filename']=UP_Pan.$music['purl'];
				 }
                 $this->load->library('mp3file',$params);
		         $arr = $this->mp3file->get_metadata();
				 $music['dx']=!empty($arr['Filesize'])?formatsize($arr['Filesize']):'';
                 $music['yz']=!empty($arr['Bitrate'])?$arr['Bitrate'].' Kbps':'';
                 $music['sc']=!empty($arr['Length mm:ss'])?$arr['Length mm:ss']:'';
			}
            //增加到数据库
            $did=$this->CsdjDB->get_insert('dance',$music);
			if(intval($did)==0){
				 msg_url('歌曲发布失败，请稍候再试~!','javascript:history.back();');
			}

            //摧毁token
            unset($_SESSION['token']);

			//增加动态
		    $dt['dir'] = 'dance';
		    $dt['uid'] = $_SESSION['cscms__id'];
		    $dt['did'] = $did;
		    $dt['yid'] = $music['yid'];
		    $dt['title'] = '发布了歌曲';
		    $dt['name'] = $music['name'];
		    $dt['link'] = linkurl('play','id',$did,1,'dance');
		    $dt['addtime'] = time();
            $this->CsdjDB->get_insert('dt',$dt);

			//如果免审核，则给会员增加相应金币、积分
			if($music['yid']==0){
			     $addhits=getzd('user','addhits',$_SESSION['cscms__id']);
				 if($addhits<User_Nums_Add){
                     $this->db->query("update ".CS_SqlPrefix."user set cion=cion+".User_Cion_Add.",jinyan=jinyan+".User_Jinyan_Add.",addhits=addhits+1 where id=".$_SESSION['cscms__id']."");
				 }
				 msg_url('恭喜您，歌曲发布成功~!',spacelink('dance','dance'));
			}else{
				 msg_url('恭喜您，歌曲发布成功,请等待管理员审核~!',spacelink('dance/verify','dance'));
			}
	}
}
