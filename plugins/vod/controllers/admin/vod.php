<?php if ( ! defined('IS_ADMIN')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-03
 */
class Vod extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->helper('vod');
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
	}

	public function index()
	{
            $yuyan = $this->input->get_post('yuyan',true);
            $diqu = $this->input->get_post('diqu',true);
            $sort = $this->input->get_post('sort',true);
            $desc = $this->input->get_post('desc',true);
            $form = $this->input->get_post('form',true);
            $cid  = intval($this->input->get_post('cid'));
            $yid  = intval($this->input->get_post('yid'));
			$remark = intval($this->input->get_post('remark'));
			$reco = intval($this->input->get_post('reco'));
            $zd   = $this->input->get_post('zd',true);
            $key  = $this->input->get_post('key',true);
 	        $page = intval($this->input->get('page'));
            if($page==0) $page=1;

	        $data['page'] = $page;
	        $data['sort'] = $sort;
	        $data['cid'] = $cid;
	        $data['yid'] = $yid;
	        $data['zd'] = $zd;
	        $data['key'] = $key;
	        $data['diqu'] = $diqu;
	        $data['yuyan'] = $yuyan;
	        $data['remark'] = $remark;
	        $data['reco'] = $reco;
	        $data['form'] = $form;
			if(empty($sort)) $sort="addtime";

            $sql_string = "SELECT id,name,pic,remark,hits,hid,reco,cid,yid,addtime FROM ".CS_SqlPrefix."vod where 1=1";
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
			if(!empty($key)){
				 $sql_string.= " and ".$zd." like '%".$key."%'";
			}
			if(!empty($yuyan)){
	             $sql_string.= " and yuyan='".$yuyan."'";
			}
			if(!empty($diqu)){
	             $sql_string.= " and diqu='".$diqu."'";
			}
			if($remark==1){
	             $sql_string.= " and (remark='' or remark='完结')";
			}
			if($remark==2){
	             $sql_string.= " and remark!='' and remark!='完结'";
			}
			if($reco>0){
	             $sql_string.= " and reco=".$reco."";
			}
			if(!empty($form)){
				 $sql_string.= " and purl like '%$".$form."%'";
			}
	        $sql_string.= " order by ".$sort." desc";
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('vod/admin/vod')."?yid=".$yid."&remark=".$remark."&zd=".$zd."&yuyan=".$yuyan."&diqu=".$diqu."&key=".$key."&cid=".$cid."&form=".$form."&sort=".$sort."&reco=".$reco;
	        $per_page = 15; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['vod'] = $query->result();
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类

            $this->load->view('vod.html',$data);
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
				  $str=($sid==0)?'<a title="点击锁定" href="javascript:get_cmd(\''.site_url('vod/admin/vod/init').'?sid=1\',\'zt\','.$id.');"><img align="absmiddle" src="'.Web_Path.'packs/admin/images/icon/ok.gif" /></a>':'<a title="点击解除锁定" href="javascript:get_cmd(\''.site_url('vod/admin/vod/init').'?sid=0\',\'zt\','.$id.');"><img align="absmiddle" src="'.Web_Path.'packs/admin/images/icon/no.gif" /></a>';
			}elseif($ac=='tj'){  //推荐
                  $edit['reco']=$sid;
		          $str='<a title="点击取消推荐" href="javascript:get_cmd(\''.site_url('vod/admin/vod/init').'?sid=0\',\'tj\','.$id.');"><font color="#ff0033">×</font></a>';
                  for($i=1;$i<=$sid;$i++){
                      $str.='<a title="推荐：'.$i.'星" href="javascript:get_cmd(\''.site_url('vod/admin/vod/init').'?sid='.$i.'\',\'tj\','.$id.');">★</a>';
                  }
                  for($j=$sid+1;$j<=5;$j++){
                      $str.='<a title="推荐：'.$j.'星" href="javascript:get_cmd(\''.site_url('vod/admin/vod/init').'?sid='.$j.'\',\'tj\','.$id.');">☆</a>';
                  }
			}
            $this->CsdjDB->get_update('vod',$id,$edit);
            die($str);
	}

    //视频分类剧情
	public function type_init()
	{
            $id   = intval($this->input->get('id'));
	        $rowc = $this->db->query("SELECT fid FROM ".CS_SqlPrefix."vod_list where id=".$id."")->row();
			if($rowc->fid>0) $id=$rowc->fid;
            $type   = $this->input->get('type',true);
            $sql_string = "SELECT id,name FROM ".CS_SqlPrefix."vod_type where cid=".$id." order by xid asc";
	        $query = $this->db->query($sql_string);
			$data=array();
			$i=0;
			foreach ($query->result() as $row) {
                   $data[$i]['name']=get_bm($row->name,'gbk','utf-8');
                   $data[$i]['chk']=(getqx($row->name,$type)=='ok')?'true':'false';
            $i++;
			}
			echo json_encode($data);            
	}

    //视频新增、修改
	public function edit()
	{
            $id   = intval($this->input->get('id'));
			if($id==0){
                $data['id']=0;
                $data['cid']=0;
                $data['yid']=0;
                $data['tid']=0;
                $data['reco']=0;
                $data['diqu']='';
                $data['yuyan']='';
                $data['uid']=0;
                $data['name']='';
                $data['type']='';
                $data['pic']='';
                $data['info']='';
                $data['color']='';
                $data['bname']='';
                $data['remark']='完结';
                $data['year']=date('Y');
                $data['cion']=0;
                $data['dcion']=0;
                $data['vip']=0;
                $data['level']=0;
                $data['zhuyan']='';
                $data['daoyan']='';
                $data['tags']='';
                $data['pic2']='';
                $data['phits']=0;
                $data['pfen']=0;
                $data['hits']=0;
                $data['yhits']=0;
                $data['zhits']=0;
                $data['rhits']=0;
                $data['shits']=0;
                $data['xhits']=0;
                $data['dhits']=0;
                $data['chits']=0;
                $data['singerid']=0;
                $data['purl']='';
                $data['durl']='';
                $data['text']='';
                $data['skins']='';
                $data['title']='';
                $data['keywords']='';
                $data['description']='';
			}else{
                $row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."vod where id=".$id."")->row(); 
			    if(!$row) admin_msg('该条记录不存在~!','javascript:history.back();','no');  //记录不存在

                $data['id']=$row->id;
                $data['cid']=$row->cid;
                $data['yid']=$row->yid;
                $data['tid']=$row->tid;
                $data['reco']=$row->reco;
                $data['diqu']=$row->diqu;
                $data['yuyan']=$row->yuyan;
                $data['uid']=$row->uid;
                $data['name']=$row->name;
                $data['type']=$row->type;
                $data['pic']=$row->pic;
                $data['info']=$row->info;
                $data['color']=$row->color;
                $data['bname']=$row->bname;
                $data['remark']=$row->remark;
                $data['year']=$row->year;
                $data['cion']=$row->cion;
                $data['dcion']=$row->dcion;
                $data['vip']=$row->vip;
                $data['level']=$row->level;
                $data['zhuyan']=$row->zhuyan;
                $data['daoyan']=$row->daoyan;
                $data['tags']=$row->tags;
                $data['pic2']=$row->pic2;
                $data['phits']=$row->phits;
                $data['pfen']=$row->pfen;
                $data['hits']=$row->hits;
                $data['yhits']=$row->yhits;
                $data['zhits']=$row->zhits;
                $data['rhits']=$row->rhits;
                $data['shits']=$row->shits;
                $data['xhits']=$row->xhits;
                $data['dhits']=$row->dhits;
                $data['chits']=$row->chits;
                $data['singerid']=$row->singerid;
                $data['purl']=$row->purl;
                $data['durl']=$row->durl;
                $data['text']=$row->text;
                $data['skins']=$row->skins;
                $data['title']=$row->title;
                $data['keywords']=$row->keywords;
                $data['description']=$row->description;
			}
            $this->load->view('vod_edit.html',$data);
	}

    //视频保存
	public function save()
	{
            $id   = intval($this->input->post('id'));
            $name = $this->input->post('name',true);
            $text = remove_xss($this->input->post('text'));
            $user = $this->input->post('user',true);
			$type = $this->input->post('type',true);
			$tags = $this->input->post('tags',true);
			$purl = $this->input->post('purl');
			$durl = $this->input->post('durl');
			$singer = $this->input->post('singer',true);
            $addtime = $this->input->post('addtime',true);
			$playform = $this->input->post('playform',true);
			$downform = $this->input->post('downform',true);
            $data['cid']=intval($this->input->post('cid'));

            if(empty($name)||empty($data['cid'])){
                   admin_msg('抱歉，视频名称、分类不能为空~!','javascript:history.back();','no');
			}

			//自动获取TAGS标签
            if(empty($tags)){
			     $tags = gettag($name,$text);
			}

			//试听地址
			$playurl='';
			if(!empty($purl)){
				$k=0;
				$purl=str_replace("\r","",$purl);
                foreach ($purl as $value) {
                     $All=explode("\n",$value);
                     for($j=0;$j<count($All);$j++){	
						 if(!empty($All[$j])){
                             if(strpos($All[$j],'$') === FALSE){
                                 if(($j+1)==count($All)){
                                      $playurl.="第".($j+1)."集$".$All[$j]."$".$playform[$k];
                                 }else{
                                      $playurl.="第".($j+1)."集$".$All[$j]."$".$playform[$k]."\n";
                                 }
                             }else{
                                 if(($j+1)==count($All)){
                                      $playurl.=$All[$j];
                                 }else{
                                      $playurl.=$All[$j]."\n";
                                 }
							 }
						 }
					 }
					 $playurl.="#cscms#";
					 $k++;
				}
                $playurl=str_replace("\n\n","\n",$playurl);
			}

			//下载地址
			$downurl='';
			if(!empty($durl)){
				$k=0;
				$durl=str_replace("\r","",$durl);
                foreach ($durl as $value) {
                     $All=explode("\n",$value);
                     for($j=0;$j<count($All);$j++){	
						 if(!empty($All[$j])){
                             if(strpos($All[$j],'$') === FALSE){
                                 if(($j+1)==count($All)){
                                      $downurl.="第".($j+1)."集$".$All[$j]."$".$playform[$k];
                                 }else{
                                      $downurl.="第".($j+1)."集$".$All[$j]."$".$playform[$k]."\n";
                                 }
                             }else{
                                 if(($j+1)==count($All)){
                                      $downurl.=$All[$j];
                                 }else{
                                      $downurl.=$All[$j]."\n";
                                 }
							 }
						 }
					 }
					 $downurl.="#cscms#";
					 $k++;
				}
                $downurl=str_replace("\n\n","\n",$downurl);
			}

			//判断歌手
			if($this->db->table_exists(CS_SqlPrefix.'singer') && !empty($singer)){  //歌手表存在
                 $data['singerid']=intval(getzd('singer','id',$singer,'name'));
			}

            $data['yid']=intval($this->input->post('yid'));
            $data['tid']=intval($this->input->post('tid'));
            $data['reco']=intval($this->input->post('reco'));
            $data['diqu']=$this->input->post('diqu',true);
            $data['yuyan']=$this->input->post('yuyan',true);
            $data['uid']=intval(getzd('user','id',$user,'name'));
            $data['name']=$name;
            $data['type']=!empty($type)?implode(',',$type):'';
            $data['pic']=$this->input->post('pic',true);
            $data['info']=$this->input->post('info',true);
            $data['color']=$this->input->post('color',true);
            $data['bname']=$this->input->post('bname',true);
            $data['remark']=$this->input->post('remark',true);
            $data['year']=$this->input->post('year',true);
			if(empty($data['year'])) $data['year']=date('Y');
            $data['cion']=intval($this->input->post('cion'));
            $data['dcion']=intval($this->input->post('dcion'));
            $data['vip']=intval($this->input->post('vip'));
            $data['level']=intval($this->input->post('level'));
            $data['zhuyan']=$this->input->post('zhuyan',true);
            $data['daoyan']=$this->input->post('daoyan',true);
            $data['tags']=$tags;
            $data['pic2']=$this->input->post('pic2',true);
            $data['phits']=intval($this->input->post('phits'));
            $data['pfen']=intval($this->input->post('pfen'));
            $data['hits']=intval($this->input->post('hits'));
            $data['yhits']=intval($this->input->post('yhits'));
            $data['zhits']=intval($this->input->post('zhits'));
            $data['rhits']=intval($this->input->post('rhits'));
            $data['shits']=intval($this->input->post('shits'));
            $data['xhits']=intval($this->input->post('xhits'));
            $data['dhits']=intval($this->input->post('dhits'));
            $data['chits']=intval($this->input->post('chits'));
            $data['purl']=trim(substr($playurl,0,-7));
            $data['durl']=trim(substr($downurl,0,-7));
            $data['text']=$text;
            $data['skins']=$this->input->post('skins',true);
            $data['title']=$this->input->post('title',true);
            $data['keywords']=$this->input->post('keywords',true);
            $data['description']=$this->input->post('description',true);

			if($id==0){ //新增
				 $data['addtime']=time();
                 $this->CsdjDB->get_insert('vod',$data);
			}else{
				 if($data['yid']==0) $this->dt($id);
			     if($addtime=='ok') $data['addtime']=time();
                 $this->CsdjDB->get_update('vod',$id,$data);
			}
            admin_msg('恭喜您，操作成功~!',site_url('vod/admin/vod'),'ok');  //操作成功
	}

    //视频地址采集
	public function caiji()
	{
		    $vodurl = $this->input->get('vodurl',true);
			if(empty($vodurl)){
                  $data['id']   = intval($this->input->get_post('id'));
                  $data['sid']   = intval($this->input->get_post('sid'));
			      $data['check'] =($data['id']==1)?'checked':'';
                  $this->load->view('vod_caiji.html',$data);
			}else{
		          echo caiji($vodurl);
			}
	}

    //视频删除
	public function del()
	{
            $yid = intval($this->input->get('yid'));
            $ids = $this->input->get_post('id');
            $ac = $this->input->get_post('ac');
			//回收站
			if($ac=='hui'){
			     $result=$this->db->query("SELECT id,pic,pic2 FROM ".CS_SqlPrefix."vod where hid=1")->result();
			     $this->load->library('csup');
			     foreach ($result as $row) {
                     if(!empty($row->pic)){
					    $this->csup->del($row->pic,'vod'); //删除图片
				     }
                     if(!empty($row->pic2)){
					    $this->csup->del($row->pic2,'vod'); //删除幻灯图
				     }
					 $this->CsdjDB->get_del('vod',$row->id);
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
			     $result=$this->db->query("SELECT pic,pic2 FROM ".CS_SqlPrefix."vod where id in(".$idss.")")->result();
			     $this->load->library('csup');
			     foreach ($result as $row) {
                     if(!empty($row->pic)){
					    $this->csup->del($row->pic,'vod'); //删除图片
				     }
                     if(!empty($row->pic2)){
					    $this->csup->del($row->pic2,'vod'); //删除幻灯图
				     }
				 }
			     $this->CsdjDB->get_del('vod',$ids);
                 admin_msg('恭喜您，删除成功~!','javascript:history.back();','ok');  //操作成功
			}else{
				 $data['hid']=1;
			     $this->CsdjDB->get_update('vod',$ids,$data);
                 admin_msg('恭喜您，数据已经移动至回收站~!','javascript:history.back();','ok');  //操作成功
			}
	}

    //视频还原
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
			$this->CsdjDB->get_update('vod',$ids,$data);
            admin_msg('恭喜您，数据还原成功~!','javascript:history.back();','ok');  //操作成功
	}

    //视频批量
	public function pledit()
	{
            $data['id'] = $this->input->get_post('id');
			$this->load->view('pl_edit.html',$data);
	}

    //批量修改操作
	public function pl_save()
	{
            $xid=intval($this->input->post('xid'));
            $csid=$this->input->post('csid');
            $id=$this->input->post('id',true);
		    $cids=intval($this->input->post('cids'));

		    $cid=intval($this->input->post('cid'));
		    $hid=intval($this->input->post('hid'));
		    $tid=intval($this->input->post('tid'));
		    $yid=intval($this->input->post('yid'));
		    $singer=$this->input->post('singer',true);
		    $user=$this->input->post('user',true);
		    $reco=intval($this->input->post('reco'));
		    $cion=intval($this->input->post('cion'));
		    $dcion=intval($this->input->post('dcion'));
		    $vip=intval($this->input->post('vip'));
		    $level=intval($this->input->post('level'));
		    $hits=intval($this->input->post('hits'));
		    $yhits=intval($this->input->post('yhits'));
		    $zhits=intval($this->input->post('zhits'));
		    $rhits=intval($this->input->post('rhits'));
		    $xhits=intval($this->input->post('xhits'));
		    $shits=intval($this->input->post('shits'));

            if(empty($csid)) admin_msg('请选择要操作的数据~!','javascript:history.back();','no');

            if($xid==1){  //按ID操作
				   if(empty($id)) admin_msg('请选择要操作的视频ID~!','javascript:history.back();','no');
                   foreach ($csid as $v) {
                          if($v=="cid"){
					          $this->db->query("update ".CS_SqlPrefix."vod set cid=".$cid." where id in (".$id.")");
                          }elseif($v=="yid"){
				              if($yid==0) $this->dt($id);
					          $this->db->query("update ".CS_SqlPrefix."vod set yid=".$yid." where id in (".$id.")");
                          }elseif($v=="tid"){
					          $this->db->query("update ".CS_SqlPrefix."vod set tid=".$tid." where id in (".$id.")");
                          }elseif($v=="reco"){
					          $this->db->query("update ".CS_SqlPrefix."vod set reco=".$reco." where id in (".$id.")");
                          }elseif($v=="cion"){
					          $this->db->query("update ".CS_SqlPrefix."vod set cion=".$cion." where id in (".$id.")");
                          }elseif($v=="dcion"){
					          $this->db->query("update ".CS_SqlPrefix."vod set dcion=".$dcion." where id in (".$id.")");
                          }elseif($v=="vip"){
					          $this->db->query("update ".CS_SqlPrefix."vod set vip=".$vip." where id in (".$id.")");
                          }elseif($v=="level"){
					          $this->db->query("update ".CS_SqlPrefix."vod set level=".$level." where id in (".$id.")");
                          }elseif($v=="hits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set hits=".$hits." where id in (".$id.")");
                          }elseif($v=="yhits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set yhits=".$yhits." where id in (".$id.")");
                          }elseif($v=="zhits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set zhits=".$zhits." where id in (".$id.")");
                          }elseif($v=="rhits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set rhits=".$rhits." where id in (".$id.")");
                          }elseif($v=="shits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set shits=".$shits." where id in (".$id.")");
                          }elseif($v=="xhits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set xhits=".$xhits." where id in (".$id.")");
                          }elseif($v=="user"){
					          $uid=intval(getzd('user','id',$user,'name'));
					          $this->db->query("update ".CS_SqlPrefix."vod set uid=".$uid." where id in (".$id.")");
				          }elseif($v=="singer"){
					          $singerid=intval(getzd('singer','id',$singer,'name'));
					          $this->db->query("update ".CS_SqlPrefix."vod set singerid=".$singerid." where id in (".$id.")");
				          }elseif($v=="hid"){
					          if($hid==2){
                                  $this->CsdjDB->get_del('vod',$id);
					          }else{
					              $this->db->query("update ".CS_SqlPrefix."vod set hid=".$hid." where id in (".$id.")");
					          }
				          }
				   }
			}else{ //按分类操作
				   if(empty($cids)) admin_msg('请选择要操作的视频分类~!','javascript:history.back();','no');
                   foreach ($csid as $v) {
                          if($v=="cid"){
					          $this->db->query("update ".CS_SqlPrefix."vod set cid=".$cid." where cid in (".$cids.")");
                          }elseif($v=="yid"){
				              if($yid==0) $this->dt($id);
					          $this->db->query("update ".CS_SqlPrefix."vod set yid=".$yid." where cid in (".$cids.")");
                          }elseif($v=="tid"){
					          $this->db->query("update ".CS_SqlPrefix."vod set tid=".$tid." where cid in (".$cids.")");
                          }elseif($v=="reco"){
					          $this->db->query("update ".CS_SqlPrefix."vod set reco=".$reco." where cid in (".$cids.")");
                          }elseif($v=="cion"){
					          $this->db->query("update ".CS_SqlPrefix."vod set cion=".$cion." where cid in (".$cids.")");
                          }elseif($v=="dcion"){
					          $this->db->query("update ".CS_SqlPrefix."vod set dcion=".$dcion." where cid in (".$cids.")");
                          }elseif($v=="vip"){
					          $this->db->query("update ".CS_SqlPrefix."vod set vip=".$vip." where cid in (".$cids.")");
                          }elseif($v=="level"){
					          $this->db->query("update ".CS_SqlPrefix."vod set level=".$level." where cid in (".$cids.")");
                          }elseif($v=="hits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set hits=".$hits." where cid in (".$cids.")");
                          }elseif($v=="yhits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set yhits=".$yhits." where cid in (".$cids.")");
                          }elseif($v=="zhits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set zhits=".$zhits." where cid in (".$cids.")");
                          }elseif($v=="rhits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set rhits=".$rhits." where cid in (".$cids.")");
                          }elseif($v=="shits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set shits=".$shits." where cid in (".$cids.")");
                          }elseif($v=="xhits"){
					          $this->db->query("update ".CS_SqlPrefix."vod set xhits=".$xhits." where cid in (".$cids.")");
                          }elseif($v=="user"){
					          $uid=intval(getzd('user','id',$user,'name'));
					          $this->db->query("update ".CS_SqlPrefix."vod set uid=".$uid." where cid in (".$cids.")");
				          }elseif($v=="singer"){
					          $singerid=intval(getzd('singer','id',$singer,'name'));
					          $this->db->query("update ".CS_SqlPrefix."vod set singerid=".$singerid." where cid in (".$cids.")");
				          }elseif($v=="hid"){
					          if($hid==2){
                                  $this->CsdjDB->get_del('vod',$cids);
					          }else{
					              $this->db->query("update ".CS_SqlPrefix."vod set hid=".$hid." where cid in (".$cids.")");
					          }
				          }
				   }
			}
			exit('<script type="text/javascript">
			parent.location.href=parent.location.href;
			parent.tip_cokes();
			</script>');  //操作成功
	}

	//审核增加积分、经验、同时动态显示
	public function dt($id)
	{
			$dt=$this->db->query("SELECT id,name FROM ".CS_SqlPrefix."dt where link='".linkurl('show','id',$id,1,'vod')."' and yid=1")->row();
			if($dt){
                  $uid=getzd('vod','uid',$id);
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
				  $add['name']='视频审核通知';
				  $add['neir']='恭喜您，您的视频《'.$dt->name.'》已经审核通过，'.$str.'感谢您的支持~~';
				  $add['addtime']=time();
            	  $this->CsdjDB->get_insert('msg',$add);
			}
	}
}