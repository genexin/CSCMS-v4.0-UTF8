<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2008-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-11-13
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Collect extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjAdmin');
			$this->lang->load('admin_collect');
	        $this->CsdjAdmin->Admin_Login();
            $this->load->library('caiji');
	}

    //规则列表
	public function index()
	{
 	        $page = intval($this->input->get('page'));
 	        $dir = $this->input->get('dir',true,true);
            if($page==0) $page=1;

            if($dir){
                 $sql_string = "SELECT id,name,dir,addtime FROM ".CS_SqlPrefix."caiji where dir='".$dir."' order by id desc";
			}else{
                 $sql_string = "SELECT id,name,dir,addtime FROM ".CS_SqlPrefix."caiji order by id desc";
			}
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('collect/index').'?dir='.$dir;
	        $per_page = 15; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['collect'] = $query->result();
	        $data['page'] = $page;
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类

            $this->load->view('collect.html',$data);
	}

    //新增
	public function add()
	{
 	        $id = intval($this->input->get_post('id'));
 	        $page = intval($this->input->get('page'));
			$html = $this->input->post('html');
            $data['page']=$page;
            $data['id']=$id;
			$data['savelink']=site_url('collect/add').'?id='.$id;

			if($id>0){
				 $row = $this->db->query("SELECT * FROM ".CS_SqlPrefix."caiji where id='".$id."'")->row();
				 if(!$row){
                       admin_msg(L('plub_01'),'javascript:history.back();','no');
				 }
			}

            if($page==0 || $page==1){
                 if($id==0){
                       $data['name']="";
                       $data['url']="";
                       $data['code']="gbk";
                       $data['dir']="";
                       $data['cjurl']="";
                       $data['ksid']=1;
                       $data['jsid']=1;
                       $data['cfid']=0;
                       $data['picid']=0;
                       $data['dxid']=0;
                       $data['rkid']=0;
				 }else{
                       $data['name']=$row->name;
                       $data['url']=$row->url;
                       $data['code']=$row->code;
                       $data['dir']=$row->dir;
                       $data['cjurl']=$row->cjurl;
                       $data['ksid']=$row->ksid;
                       $data['jsid']=$row->jsid;
                       $data['cfid']=$row->cfid;
                       $data['picid']=$row->picid;
                       $data['dxid']=$row->dxid;
                       $data['rkid']=$row->rkid;
				 }

            }elseif($page==2){

                 $datas['name']=$this->input->post('name',true);
                 $datas['url']=$this->input->post('url',true);
                 $datas['code']=$this->input->post('code',true);
                 $datas['dir']=$this->input->post('dir',true);
                 $datas['cjurl']=$this->input->post('cjurl',true);
                 $datas['ksid']=intval($this->input->post('ksid'));
                 $datas['jsid']=intval($this->input->post('jsid'));
                 $datas['cfid']=intval($this->input->post('cfid'));
                 $datas['picid']=intval($this->input->post('picid'));
                 $datas['dxid']=intval($this->input->post('dxid'));
                 $datas['rkid']=intval($this->input->post('rkid'));
				 if($datas['ksid']==0) $datas['ksid']=1;
				 if($datas['jsid']==0) $datas['jsid']=1;
				 if(empty($datas['code'])) $datas['code']='gbk';

                 if(empty($datas['name']) || empty($datas['url']) || empty($datas['dir']) || empty($datas['cjurl'])){
                       admin_msg(L('plub_02'),'javascript:history.back();','no');
				 }

                 $cjurl =str_replace('{$id}',$datas['ksid'],$datas['cjurl']);
				 $neir=$this->caiji->str($cjurl,$datas['code']);
                 if(empty($neir)) admin_msg(L('plub_03'),'javascript:history.back();','no');

                 //写入数据库
				 if($id==0){  //新增
					  $row = $this->db->query("SELECT id FROM ".CS_SqlPrefix."caiji where cjurl='".$cjurl."'")->row(); 
					  if(!$row){
                            $data['id']=$this->CsdjDB->get_insert('caiji',$datas);
					  }else{
                            $data['id']=$row->id;
					  }
					  $data['listks']='';
					  $data['listjs']='';
			     }else{  //修改

                      $this->CsdjDB->get_update('caiji',$id,$datas);
					  $data['listks']=$row->listks;
					  $data['listjs']=$row->listjs;
				 }
                 $data['savelink']=site_url('collect/add').'?id='.$data['id'];
                 $data['html']=$neir;

            }elseif($page==3){

				 $datas['listks']=$this->input->post('listks');
				 $datas['listjs']=$this->input->post('listjs');

                 if(empty($datas['listks']) || empty($datas['listjs'])){
                       admin_msg(L('plub_04'),'javascript:history.back();','no');
				 }

				 $neir=$this->caiji->getstr($html,$datas['listks'],$datas['listjs']);
                 if(empty($neir)) admin_msg(L('plub_05'),'javascript:history.back();','no');

				 //修改数据库
                 $this->CsdjDB->get_update('caiji',$id,$datas);

                 $data['html']=$neir;
                 $data['picmode']=$row->picmode;
                 $data['picks']=$row->picks;
                 $data['picjs']=$row->picjs;
                 $data['linkks']=$row->linkks;
                 $data['linkjs']=$row->linkjs;

            }elseif($page==4){

				 $datas['linkks']=$this->input->post('linkks');
				 $datas['linkjs']=$this->input->post('linkjs');
                 $datas['picmode']=intval($this->input->post('picmode'));
                 $datas['picks']=$this->input->post('picks');
                 $datas['picjs']=$this->input->post('picjs');

                 if(empty($datas['linkks']) || empty($datas['linkjs'])){
                       admin_msg(L('plub_06'),'javascript:history.back();','no');
				 }

                 if($datas['picmode']==1 && (empty($datas['picks']) || empty($datas['picjs']))){
                       admin_msg(L('plub_07'),'javascript:history.back();','no');
				 }
                 $this->caiji->weburl($row->url);
				 $links=$this->caiji->getarr($datas['linkks'],$datas['linkjs'],$html);
                 if(empty($links)) admin_msg(L('plub_08'),'javascript:history.back();','no');

				 //修改数据库
                 $this->CsdjDB->get_update('caiji',$id,$datas);

                 $data['links']=$links;
                 $data['html']=$this->caiji->str($links[0],$row->code);
                 $data['nameks']=$row->nameks;
                 $data['namejs']=$row->namejs;
                 $data['strth']=$row->strth;
                 $data['picmode']=$row->picmode;
                 $data['picks']=$row->picks;
                 $data['picjs']=$row->picjs;
                 $data['dir']=$row->dir;

            }elseif($page==5){

                 $html=$this->input->post('html');
                 $datas['nameks']=$this->input->post('nameks');
                 $datas['namejs']=$this->input->post('namejs');
                 $datas['strth']=$this->input->post('strth');
				 if($row->picmode==2){
                     $datas['picks']=$this->input->post('picks');
                     $datas['picjs']=$this->input->post('picjs');
                     $data['pic']=$this->caiji->getstr($html,$datas['picks'],$datas['picjs']);
				 }
				 //修改数据库
                 $this->CsdjDB->get_update('caiji',$id,$datas);

                 //获取标题和正文
                 $data['name']=$this->caiji->rep($this->caiji->getstr($html,$datas['nameks'],$datas['namejs']),$datas['strth']);

                 //判断自定义标记规则
                 $zdy=$this->input->post('zdy');
				 $ids='';
				 for ($i=0; $i < count($zdy['id']); $i++) {
                          $add['name']=$zdy['name'][$i];
                          $add['zd']=$zdy['zd'][$i];
                          $add['ks']=$zdy['ks'][$i];
                          $add['js']=$zdy['js'][$i];
                          $add['fid']=$zdy['fid'][$i];
                          $add['fname']=$zdy['fname'][$i];
                          $add['cid']=$row->id;

                          if(intval($zdy['id'][$i])==0){ //新增
                                $rows=$this->db->query("SELECT id FROM ".CS_SqlPrefix."cjannex where zd='".$add['zd']."' and cid=".$add['cid']."")->row(); 
								if(!$rows){
                                     $id=$this->CsdjDB->get_insert('cjannex',$add);
								     $ids.=' and id!='.$id.'';
								}else{
								     $ids.=' and id!='.$rows->id.'';
								}
						  }else{  //修改
                                $this->CsdjDB->get_update('cjannex',$zdy['id'][$i],$add);
								$ids.=' and id!='.$zdy['id'][$i].'';
						  }
                          $data['zdy']['name'][]=$add['name'];
						  if($add['fid']==0){
                               $data['zdy']['text'][]=$this->caiji->rep($this->caiji->getstr($html,$add['ks'],$add['js']),$datas['strth']);
						  }else{
                               $data['zdy']['text'][]=$add['fname'];
						  }
				 }
				 //删除自定义标记规则
                 $this->db->query("delete from ".CS_SqlPrefix."cjannex where cid=".$id." ".$ids."");
                 $data['picmode']=$row->picmode;
			}
            $this->load->view('collect_edit.html',$data);
	}

    //采集
	public function caiji()
	{
            $ac=$this->input->get('ac',true);
            $id=intval($this->input->get('id'));
            $xid=intval($this->input->get('xid'));
            $page=intval($this->input->get('page'));
            $okid=intval($this->input->get('okid'));
			if($page==0) $page=1;
			if($xid==0) $xid=1;

			if($id==0) admin_msg(L('plub_09'),'javascript:history.back();','no');  //ID不能为空
			$row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."caiji where id='".$id."'")->row(); 
			if(!$row) admin_msg(L('plub_10'),'javascript:history.back();','no');  //记录存在

            //总页数
            $pagejs=$row->jsid-$row->ksid;
			if($pagejs<1) $pagejs=1;

            //倒顺采集
            if($row->dxid==1){
                  if($xid==1) $xid=$row->jsid;
                  $cjurl =str_replace('{$id}',$xid,$row->cjurl);
                  $data['ids']=$xid;
                  $xid=($xid-1);
            }else{
				
                  if($xid==1) $xid=$row->ksid;
                  $cjurl =str_replace('{$id}',$xid,$row->cjurl);
                  $data['ids']=$xid;
                  $xid=($xid+1);
            }

			//读取列表文件
			$Content=trim($this->caiji->str($cjurl,$row->code));
			if(empty($Content)) admin_msg(L('plub_11'),site_url('collect'),'no');

			//赋予网站主路径
            $this->caiji->weburl($row->url);

			//获取列表开始-结束
	        $Liststr = $this->caiji->getstr($Content,trim($row->listks),trim($row->listjs));
			if(empty($Liststr)) admin_msg(L('plub_12'),site_url('collect'),'no');

			//获取到连接地址 返回数组
			$LinkArr= $this->caiji->getarr(trim($row->linkks),trim($row->linkjs),$Liststr);
			if(empty($LinkArr)) admin_msg(L('plub_13'),site_url('collect'),'no');

            //列表页采集图片
            $pic='';
            if($row->picmode==1){
			   //获取到图片地址 返回数组
               $PicArr=$this->caiji->getarr(trim($row->picks),trim($row->picjs),$Liststr);
               $pic=$PicArr[$okid];
               if(!empty($pic) && substr($pic,0,7)!='http://') $pic=$row->url.$pic;
            }
			$data['pic']=$pic;

			//当前页内容总数
            $LinkCount=count($LinkArr);
            if($page>($pagejs+1)){ //采集完毕
				    admin_msg(L('plub_14'),site_url('collect'));
            }

            //采集内容页开始
            $data['err']='';
			$data['pagejs']=($pagejs+1);
			$data['page']=$page;
			$data['okid']=$okid;
			$data['xid']=$xid;
			$data['oknum']=($okid+1);
			$data['names']=$row->name;
			$data['linkcount']=$LinkCount;
			$data['title']='';

            $neirurl=(substr($LinkArr[$okid],0,7)!='http://')?$row->url.$LinkArr[$okid]:$LinkArr[$okid];
			//读取内容文件
			$cjzt=0;
            $DanceContent=$this->caiji->str($neirurl,$row->code);
            if(empty($DanceContent)) {
                     $data['err']=vsprintf(L('plub_15'),array($neirurl));
            }else{
                     //标题
	                 $name = $this->caiji->getstr($DanceContent,trim($row->nameks),trim($row->namejs)); 
	                 $data['name'] = str_checkhtml($this->caiji->rep($name,$row->strth));

                     //图片
					 if($row->picmode==2){
	                     $data['pic'] =  $this->caiji->getstr($DanceContent,trim($row->picks),trim($row->picjs)); 
					 }else{
	                     $data['pic'] = str_checkhtml($pic);
					 }
					 if(empty($data['name'])){
                          $data['err']=L('plub_16');
					 }else{
                          //获取自定义
                          $query=$this->db->query("SELECT * FROM ".CS_SqlPrefix."cjannex where cid=".$id." order by id desc"); 
                          foreach ($query->result() as $rowz) {
	                             $zdy = $this->caiji->getstr($DanceContent,trim($rowz->ks),trim($rowz->js));
	                             $data['zdy']['name'][] = $rowz->name;
	                             $data['zdy']['zd'][] = $rowz->zd;
								 if($rowz->fid==1){
	                                   $data['zdy']['text'][] = $rowz->fname;
								 }else{
	                                   $data['zdy']['text'][] = str_checkhtml($this->caiji->rep($zdy,$row->strth));
								 }
						  }
						  if($ac=='ceshi'){  //测试不入库
                                 $data['title']=L('plub_17');
						  }else{
							     $cjzt=1;
                                 //---------------------------入库---------------------------------------
                                 $add['name']=$data['name'];
                                 $add['pic']=$data['pic'];
                                 $add['dir']=$row->dir;
                                 $add['zdy']=(!empty($data['zdy']))?arraystring($data['zdy']):'';
                                 $add['addtime']=time();

                                 //------------------------判断保存图片到本地----------------------------------
                                 if($row->picid==1){
                                     if(!empty($data['pic']) && substr($data['pic'],0,7)=='http://'){
                                          $picdata = @file_get_contents($data['pic']); // 读文件内容
                                          $picfolder =FCPATH."attachment/vod/";
                                          $picname=date('Ymd')."/".date('Ymd').time().mt_rand(1,100).".jpg";
                                          if(!empty($picdata)){
                                              if(write_file($picfolder.$picname, $picdata)){
											      $add['pic']=$picname;
											  }
                                          }
                                     }
                                 }

                                 //------------------------判断入未审核库----------------------------------
                                 if($row->rkid==0){ //临时库
                                        $add['cfid']=$row->cfid;
                                        //判断相同数据
										$rows=$this->db->query("SELECT id FROM ".CS_SqlPrefix."cjdata where name='".$add['name']."' and dir='".$row->dir."'")->row();
										if($rows){
                                                if($row->cfid==0){  //不入库
                                                     $data['title']=L('plub_18');
                                                }elseif($row->cfid==1){  //新增
                                                     $this->CsdjDB->get_insert('cjdata',$add);
                                                     $data['title']="入库成功~！";
                                                }elseif($row->cfid==2){  //覆盖
                                                     $this->CsdjDB->get_update('cjdata',$rows->id,$add);
                                                     $data['title']=L('plub_19');
                                                }
										}else{
                                                $this->CsdjDB->get_insert('cjdata',$add);
                                                $data['title']=L('plub_20');
										}

								 }else{ //入板块主数据库

									  //判断板块数据表是否存在
                                      if ($this->db->table_exists(CS_SqlPrefix.$row->dir)){
										    $strs='';
									        unset($add['dir']); 
									        unset($add['zdy']);
											//判断名称字段
										    if(!$this->db->field_exists('name', CS_SqlPrefix.$row->dir)){
									             unset($add['name']); 
											}else{
										         $strs.="and name='".$add['name']."' ";
											}
											//判断图片字段
										    if(!$this->db->field_exists('pic', CS_SqlPrefix.$row->dir)){
									             unset($add['pic']); 
											}else{
										         $strs.="and pic='".$add['pic']."' ";
											}
											//判断时间字段
										    if(!$this->db->field_exists('addtime', CS_SqlPrefix.$row->dir)){
									             unset($add['addtime']); 
											}
                                            //自定义规则
											$zdy=$data['zdy'];
											if(!empty($zdy)){
                                                 for ($i=0; $i < count($zdy['zd']); $i++) {
                                                      $add[$zdy['zd'][$i]]=$zdy['text'][$i];
													  if(strlen($zdy['text'][$i])<200) $strs.="and ".$zdy['zd'][$i]."='".$zdy['text'][$i]."' ";
												 }
											}
									 		$strs=substr($strs,3);
									 		$rowk=$this->db->query("SELECT id FROM ".CS_SqlPrefix.$row->dir." where ".$strs."")->row();
									 		if($rowk){
										 		 if($row->cfid==2){ //覆盖
										        		$data['title']=L('plub_19');
							                   		    $this->CsdjDB->get_update($row->dir,$rowk->id,$add);
												 }elseif($row->cfid==1){ //新增
											    		$this->CsdjDB->get_insert($row->dir,$add);
										        		$data['title']=L('plub_20');
										  		 }else{
										        		$data['title']=L('plub_18');
										  		 }
									 		}else{
					                      		$this->CsdjDB->get_insert($row->dir,$add);
										  		$data['title']=L('plub_20');
									 		}
									  }
								 }
                                 //修改采集时间
                                 $cjtdata['addtime']=time();
                                 $this->CsdjDB->get_update('caiji',$row->id,$cjtdata);
						  }
					 }
			}
			if($ac!='ceshi' && !empty($data['name'])){  //写入采集记录,测试不入库
                 $cjdata['name']=$data['name'];
                 $cjdata['names']=$row->name;
                 $cjdata['dir']=$row->dir;
                 $cjdata['url']=$neirurl;
                 $cjdata['zid']=$cjzt;
                 $this->CsdjDB->get_insert('cjlist',$cjdata);
			}
            $data['id']=$row->id;
            $data['ac']=$ac;
            $this->load->view('collect_caiji.html',$data);
	}

    //规则导出
	public function daochu()
	{
            $id=intval($this->input->get('id'));
			if($id==0) admin_msg(L('plub_09'),'javascript:history.back();','no');  //ID不能为空
			$row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."caiji where id='".$id."'")->row(); 
			if(!$row) admin_msg(L('plub_10'),'javascript:history.back();','no');  //记录存在

            $query = $this->db->query("SHOW FULL FIELDS FROM ".CS_SqlPrefix."caiji");
			$rule='';
			foreach ($query->result_array() as $rowz) {
				   if($rowz['Field']!='id' && $rowz['Field']!='addtime'){
			           $rule.="<cscms_".$rowz['Field'].">".str_replace("\r\n","###换行###",$row->$rowz['Field'])."</cscms_".$rowz['Field'].">\r\n";
				   }
			}
			//获取自定义规则
			$query=$this->db->query("SELECT * FROM ".CS_SqlPrefix."cjannex where cid=".$id." order by id desc"); 
            foreach ($query->result() as $rows) {
			       $rule.="<cscms_zdy><name>".$rows->name."</name><ks>".str_replace("\r\n","###换行###",$rows->ks)."</ks><js>".str_replace("\r\n","###换行###",$rows->js)."</js><fid>".$rows->fid."</fid><zd>".$rows->zd."</zd><fname>".str_replace("\r\n","###换行###",$rows->fname)."</fname></cscms_zdy>\r\n";
			}
			$rule.="---------cscms规则结束----------";
            $rule=str_replace("\r\n---------cscms规则结束----------","",$rule);
            $this->load->helper('download');
            force_download($row->name.".txt", $rule);
	}

    //导入规则
	public function daoru()
	{
            $this->load->view('collect_daoru.html');
	}

    //导入规则入库
	public function daoru_save() 
    {            
 	        $sid = intval($this->input->post('sid'));
 	        $neirs = $this->input->post('neir');
            if($sid==2){  //上传规则

               $config['upload_path'] = FCPATH.'attachment/other/';
               $config['allowed_types'] = 'txt';
               $config['encrypt_name'] = TRUE;  //重命名
               $this->load->library('upload', $config);
               if ( ! $this->upload->do_upload()){
                          $error = array('error' => $this->upload->display_errors());
						  admin_msg($error['error'],'javascript:history.back();','no');
               } else {
                          $data = $this->upload->data();
                          $Filename=$data['full_path'];
                          $neirs=@file_get_contents($Filename);
               }
            }

            if(empty($neirs)) admin_msg(L('plub_23'),'javascript:history.back();','no');

	        $regArr=explode("\n",$neirs);
	        for($i=0;$i<count($regArr);$i++){
                $ziduan=$this->caiji->getstr($regArr[$i],'<cscms_','>');
				if($ziduan!='zdy'){ //自定义规则
                     $neir=$this->caiji->getstr($regArr[$i],"<cscms_".$ziduan.">","</cscms_".$ziduan.">");
                     if(!empty($ziduan)){
                           $add[$ziduan]=str_replace("###换行###","\r\n",$neir);
					 }
                }
            }

            if(empty($add)) admin_msg(L('plub_24'),'javascript:history.back();','no');
            $id=$this->CsdjDB->get_insert('caiji',$add);

            //自定义规则
            $zdy=$this->caiji->getarr('<cscms_zdy>','</cscms_zdy>',$neirs,2);
			if(!empty($zdy)){
				$add2='';
				$zdy=explode("\n",$zdy);
                foreach ($zdy as $zdystr) {
                       $add2['name']=$this->caiji->getstr($zdystr,'<name>','</name>');
                       $add2['ks']=str_replace("###换行###","\r\n",$this->caiji->getstr($zdystr,'<ks>','</ks>'));
                       $add2['js']=str_replace("###换行###","\r\n",$this->caiji->getstr($zdystr,'<js>','</js>'));
                       $add2['fid']=$this->caiji->getstr($zdystr,'<fid>','</fid>');
                       $add2['zd']=$this->caiji->getstr($zdystr,'<zd>','</zd>');
                       $add2['fname']=str_replace("###换行###","\r\n",$this->caiji->getstr($zdystr,'<fname>','</fname>'));
                       $add2['cid']=$id;
			           if(!empty($add2)) $this->CsdjDB->get_insert('cjannex',$add2); //增加自定义
				}
			}
            if($sid==2){ //如果是上传你的则删除文件
                 @unlink($Filename);
			}
            admin_msg(L('plub_25'),site_url('collect'));
	}   

    //删除规则
	public function del()
	{
            $id = $this->input->get_post('id',true);
			$this->CsdjDB->get_del('caiji',$id);
			$this->CsdjDB->get_del('cjannex',$id,'cid');
            admin_msg(L('plub_26'),'javascript:history.back();','ok');  //操作成功
	}

    //历史记录
	public function lists()
	{
 	        $page = intval($this->input->get('page'));
 	        $zid = intval($this->input->get_post('zid'));
 	        $dir = $this->input->get_post('dir',true);
            if($page==0) $page=1;

            $sql_string = "SELECT * FROM ".CS_SqlPrefix."cjlist where 1=1";
            if(!empty($dir)){
                 $sql_string.=" and dir='".$dir."'";
			}
            if($zid>0){
                 $sql_string.=" and zid=".($zid-1)."";
			}
            $sql_string.=" order by id desc";
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('collect/lists').'?dir='.$dir.'&zid='.$zid;
	        $per_page = 15; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['collect'] = $query->result();
	        $data['page'] = $page;
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类

            $this->load->view('collect_lists.html',$data);
	}

    //删除历史记录
	public function lists_del()
	{
            $ac = $this->input->get_post('ac',true);
            $id = $this->input->get_post('id',true);
			if($ac=='all'){ //全部
				  $this->db->query("delete from ".CS_SqlPrefix."cjlist");
			}else{
			      $this->CsdjDB->get_del('cjlist',$id);
			}
            admin_msg(L('plub_26'),site_url('collect/lists'),'ok');  //操作成功
	}

    //临时库记录
	public function ruku()
	{
 	        $page = intval($this->input->get('page'));
 	        $zid = intval($this->input->get_post('zid'));
 	        $dir = $this->input->get_post('dir',true);
            if($page==0) $page=1;

            $sql_string = "SELECT * FROM ".CS_SqlPrefix."cjdata where 1=1";
            if(!empty($dir)){
                 $sql_string.=" and dir='".$dir."'";
			}
            if($zid>0){
                 $sql_string.=" and zid=".($zid-1)."";
			}
            $sql_string.=" order by id desc";
	        $query = $this->db->query($sql_string); 
	        $total = $query->num_rows();

            $base_url = site_url('collect/ruku').'?dir='.$dir.'&zid='.$zid;
	        $per_page = 15; 
            $totalPages = ceil($total / $per_page); // 总页数
	        $data['nums'] = $total;
            if($total<$per_page){
                  $per_page=$total;
            }
            $sql_string.=' limit '. $per_page*($page-1) .','. $per_page;
	        $query = $this->db->query($sql_string);

	        $data['collect'] = $query->result();
	        $data['page'] = $page;
	        $data['pages'] = get_admin_page($base_url,$totalPages,$page,10); //获取分页类

            $this->load->view('collect_ruku.html',$data);
	}

    //查看临时库记录
	public function look()
	{
            $id   = intval($this->input->get('id'));
            $row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."cjdata where id=".$id."")->row(); 
			if(!$row) admin_msg(L('plub_01'),'javascript:history.back();','no');  //记录不存在
            $data['row']=$row;
            $this->load->view('collect_look.html',$data);
	}

    //入库临时库
	public function ruku_add()
	{
            $ac = $this->input->get('ac',true);
            $id = $this->input->get_post('id',true);

            if($ac=='no'){  //入库全部未入库数据
                  $query = $this->db->query("SELECT * FROM ".CS_SqlPrefix."cjdata where zid=0 order by id desc limit 50");
				  if($query->num_rows()==0){
                        admin_msg(L('plub_27'),site_url('collect/ruku'),'ok');  //操作成功
				  }
				  echo '<!doctype html><html><head><meta charset=utf-8><link type="text/css" rel="stylesheet" href="'.Web_Path.'packs/admin/css/style.css" /></head><body><div class="rightinfo"><table class="tablelist"><thead><tr><th>'.L('plub_28').'</th></tr></thead><tbody>';
				  foreach ($query->result() as $row) {
					        //判断板块数据表是否存在
                            if ($row && $this->db->table_exists(CS_SqlPrefix.$row->dir)){
								     $strs='';
								     //判断标题字段
					                 if($this->db->field_exists('name', CS_SqlPrefix.$row->dir)){
										 $add['name']=$row->name;
										 $strs.="and name='".$row->name."' ";
									 }
								     //判断图片字段
					                 if($this->db->field_exists('pic', CS_SqlPrefix.$row->dir)){
										 $add['pic']=$row->pic;
										 $strs.="and pic='".$row->pic."' ";
									 }
								     //判断时间字段
					                 if($this->db->field_exists('addtime', CS_SqlPrefix.$row->dir)){
										 $add['addtime']=time();
									 }
								     //判断自定义字段
					                 if(!empty($row->zdy)){
		                                    $zdy=unarraystring($row->zdy);
			                                for ($i=0; $i < count($zdy['name']); $i++) {
                                                 $add[$zdy['zd'][$i]]=$zdy['text'][$i];
										         if(strlen($zdy['text'][$i])<200) $strs.="and ".$zdy['zd'][$i]."='".$zdy['text'][$i]."' ";
			                                } 
					                 }
									 $strs=substr($strs,3);
									 $rowk=$this->db->query("SELECT id FROM ".CS_SqlPrefix.$row->dir." where ".$strs."")->row();
									 if($rowk){
										  if($row->cfid==2){ //覆盖
										        $title=L('plub_21');
							                    $this->CsdjDB->get_update($row->dir,$rowk->id,$add);
										  }elseif($row->cfid==1){ //新增
											    $this->CsdjDB->get_insert($row->dir,$add);
										        $title=L('plub_20');
										  }else{
										        $title=L('plub_22');
										  }
									 }else{
					                      $this->CsdjDB->get_insert($row->dir,$add);
										  $title=L('plub_20');
									 }
							         //修改入库状态
							         $edit['zid']=1;
							         $this->CsdjDB->get_update('cjdata',$row->id,$edit);

                                     echo '<tr><td><font color=#2222ff>'.L('plub_29').'：'.$row->id.'-->'.$row->name.'-->'.$title.'~!</font></td></tr>';
									 ob_flush();flush();
							}
                  }
				  echo "<tr><td><font color=red><b>".L('plub_30')."</b></font><script>setTimeout('ReadGo();',3000);function ReadGo(){window.location='?ac=no'}</script></td></tr>";
				  echo '</tbody></table></div></body></html>';
			}else{  //入库所选
                 if(empty($id)) admin_msg(L('plub_31'),'javascript:history.back();','no');  //未选择数据
                 if(is_array($id)){ //多记录
                       foreach ($id as $ids) {
                              $row=$this->db->query("SELECT * FROM ".CS_SqlPrefix."cjdata where id=".intval($ids)." and zid=0")->row();
					          //判断板块数据表是否存在
                              if ($row && $this->db->table_exists(CS_SqlPrefix.$row->dir)){
							         $strs='';
								     //判断标题字段
					                 if($this->db->field_exists('name', CS_SqlPrefix.$row->dir)){
										 $add['name']=$row->name;
										 $strs.="and name='".$row->name."' ";
									 }
								     //判断图片字段
					                 if($this->db->field_exists('pic', CS_SqlPrefix.$row->dir)){
										 $add['pic']=$row->pic;
										 $strs.="and pic='".$row->pic."' ";
									 }
								     //判断时间字段
					                 if($this->db->field_exists('addtime', CS_SqlPrefix.$row->dir)){
										 $add['addtime']=time();
									 }
								     //判断自定义字段
					                 if(!empty($row->zdy)){
		                                    $zdy=unarraystring($row->zdy);
			                                for ($i=0; $i < count($zdy['name']); $i++) {
                                                 $add[$zdy['zd'][$i]]=$zdy['text'][$i];
										         if(strlen($zdy['text'][$i])<200) $strs.="and ".$zdy['zd'][$i]."='".$zdy['text'][$i]."' ";
			                                } 
					                 }
									 $strs=substr($strs,3);
									 $rowk=$this->db->query("SELECT id FROM ".CS_SqlPrefix.$row->dir." where ".$strs."")->row();
									 if($rowk){
										  if($row->cfid==2){ //覆盖
										        $title=L('plub_21');
							                    $this->CsdjDB->get_update($row->dir,$rowk->id,$add);
										  }elseif($row->cfid==1){ //新增
											    $this->CsdjDB->get_insert($row->dir,$add);
										        $title=L('plub_20');
										  }else{
										        $title=L('plub_22');
										  }
									 }else{
					                      $this->CsdjDB->get_insert($row->dir,$add);
										  $title=L('plub_20');
									 }
							         //修改入库状态
							         $edit['zid']=1;
							         $this->CsdjDB->get_update('cjdata',$row->id,$edit);
							  }
					   }
				 }
                 admin_msg(L('plub_27'),site_url('collect/ruku'),'ok');  //操作成功
			}
	}

    //删除临时库记录
	public function ruku_del()
	{
            $ac = $this->input->get_post('ac',true);
            $id = $this->input->get_post('id',true);
			if($ac=='all'){ //全部
				  $this->db->query("delete from ".CS_SqlPrefix."cjdata");
			}elseif($ac=='yes'){ //已经入库
			      $this->CsdjDB->get_del('cjdata',1,'zid');
			}else{
			      $this->CsdjDB->get_del('cjdata',$id);
			}
            admin_msg(L('plub_26'),site_url('collect/ruku'),'ok');  //操作成功
	}
}
