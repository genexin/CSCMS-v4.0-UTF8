<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-16
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Gbook extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjTpl');
		    $this->load->model('CsdjUser');
		    $this->lang->load('home');
    	    if(!defined('HOMEPATH')) define('HOMEPATH', 'home');
	}

	public function index()
	{
			//模板
			$tpl='gbook.html';
			//当前会员
			$uid=get_home_uid();
		    $row=$this->CsdjDB->get_row_arr('user','*',$uid);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
			//装载模板
			$title=$row['nichen'].L('gbook_01');
			$ids['uid']=$row['id'];
			$ids['uida']=$row['id'];
            $this->CsdjTpl->home_list($row,'gbook',1,$tpl,$title,$ids);
	}

	public function ajax()
	{
		    $callback = $this->input->get('callback',true);
			$uid=intval($this->uri->segment(4));
			$page=intval($this->uri->segment(5));
			$data=$data_content=$aliasname='';
			//模板
			$tpl='gbook-ajax.html';
			//URL地址
		    $url='gbook/ajax/'.$uid;
			//当前会员
		    $row=$this->CsdjDB->get_row_arr('user','*',$uid);
			if(empty($row['nichen'])) $row['nichen']=$row['name'];
            //装载模板
			$sid=defined('PLUBPATH')?1:0;
			$this->load->get_templates('home',$sid,$row['skins']);
		    $Mark_Text=$this->load->view($tpl,'',true);
			$Mark_Text=$this->skins->cscms_common($Mark_Text,$row['skins']);
		    //预先除了分页
		    $pagenum=getpagenum($Mark_Text);
		    preg_match_all('/{cscms:([\S]+)\s+(.*?pagesize=\"([\S]+)\".*?)}([\s\S]+?){\/cscms:\1}/',$Mark_Text,$page_arr);
		    if(!empty($page_arr) && !empty($page_arr[2])){

					       $field=$page_arr[1][0]; //前缀名
				           //组装SQL数据
						   $arr['uid']=$row['id'];
			               $arr['uida']=$row['id'];
					       $sqlstr=$this->skins->cscms_sql($page_arr[1][0],$page_arr[2][0],$page_arr[0][0],$page_arr[3][0],'id',$arr,'');
					       $nums=$this->db->query($sqlstr)->num_rows(); //总数量
					       $Arr=spanajaxpage($sqlstr,$nums,$page_arr[3][0],$pagenum,'cscms_home_gbook',$page);
			               if($nums==0){
				                $data_content.="";
			               }else{
					            $sorti=1;
							    $result_array=$this->db->query($Arr[0])->result_array();
					            foreach ($result_array as $row2) {
						             $datatmp=$this->skins->cscms_skins($field,$page_arr[0][0],$page_arr[4][0],$row2,$sorti);
						             $sorti++;
						             $data_content.=$datatmp;
					            }
			               }
			               $Mark_Text=page_mark($Mark_Text,$Arr);	//分页解析
			               $Mark_Text=str_replace($page_arr[0][0],$data_content,$Mark_Text);
			}
		    unset($page_arr);
			//Token
			$this->load->helper('string');
			$token=random_string('alnum',10);
			$_SESSION['gbooktoken']=$token;
			$Mark_Text=str_replace("[gbook:token]",$token,$Mark_Text);
			//表情
            $plfaces="";
    	    for($i=1;$i<=56;$i++){
                 $plfaces.="<img style='cursor:pointer;' src=\"".Web_Path."packs/images/faces/e".$i.".gif\" onclick=\"$('#cscms_gbook_content').val($('#cscms_gbook_content').val()+'[em:".$i."]');$('#cscms_faces').hide();\" />";
            }
			$Mark_Text=str_replace("[gbook:faces]",$plfaces,$Mark_Text);
            $Mark_Text=$this->skins->template_parse($Mark_Text,false);
			$Mark_Text=get_bm($Mark_Text,'gbk','utf-8');
			echo $callback."({str:".json_encode($Mark_Text)."})";
	}

    //新增留言
	public function add()
	{
		    $callback = $this->input->get('callback',true);
			$token=$this->input->get_post('token', TRUE);
			$add['uida']=(int)$this->input->get_post('uid', TRUE);
			$add['neir']=$this->input->get_post('neir', TRUE);
			$add['neir']=facehtml(filter(get_bm($add['neir'])));
			//转化回复
            preg_match_all ('/'.L('gbook_02').'@(.*)@:/i',$add['neir'],$bs);
            if(!empty($bs[0][0]) && !empty($bs[1][0])){
				  $uid=getzd('user','id',$bs[1][0],'name');
				  $nichen=getzd('user','nichen',$bs[1][0],'name');
				  $ulink=userlink('index',$uid,$bs[1][0]);
				  if(empty($nichen)) $nichen=$bs[1][0];
				  $b=L('gbook_02').'<a target="_blank" href="'.$ulink.'">@'.$nichen.'@</a>:';
                  $add['neir']=str_replace($bs[0][0],$b,$add['neir']);
			}
            unset($bs);
			if($add['uida']==0){
                 $error='10000';
			}elseif(!isset($_SESSION['gbooktoken']) || $token!=$_SESSION['gbooktoken']){
                 $error='10001';
			}elseif(isset($_SESSION['gbookaddtime']) && time()<$_SESSION['gbookaddtime']+30){
                 $error='10006';
			}elseif(empty($add['neir'])){
                 $error='10002';
			}elseif(empty($_SESSION['cscms__id'])){
                 $error='10003';
			}else{

                $add['uidb']=$_SESSION['cscms__id'];
			    $add['fid']=intval($this->input->get_post('fid'));
			    $add['ip']=getip();
			    $add['addtime']=time();

                $ids=$this->CsdjDB->get_insert('gbook',$add);
			    if(intval($ids)==0){
                    $error='10004'; //失败
				}else{
                    //摧毁token
			        unset($_SESSION['token']);
                    $error='10005';
                    $_SESSION['gbookaddtime']=time();

					//发送通知
				    $addm['uida']=$add['uida'];
				    $addm['uidb']=$_SESSION['cscms__id'];
				    $addm['name']=L('gbook_03');
				    $addm['neir']=vsprintf(L('ajax_04'),array($_SESSION['cscms__name']));
				    $addm['addtime']=time();
            	    $this->CsdjDB->get_insert('msg',$addm);
				}
			}
			echo $callback."({error:".$error."})";
	}

    //删除留言
	public function del()
	{
		    $callback = $this->input->get('callback',true);
			$id = intval($this->input->get_post('id'));
			$token=$this->input->get_post('token', TRUE);
			if($id==0){
                    $error='10001';
			}elseif(!isset($_SESSION['gbooktoken']) || $token!=$_SESSION['gbooktoken']){
                 $error='10002';
			}elseif(empty($_SESSION['cscms__id'])){ //未登陆
                    $error='10000';
			}else{
		            $row=$this->CsdjDB->get_row('gbook','uida,uidb',$id);
		            if(!$row){
                         $error='10001';
					}elseif($row->uida!=$_SESSION['cscms__id'] && $row->uidb!=$_SESSION['cscms__id']){
                         $error='10003';
					}else{
                         //权限通过删除
						 $this->CsdjDB->get_del('gbook',$id,'fid');
						 $this->CsdjDB->get_del('gbook',$id);
                         $error='10004';
					}
			}
			echo $callback."({error:".$error."})";
	}
}
