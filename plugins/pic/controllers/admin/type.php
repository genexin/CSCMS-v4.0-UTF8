<?php if ( ! defined('IS_ADMIN')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-02-25
 */
class Type extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->helper('pic');
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
	}

	public function index()
	{
            $sort = $this->input->get_post('sort',true);
            $desc = $this->input->get_post('desc',true);
            $cid  = intval($this->input->get_post('cid'));
            $yid  = intval($this->input->get_post('yid'));
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
	        $data['reco'] = $reco;
			if(empty($sort)) $sort="addtime";

            $sql_string = "SELECT id,name,pic,hits,rhits,hid,reco,singerid,uid,cid,yid,addtime FROM ".CS_SqlPrefix."pic_type where 1=1";
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
				if($zd=='user'){
					$uid=getzd('user','id',$key,'name');
				    $sql_string.= " and uid='".intval($uid)."'";
				}elseif($zd=='singer'){
					$singerid=getzd('singer','id',$key,'name');
				    $sql_string.= " and singerid='".intval($singerid)."'";
				}elseif($zd=='id'){
				    $sql_string.= " and id='".intval($key)."'";
				}else{
				    $sql_string.= " and ".$zd." like '%".$key."%'";
				}
			}
			if($reco>0){
	             $sql_string.= " and reco=".$reco."";
			}
	        $sql_string.= " order by ".$sort." desc";
			//exit($sql_string);
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('pic/admin/type')."?yid=".$yid."&zd=".$zd."&key=".$key."&cid=".$cid."&sort=".$sort."&reco=".$reco;
	        $per_page = 8; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['pic'] = $query->result();
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类

            $this->load->view('pic_type.html',$data);
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
				  $str=($sid==0)?'<a title="点击锁定" href="javascript:get_cmd(\''.site_url('dance/admin/dance/init').'?sid=1\',\'zt\','.$id.');"><img align="absmiddle" src="'.Web_Path.'packs/admin/images/icon/ok.gif" /></a>':'<a title="点击解除锁定" href="javascript:get_cmd(\''.site_url('dance/admin/dance/init').'?sid=0\',\'zt\','.$id.');"><img align="absmiddle" src="'.Web_Path.'packs/admin/images/icon/no.gif" /></a>';
			}elseif($ac=='tj'){  //推荐
                  $edit['reco']=$sid;
		          $str='<a title="点击取消推荐" href="javascript:get_cmd(\''.site_url('dance/admin/dance/init').'?sid=0\',\'tj\','.$id.');"><font color="#ff0033">×</font></a>';
                  for($i=1;$i<=$sid;$i++){
                      $str.='<a title="推荐：'.$i.'星" href="javascript:get_cmd(\''.site_url('dance/admin/dance/init').'?sid='.$i.'\',\'tj\','.$id.');">★</a>';
                  }
                  for($j=$sid+1;$j<=5;$j++){
                      $str.='<a title="推荐：'.$j.'星" href="javascript:get_cmd(\''.site_url('dance/admin/dance/init').'?sid='.$j.'\',\'tj\','.$id.');">☆</a>';
                  }
			}
            $this->CsdjDB->get_update('pic_type',$id,$edit);
            die($str);
	}

    //相册新增、修改
	public function edit()
	{
            $id   = intval($this->input->get('id'));
			if($id==0){
                $data['id']=0;
                $data['cid']=0;
                $data['yid']=0;
                $data['reco']=0;
                $data['uid']=0;
                $data['name']='';
                $data['bname']='';
                $data['pic']='';
                $data['tags']='';
                $data['hits']=0;
                $data['yhits']=0;
                $data['zhits']=0;
                $data['rhits']=0;
                $data['dhits']=0;
                $data['chits']=0;
                $data['singerid']=0;
                $data['skins']='show.html';
                $data['title']='';
                $data['keywords']='';
                $data['description']='';
			}else{
                $row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."pic_type where id=".$id."")->row(); 
			    if(!$row) admin_msg('该条记录不存在~!','javascript:history.back();','no');  //记录不存在

                $data['id']=$row->id;
                $data['cid']=$row->cid;
                $data['yid']=$row->yid;
                $data['reco']=$row->reco;
                $data['uid']=$row->uid;
                $data['name']=$row->name;
                $data['bname']=$row->bname;
                $data['pic']=$row->pic;
                $data['tags']=$row->tags;
                $data['hits']=$row->hits;
                $data['yhits']=$row->yhits;
                $data['zhits']=$row->zhits;
                $data['rhits']=$row->rhits;
                $data['dhits']=$row->dhits;
                $data['chits']=$row->chits;
                $data['singerid']=$row->singerid;
                $data['skins']=$row->skins;
                $data['title']=$row->title;
                $data['keywords']=$row->keywords;
                $data['description']=$row->description;
			}
            $this->load->view('pic_type_edit.html',$data);
	}

    //相册保存
	public function save()
	{
            $id   = intval($this->input->post('id'));
            $name = $this->input->post('name',true);
            $user = $this->input->post('user',true);
            $singer = $this->input->post('singer',true);
			$tags = $this->input->post('tags',true);
            $addtime = $this->input->post('addtime',true);
            $data['cid']=intval($this->input->post('cid'));

            if(empty($name)||empty($data['cid'])){
                   admin_msg('抱歉，相册名称、分类不能为空~!','javascript:history.back();','no');
			}

			//自动获取TAGS标签
            if(empty($tags)){
			     $tags = gettag($name);
			}

            $data['yid']=intval($this->input->post('yid'));
            $data['reco']=intval($this->input->post('reco'));
            $data['uid']=intval(getzd('user','id',$user,'name'));
            $data['name']=$name;
            $data['bname']=$this->input->post('bname',true);
            $data['pic']=$this->input->post('pic',true);
            $data['tags']=$tags;
            $data['hits']=intval($this->input->post('hits'));
            $data['yhits']=intval($this->input->post('yhits'));
            $data['zhits']=intval($this->input->post('zhits'));
            $data['rhits']=intval($this->input->post('rhits'));
            $data['dhits']=intval($this->input->post('dhits'));
            $data['chits']=intval($this->input->post('chits'));
            $data['skins']=$this->input->post('skins',true);
            $data['title']=$this->input->post('title',true);
            $data['keywords']=$this->input->post('keywords',true);
            $data['description']=$this->input->post('description',true);

			if($this->db->table_exists(CS_SqlPrefix.'singer')){  //歌手表存在
                 $data['singerid']=intval(getzd('singer','id',$singer,'name'));
			}

			if($id==0){ //新增
				 $data['addtime']=time();
                 $this->CsdjDB->get_insert('pic_type',$data);
			}else{
				 if($data['yid']==0) $this->dt($id);
                 if($addtime=='ok') $data['addtime']=time();
                 $this->CsdjDB->get_update('pic_type',$id,$data);
			}
            admin_msg('恭喜您，操作成功~!',site_url('pic/admin/type'),'ok');  //操作成功
	}

    //相册删除
	public function del()
	{
            $yid = intval($this->input->get('yid'));
            $ids = $this->input->get_post('id');
            $ac = $this->input->get_post('ac');
			//回收站
			if($ac=='hui'){
			     $result=$this->db->query("SELECT id,pic FROM ".CS_SqlPrefix."pic_type where hid=1")->result();
			     $this->load->library('csup');
			     foreach ($result as $row) {
					 //删除相册图片
					 $result2=$this->db->query("SELECT id,pic FROM ".CS_SqlPrefix."pic where sid=".$row->id."")->result();
			         foreach ($result2 as $row2) {
                         if(!empty($row2->pic)){
					        $this->csup->del($row2->pic,'pic'); //删除图片
				         }
					     $this->CsdjDB->get_del('pic',$row2->id);
					 }
                     if(!empty($row->pic)){
					    $this->csup->del($row->pic,'pic'); //删除相册
				     }
					 $this->CsdjDB->get_del('pic_type',$row->id);
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
			     $result=$this->db->query("SELECT pic FROM ".CS_SqlPrefix."pic_type where id in(".$idss.")")->result();
			     $this->load->library('csup');
			     foreach ($result as $row) {
					 //删除相册图片
					 $result2=$this->db->query("SELECT id,pic FROM ".CS_SqlPrefix."pic where sid=".$row->id."")->result();
			         foreach ($result2 as $row2) {
                         if(!empty($row2->pic)){
					        $this->csup->del($row2->pic,'pic'); //删除图片
				         }
					     $this->CsdjDB->get_del('pic',$row2->id);
					 }
                     if(!empty($row->pic)){
					    $this->csup->del($row->pic,'pic'); //删除图片
				     }
				 }
			     $this->CsdjDB->get_del('pic_type',$ids);
                 admin_msg('恭喜您，删除成功~!','javascript:history.back();','ok');  //操作成功
			}else{
				 $data['hid']=1;
			     $this->CsdjDB->get_update('pic_type',$ids,$data);
                 admin_msg('恭喜您，数据已经移动至回收站~!','javascript:history.back();','ok');  //操作成功
			}
	}

    //相册还原
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
			$this->CsdjDB->get_update('pic_type',$ids,$data);
            admin_msg('恭喜您，数据还原成功~!','javascript:history.back();','ok');  //操作成功
	}

    //相册批量
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
		    $yid=intval($this->input->post('yid'));
		    $user=$this->input->post('user',true);
		    $singer=$this->input->post('singer',true);
		    $skins=$this->input->post('skins',true);
		    $reco=intval($this->input->post('reco'));
		    $hits=intval($this->input->post('hits'));
		    $yhits=intval($this->input->post('yhits'));
		    $zhits=intval($this->input->post('zhits'));
		    $rhits=intval($this->input->post('rhits'));

            if(empty($csid)) admin_msg('请选择要操作的数据~!','javascript:history.back();','no');

            if($xid==1){  //按ID操作
				   if(empty($id)) admin_msg('请选择要操作的相册ID~!','javascript:history.back();','no');
                   foreach ($csid as $v) {
                          if($v=="cid"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set cid=".$cid." where id in (".$id.")");
                          }elseif($v=="yid"){
							  if($yid==0) $this->dt($id);
					          $this->db->query("update ".CS_SqlPrefix."pic_type set yid=".$yid." where id in (".$id.")");
                          }elseif($v=="reco"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set reco=".$reco." where id in (".$id.")");
                          }elseif($v=="hits"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set hits=".$hits." where id in (".$id.")");
                          }elseif($v=="yhits"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set yhits=".$yhits." where id in (".$id.")");
                          }elseif($v=="zhits"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set zhits=".$zhits." where id in (".$id.")");
                          }elseif($v=="rhits"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set rhits=".$rhits." where id in (".$id.")");
                          }elseif($v=="skins"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set skins='".$skins."' where id in (".$id.")");
                          }elseif($v=="user"){
					          $uid=intval(getzd('user','id',$user,'name'));
					          $this->db->query("update ".CS_SqlPrefix."pic_type set uid=".$uid." where id in (".$id.")");
					      }elseif($v=="singer"){
					          $singerid=intval(getzd('singer','id',$singer,'name'));
					          $this->db->query("update ".CS_SqlPrefix."pic_type set singerid=".$singerid." where id in (".$id.")");
				          }elseif($v=="hid"){
					          if($hid==2){
                                  $this->CsdjDB->get_del('pic',$id);
					          }else{
					              $this->db->query("update ".CS_SqlPrefix."pic_type set hid=".$hid." where id in (".$id.")");
					          }
				          }
				   }
			}else{ //按分类操作
				   if(empty($cids)) admin_msg('请选择要操作的相册分类~!','javascript:history.back();','no');
                   foreach ($csid as $v) {
                          if($v=="cid"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set cid=".$cid." where cid in (".$cids.")");
                          }elseif($v=="yid"){
							  if($yid==0) $this->dt($id);
					          $this->db->query("update ".CS_SqlPrefix."pic_type set yid=".$yid." where cid in (".$cids.")");
                          }elseif($v=="reco"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set reco=".$reco." where cid in (".$cids.")");
                          }elseif($v=="hits"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set hits=".$hits." where cid in (".$cids.")");
                          }elseif($v=="yhits"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set yhits=".$yhits." where cid in (".$cids.")");
                          }elseif($v=="zhits"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set zhits=".$zhits." where cid in (".$cids.")");
                          }elseif($v=="rhits"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set rhits=".$rhits." where cid in (".$cids.")");
                          }elseif($v=="skins"){
					          $this->db->query("update ".CS_SqlPrefix."pic_type set skins='".$skins."' where cid in (".$cids.")");
                          }elseif($v=="user"){
					          $uid=intval(getzd('user','id',$user,'name'));
					          $this->db->query("update ".CS_SqlPrefix."pic_type set uid=".$uid." where cid in (".$cids.")");
				          }elseif($v=="singer"){
					          $singerid=intval(getzd('singer','id',$singer,'name'));
					          $this->db->query("update ".CS_SqlPrefix."pic_type set singerid=".$singerid." where cid in (".$cids.")");
				          }elseif($v=="hid"){
					          if($hid==2){
                                  $this->CsdjDB->get_del('pic',$cids);
					          }else{
					              $this->db->query("update ".CS_SqlPrefix."pic_type set hid=".$hid." where cid in (".$cids.")");
					          }
				          }
				   }
			}
			exit('<script type="text/javascript">
			parent.location.href=parent.location.href;
			parent.tip_cokes();
			</script>');  //操作成功
	}

	//审核图片增加积分、经验、同时动态显示
	public function dt($id)
	{
			$dt=$this->db->query("SELECT id,name FROM ".CS_SqlPrefix."dt where link='".linkurl('show','id',$id,1,'pic')."' and yid=1")->row();
			if($dt){
                  $uid=getzd('pic_type','uid',$id);
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
				  $add['name']='相册审核通知';
				  $add['neir']='恭喜您，您的相册《'.$dt->name.'》已经审核通过，'.$str.'感谢您的支持~~';
				  $add['addtime']=time();
            	  $this->CsdjDB->get_insert('msg',$add);
			}
	}
}
