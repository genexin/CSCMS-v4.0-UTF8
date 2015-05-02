<?php if ( ! defined('IS_ADMIN')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-03
 */
class Apiku extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->helper('vod');
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
	}

    //资源库列表
	public function index()
	{
            $ac = $this->input->get('ac',true);
            $op = $this->input->get('op',true);
            $do = $this->input->get('do',true);
			$rid  = intval($this->input->get('rid'));

			if($do=='caiji'){ //入库

 	                $api  = $this->input->get('api',TRUE);
 	                $page = intval($this->input->get('page'));
 	                $cid  = intval($this->input->get('cid'));
 	                $ac   = $this->input->get('ac',TRUE);
 	                $ops   = $this->input->get('op',TRUE);
 	                $key  = $keys = $this->input->get('key',TRUE);
 	                $ids  = $this->input->get('ids',TRUE);
		            if($page==0) $page=1;
		            if($ops=='24') $ops='day';
		            if($ops=='day'){
			            $op=24;
		            }elseif($ops=='week'){
			            $op=98;
		            }else{
			            $op=0;
		            }
					//绑定分类数组
                    $LIST = require_once(APPPATH.'config/bind.php');

		            $api_url ='?api='.$api.'&rid='.$rid.'&op='.$op.'&ac='.$ac.'&do=caiji&key='.$key.'&cid='.$cid;
                    if($api){
                          $API_URL=cs_base64_decode($api).'?ac=videolist&rid='.$rid.'&wd='.$key.'&t='.$cid.'&h='.$op.'&ids='.$ids.'&pg='.$page;
                          $strs=htmlall($API_URL,'gbk');

			              if(empty($strs)) admin_msg('<font color=red>采集失败，请多试几次，如一直出现该错误，通常为网络不稳定或禁用了采集！</font>','javascript:history.go(-1);');

			              //组合分页信息
			              preg_match('<list page="([0-9]+)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">',$strs,$page_array);
						  if(!empty($page_array)){
			                   $recordcount = $page_array[4];
			                   $pagecount = $page_array[2];
			                   $pagesize = $page_array[3];
			                   $pageindex = $page_array[1];	
						  }else{
			                   $recordcount = 0;
			                   $pagecount = 0;
			                   $pagesize = 0;
			                   $pageindex = 0;	
						  }


                          echo '<LINK href="'.base_url().'packs/admin/css/style.css" type="text/css" rel="stylesheet"><br>';
						  echo '<div id="loading" style="display:none;position: absolute;left:40%;top:300px;z-index:10;"><span style="width:100px;height:40px;line-height:40px;background-color:#ccc;">&nbsp;&nbsp;<img align="absmiddle" src="'.Web_Path.'packs/admin/images/loading.gif">数据加载中</span></div>';
			              echo "&nbsp;&nbsp;<b><font color=#0000ff>当前页共有".$recordcount."个数据，需要采集".$pagecount."次，每一次采集".$pagesize."个，正在执行第".$pageindex."次采集任务</font></b><br/>";

			              //组合列表
			              $vod='';
			              preg_match_all('/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><pic>([\s\S]*?)<\/pic><lang>([\s\S]*?)<\/lang><area>([\s\S]*?)<\/area><year>([\s\S]*?)<\/year><state>([\s\S]*?)<\/state><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><actor><\!\[CDATA\[([\s\S]*?)\]\]><\/actor><director><\!\[CDATA\[([\s\S]*?)\]\]><\/director><dl>([\s\S]*?)<\/dl><des><\!\[CDATA\[([\s\S]*?)\]\]><\/des>([\s\S]*?)<\/video>/',$strs,$vod_array);
                          $s=1;
			              foreach($vod_array[1] as $key=>$value){

                                  $p=($pageindex-1)*$pagesize+$s;

				                  $add['name']     = str_replace("'","",htmlspecialchars_decode($vod_array[4][$key]));
				                  $add['pic']      = $vod_array[6][$key];
				                  $add['uid']      = 1;
                                  $add['daoyan']   = str_replace(" ","/",str_replace("'","",htmlspecialchars_decode($vod_array[13][$key])));
                                  $add['zhuyan']     = str_replace(" ","/",str_replace("'","",htmlspecialchars_decode($vod_array[12][$key])));
                                  $add['year']     = $vod_array[9][$key];
                                  $add['diqu']     = $vod_array[8][$key];
                                  $add['yuyan']    = $vod_array[7][$key];
					              $add['remark']   = (empty($vod_array[10][$key]))?'完结':$vod_array[10][$key];
                                  $add['text']  = str_replace("'","",htmlspecialchars_decode($vod_array[15][$key]));
                                  $add['addtime']  = time();

                                  $add['daoyan']   = str_replace("//","/",$add['daoyan']);
                                  $add['zhuyan']   = str_replace("//","/",$add['zhuyan']);

                                  preg_match_all('/<dd flag="([\s\S]*?)"><\!\[CDATA\[([\s\S]*?)\]\]><\/dd>/',$vod_array[14][$key],$url_arr);

                                  $laiy = $url_arr[1];
                                  $purl = implode('#cscms#',$url_arr[2]);
					              $purl=htmlspecialchars_decode($purl);
					              $purl=str_replace("xigua","xgvod",$purl);
					              $purl=str_replace("xfplay","yyxf",$purl);
					              $purl=str_replace("yyxf://","xfplay://",$purl);
					              $purl=str_replace("百度影音","bdhd",$purl);
					              $purl=str_replace("#","\n",$purl);
					              $purl=str_replace("\ncscms\n","#cscms#",$purl);
					              $purl=str_replace("\r","",$purl);
                                  $add['purl'] = $purl;


                                  //判断绑定
			                      $val=arr_key_value($LIST,$ac.'_'.$vod_array[3][$key]);
			                      if(!$val){

			                                   echo "&nbsp;&nbsp;&nbsp;第".$p."个影片&nbsp;<font color=red>".$vod_array[4][$key]."</font>&nbsp;&nbsp;数据没有绑定分类，不进行入库处理！<br/>";

                                  //判断数据完整性
					              }elseif(empty($vod_array[4][$key]) || empty($purl)){

			                                   echo "&nbsp;&nbsp;&nbsp;第".$p."个影片&nbsp;<font color=red>".$vod_array[4][$key]."</font>&nbsp;&nbsp;数据不完整，不进行入库处理！<br/>";

					              }else{

				                        $add['cid']  = $val;

							            //判断数据是否存在
					                    $sql="SELECT id,purl,remark FROM ".CS_SqlPrefix."vod where name='".$add['name']."'";
	 		                            $row=$this->db->query($sql)->row();
			                            if(!$row){

                                               $this->CsdjDB->get_insert('vod',$add);
					                           echo "&nbsp;&nbsp;&nbsp;第".$p."个影片&nbsp;<font color=#ff00ff>".$vod_array[4][$key]."</font>数据库中没有记录，已入库完成！<br/>";

							            }else{

								             //判断更新状态
								             if($row->remark==$add['remark'] && strpos($row->purl,'$'.$ac) !== FALSE){

			                                      echo "&nbsp;&nbsp;&nbsp;第".$p."个影片&nbsp;<font color=#ff6600>".$vod_array[4][$key]."</font>&nbsp;&nbsp;数据相同，暂无不需要更新<br/>";

								             }else{
                                                  //判断来源
												  $lyid=0;
												  $lyarr=explode("#cscms#",$row->purl);
												  for($i=0;$i<count($lyarr);$i++){
												       $ly=explode("$",$lyarr[$i]);
													   if(in_array($ly[2], $laiy)){
                                                            $lyid=1;
															$laiyname=$ly[2];
															break;
													   }
												  }

								                  if($lyid==0){ //来源不存在

                                                        $edit['purl'] = $row->purl.'#cscms#'.$purl;

                                                        echo "&nbsp;&nbsp;&nbsp;第".$p."个影片&nbsp;<font color=#ff00ff>".$vod_array[4][$key]."</font>&nbsp;&nbsp;数据存在，增加一组来源数据成功~!<br/>";

								                  }else{

									                    $url_all=explode("#cscms#",$row->purl);
														$CS_PlayUrl='';
									                    for($j=0;$j<count($url_all);$j++){	
									                         $url_aa=explode("\n",$url_all[$j]);
									                         $url_ss=explode("$",$url_aa[0]);
											                 if($url_ss[2]==$laiyname){
                                                                    $ji=count($url_aa);
                                                                    $CS_PlayUrl = $url_all[$j];
                                                                    break;
											                 }
									                    }
					                                    $edit['remark']  = $add['remark'];
                                                        $edit['purl'] = str_replace($CS_PlayUrl,$purl,$row->purl);
                                                        echo "&nbsp;&nbsp;&nbsp;第".$p."个影片&nbsp;<font color=#ff00ff>".$vod_array[4][$key]."</font>&nbsp;&nbsp;数据存在，数据更新成功~!<br/>";
								                  }

									              //更新数据
                                                  $edit['addtime'] = time();
									              $this->CsdjDB->get_update('vod',$row->id,$edit);
								             }
							            }
					              }
			              $s++;
			              }

			              if($pageindex < $pagecount){
					            //缓存断点续采
					            $jumpurl = site_url('vod/admin/apiku').$api_url.'&page='.($page+1);
					            write_file(APPPATH."config/jumpurl.txt", $jumpurl);
					            //跳转到下一页
					            echo("</br>&nbsp;&nbsp;&nbsp;<a href='".site_url('vod/admin/apiku')."?api=".$api."&op=".$ops."&ac=".$ac."&key=".$keys."&cid=".$cid."'>紧急停止</a>&nbsp;&nbsp;&nbsp;<b>&nbsp;第<font color=red>".$page."</font>页入库完毕,暂停<font color=red>3</font>秒继续。。。。。</b><script>setTimeout('updatenext();',3000);
								function updatenext(){
									document.getElementById('loading').style.display = 'block';
									location.href='".$api_url."&page=".($page+1)."';
								}
								</script></br></br>");
			              }else{
					            //清除断点续采
					            write_file(APPPATH."config/jumpurl.txt", "0");
					            echo("</br>&nbsp;&nbsp;&nbsp;&nbsp;<b>恭喜您，全部入库完成啦。。。。。</b><script>
								setTimeout('updatenext();',3000);
								function updatenext(){
									document.getElementById('loading').style.display = 'block';
									location.href='".site_url('vod/admin/apiku').str_replace("&do=caiji","",$api_url)."';
								}
								</script>");				
			              }
		            }else{
                           
						  admin_msg('<font color=red>API错误！</font>','javascript:history.go(-1);');
		            }

			}elseif(!empty($ac)){  //资源库查看

 	    		$api  = $this->input->get('api',TRUE);
 	    		$page = intval($this->input->get('page'));
 	   		    $cid  = intval($this->input->get('cid'));
 	    		$ac   = $this->input->get('ac',TRUE);
 	    		$op   = $this->input->get('op',TRUE);
 	    		$key  = $this->input->get('key',TRUE);
				if($page==0) $page=1;
				if($op=='all') $op=0;

				$data['api_url'] ='?api='.$api.'&rid='.$rid.'&op='.$op.'&ac='.$ac.'&key='.$key.'&cid='.$cid;
				$data['key'] = $key;
				$data['op']  = $op;
				$data['cid'] = $cid;
				$data['rid'] = $rid;
				$data['api'] = $api;
        		if($api){
              		 $API_URL=cs_base64_decode($api).'?ac=list&rid='.$rid.'&wd='.$key.'&t='.$cid.'&h=0&ids=&pg='.$page;
					 $strs=htmlall($API_URL,'gbk');
					 if(empty($strs)) admin_msg('<font color=red>获取列表失败，请多试几次，如一直出现该错误，通常为网络不稳定或禁用了采集！</font>','javascript:history.go(-1);');

			  		 //组合分页信息
			  		 preg_match('<list page="([0-9]+)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">',$strs,$page_array);
			  		 $data['recordcount'] = $page_array[4];
			 		 $data['pagecount'] = $page_array[2];
			  		 $data['pagesize'] = $page_array[3];
			  		 $data['pageindex'] = $page_array[1];	

			 		 $path=site_url('vod/admin/apiku').$data['api_url'].'&key='.$key.'&cid='.$cid.'&';
			 		 $data['pages'] = get_admin_page($data['api_url'],$data['pagecount'],$page,10);
			  
			  		 //组合列表
			  		 $vod='';
			  		 preg_match_all('/<video>([\s\S]*?)<\/video>/',$strs,$vod_array);
			  		 foreach($vod_array[1] as $key=>$value){
                         preg_match_all('/<last>([\s\S]*?)<\/last>/',$value,$times);
                         preg_match_all('/<id>([0-9]+)<\/id>/',$value,$ids);
                         preg_match_all('/<tid>([0-9]+)<\/tid>/',$value,$cids);
                         preg_match_all('/<name><\!\[CDATA\[([\s\S]*?)\]\]><\/name>/',$value,$names);
                         preg_match_all('/<type>([\s\S]*?)<\/type>/',$value,$cnames);
                         preg_match_all('/<dt>([\s\S]*?)<\/dt>/',$value,$dts);
						 $vod[$key]['addtime'] = $times[1][0];
						 $vod[$key]['id'] = $ids[1][0];
						 $vod[$key]['cid'] = intval($cids[1][0]);
						 $vod[$key]['name'] = $names[1][0];
						 $vod[$key]['laiy'] = (!empty($dts[1][0]))?Laiyuan($dts[1][0]):$ac;
						 $vod[$key]['cname'] = $cnames[1][0];
			  		 }
             		 $data['vod']=$vod;

			 		  //组合分类
			 		  preg_match_all('/<ty id="([0-9]+)">([\s\S]*?)<\/ty>/',$strs,$list_array);
			 		  foreach($list_array[1] as $key=>$value){
						   $vod_list[$key]['id'] = $value;
						   $vod_list[$key]['name'] = $list_array[2][$key];
			 		  }
             		  $data['vod_list']=$vod_list;
            		  $data['ac']=$ac;
            		  $data['page']=$page;

                      $data['LIST'] = require_once(APPPATH.'config/bind.php');
            		  $this->load->view('apiku_list.html',$data);

        		}else{
            		  admin_msg('<font color=red>API错误！</font>','javascript:history.go(-1);');
				}

			}elseif(empty($do)){  //资源库首页
				 $data['jumpurl']=@file_get_contents(APPPATH."config/jumpurl.txt");
                 $data['api']='http://vod.chshcms.com/api/cscms_zy_4.x_utf8.js?'.rand(1000, 9999);
                 $this->load->view('apiku.html',$data);
			}
	}

    //绑定分类
	public function bind()
	{
	        $csid = intval($this->input->get('csid'));
 	        $ac  = $this->input->get('ac',TRUE);

            $LIST = require_once(APPPATH.'config/bind.php');
			$val=arr_key_value($LIST,$ac.'_'.$csid);
            $strs='<option value="0">&nbsp;|—选择目标分类</option>';
            $query = $this->db->query("SELECT id,name FROM ".CS_SqlPrefix."vod_list where fid=0 order by xid asc"); 
            foreach ($query->result() as $row) {
                        $clas=($row->id==$val)?' selected="elected"':'';
			            $strs.='<option value="'.$row->id.'"'.$clas.'>&nbsp;|—'.$row->name.'</option>';
                        $query2 = $this->db->query("SELECT id,name FROM ".CS_SqlPrefix."vod_list where fid=".$row->id." order by xid asc"); 
                        foreach ($query2->result() as $row2) {
                            $clas2=($row2->id==$val)?' selected="elected"':'';
			                $strs.='<option value="'.$row2->id.'"'.$clas2.'>&nbsp;|&nbsp;&nbsp;&nbsp;|—'.$row2->name.'</option>';
			            }
            }
            echo '<select class="select" name="cid" id="cid">'.$strs.'
                 </select><input class="button" type="button" value="提 交" onClick="submitbind(\''.$ac.'\',\''.$csid.'\');" style="cursor:pointer"> <input name="button" type="button" value="取 消" class="button" onClick="hidebind();" style="cursor:pointer">
				 ';
    }

    //绑定分类存储
	public function bind_save()
	{
 	    $ac   = $this->input->get('ac',TRUE);
	    $csid = intval($this->input->get_post('csid'));
	    $id = intval($this->input->get_post('cid'));

	    $LIST = require_once(APPPATH.'config/bind.php');
	    $LIST[$ac.'_'.$csid] = $id;
		arr_file_edit($LIST,APPPATH.'config/bind.php');
        echo 'ok';
	}

	//解除全部绑定
	public function jie_bind()
	{
 	        $api  = $this->input->get('api',TRUE);
 	        $ac   = $this->input->get('ac',TRUE);
	        $LIST = require_once(APPPATH.'config/bind.php');
            foreach ($LIST as $k=>$v) {
                 if(strpos($k,$ac.'_') !== FALSE){
                     unset($LIST[$k]);
				 }
			}
			arr_file_edit($LIST,APPPATH.'config/bind.php');
            header("Location: ".site_url('vod/admin/apiku')."?api=".$api."&ac=".$ac.""); 
    }
}

