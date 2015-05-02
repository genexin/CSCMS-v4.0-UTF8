`<?php if ( ! defined('IS_ADMIN')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2014 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-11-21
 */
class Html extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->helper('vod');
		    $this->load->model('CsdjAdmin');
	        $this->CsdjAdmin->Admin_Login();
		    $this->load->model('CsdjTpl');
            //判断运行模式
	        if(config('Web_Mode')!=3){
                 admin_msg('板块浏览模式非静态，不需要生成静态文件~!','javascript:history.back();','no');
			}
			$this->huri=config('Html_Uri');
	}

    //首页生成
	public function index()
	{
            if($this->huri['index']['check']==0){
				admin_msg('板块主页未开启生成~!','javascript:history.back();','no');
			}
            //获取静态路径
			$uri=config('Html_Uri');
			$index_url=adminhtml($uri['index']['url']);
			$this->load->get_templates('vod',2);
		    $Mark_Text=$this->CsdjTpl->plub_index('vod','index.html',true);
			write_file(FCPATH.$index_url,$Mark_Text);
            admin_msg('板块首页生成完毕,<a target="_blank" href="'.Web_Path.$index_url.'"><font color="red">浏览</font></a>','###');
	}

    //专题页
	public function topic()
	{
            $this->load->view('html_topic.html');
	}

    //专题列表页生成操作
	public function topic_save()
	{
            if($this->huri['topic/lists']['check']==0){
				admin_msg('专题列表未开启生成~!','javascript:history.back();','no');
			}
            $start= intval($this->input->get('start')); //当前页生成编号
            $pagesize= intval($this->input->get('pagesize')); //每页多少条
            $pagejs= intval($this->input->get('pagejs')); //总页数
            $datacount= intval($this->input->get('datacount')); //数据总数
            $page= intval($this->input->get('page')); //当前页
			if($start==0) $start=1;

            //公众URI
			$uri='?pagesize='.$pagesize.'&pagejs='.$pagejs.'&datacount='.$datacount;

			//重新定义模板路径
			$this->load->get_templates('vod',2);

			//获取分页信息
            if($datacount==0){
				 $template=$this->load->view('topic.html','',true);
				 $pagesize=10;
			     preg_match_all('/{cscms:([\S]+)\s+(.*?pagesize=\"([\S]+)\".*?)}([\s\S]+?){\/cscms:\1}/',$template,$page_arr);
		         if(!empty($page_arr) && !empty($page_arr[3][0])){
					    $pagesize=$page_arr[3][0];
				 }
				 $sqlstr="select id from ".CS_SqlPrefix."vod_topic where yid=0";
                 $datacount=$this->db->query($sqlstr)->num_rows(); //总数量
    	         $pagejs = ceil($datacount/$pagesize);
			}
            if($datacount==0) $pagejs=1;


            echo '<LINK href="'.base_url().'packs/admin/css/style.css" type="text/css" rel="stylesheet"><br>';
			echo '&nbsp;&nbsp;<b>正在开始生成<font color=red> 专题列表 </font>按,分<font color=red>'.ceil($pagejs/Html_PageNum).'</font>次生成，当前第<font color=red>'.ceil($start/Html_PageNum).'</font>次</b><br/>';

            $n=1;
            $pagego=1;
            if($page>0 && $page<$pagejs){
    	        $pagejs=$page;
            }

            for($i=$start;$i<=$pagejs;$i++){
                  ob_end_flush();//关闭缓存 
                  //获取静态路径
				  $Htmllinks=$Htmllink=LinkUrl('topic','lists',0,$i,'vod');
				  //转换成生成路径
				  $Htmllink=adminhtml($Htmllinks,'vod');
				  $Mark_Text=$this->CsdjTpl->plub_list(array(),1,'lists',$page,'',true,'topic.html','topic/lists','','视频专题');
			      write_file(FCPATH.$Htmllink,$Mark_Text);
				  echo "<a target='_blank' href='".$Htmllinks."'>&nbsp;&nbsp;第".$i."页&nbsp;&nbsp;".$Htmllinks."<font color=green>生成完毕~!</font></a><br/>";
                  $n++;
                  ob_flush();flush();

                  if(Html_PageNum==$n){
			           $pagego=2;
        	           break;
                  }
			}

   		    if($pagego==2){ //操作系统设置每页数量，分多页生成
				  $url=site_url('vod/admin/html/topic_save').$uri.'&numx='.$numx.'&start='.($i+1);
			      exit("</br>&nbsp;&nbsp;<b>暂停".Html_StopTime."秒后继续下一页&nbsp;>>>>&nbsp;&nbsp;<a target='_blank' href='".$url."'>如果您的 浏览器没有跳转，请点击继续...</a></b><script>setTimeout('updatenext();',".Html_StopTime."000);function updatenext(){location.href='".$url."';}</script>");
    		}
            $url=site_url('vod/admin/html/topic');
			$str="&nbsp;&nbsp;<b><font color=#0000ff>所有专题列表全部生成完毕!</font>&nbsp;>>>>&nbsp;&nbsp;<a href='".site_url('vod/admin/html/topic')."'>如果您的 浏览器没有跳转，请点击继续...</a></b>";
			echo("</br>".$str."<script>setTimeout('updatenext();',".Html_StopTime."000);function updatenext(){location.href='".$url."';}</script>");
	}

    //专题内容页生成操作
	public function topicshow_save()
	{
            if($this->huri['topic/show']['check']==0){
				admin_msg('专题内容未开启生成~!','javascript:history.back();','no');
			}
            $tid = $this->input->get_post('tid',true); //需要生成的专题ID
            $ksid= intval($this->input->get_post('ksid')); //开始ID
            $jsid= intval($this->input->get_post('jsid')); //结束ID
            $kstime= $this->input->get_post('kstime',true); //开始日期
            $jstime= $this->input->get_post('jstime',true); //结束日期
            $pagesize= intval($this->input->get('pagesize')); //每页多少条
            $pagejs= intval($this->input->get('pagejs')); //总页数
            $datacount= intval($this->input->get('datacount')); //数据总数
            $page= intval($this->input->get('page')); //当前页
			if($page==0) $page=1;
			$str='';

            //将数组转换成字符
			if(is_array($tid)){
			     $tid=implode(',', $tid);
			}

            if($ksid>0 && $jsid>0){
                 $str.=' and id>'.($ksid-1).' and id<'.($jsid+1).'';
			}
            if(!empty($kstime) && !empty($jstime)){
				 $ktime=strtotime($kstime)-86400;
				 $jtime=strtotime($jstime)+86400;
                 $str.=' and addtime>'.$ktime.' and addtime<'.$jtime.'';
			}
            if(!empty($tid)){
                 $str.=' and id in ('.$tid.')';
			}

            if($datacount==0){
				 $sqlstr="select id from ".CS_SqlPrefix."vod_topic where yid=0 ".$str;
                 $datacount=$this->db->query($sqlstr)->num_rows(); //总数量
    	         $pagejs = ceil($datacount/Html_PageNum);
			}
            if($datacount==0) $pagejs=1;

            $pagesize=Html_PageNum;
            if($datacount<$pagesize){
    	        $pagesize=$datacount;
            }

            //全部生成完毕
	        if($datacount==0 || $page>$pagejs){
                   admin_msg('所有专题内容页全部生成完毕~!',site_url('vod/admin/html/topic'));
			}

            //公众URI
			$uri='?tid='.$tid.'&ksid='.$ksid.'&jsid='.$jsid.'&kstime='.$kstime.'&jstime='.$jstime.'&pagesize='.$pagesize.'&pagejs='.$pagejs.'&datacount='.$datacount;

			//重新定义模板路径
			$this->load->get_templates('vod',2);

            echo '<LINK href="'.base_url().'packs/admin/css/style.css" type="text/css" rel="stylesheet"><br>';
			echo '&nbsp;&nbsp;<b>正在开始生成专题内容,分<font color=red>'.$pagejs.'</font>次生成，当前第<font color=red>'.$page.'</font>次</b><br/>';

            $sql_string="select * from ".CS_SqlPrefix."vod_topic where yid=0 ".$str." order by id desc";
            $sql_string.=' limit '. $pagesize*($page-1) .','. $pagesize;
			$query = $this->db->query($sql_string);

            foreach ($query->result_array() as $row) {
                  ob_end_flush();//关闭缓存 
				  $id=$row['id'];
                  //动态人气
			      unset($row['hits']);
			      unset($row['yhits']);
			      unset($row['zhits']);
			      unset($row['rhits']);
			      unset($row['shits']);
                  //获取静态路径
				  $Htmllinks=LinkUrl('topic','show',$id,1,'vod');
				  //转换成生成路径
				  $Htmllink=adminhtml($Htmllinks,'vod');
				  $ids['tid']=$id;

				  //装载模板并输出
	        	  $Mark_Text=$this->CsdjTpl->plub_show('topic',$row,$ids,true,'topic-show.html',$row['name'],$row['name']);
				  //评论
				  $Mark_Text=str_replace("[topic:pl]",get_pl('vod',$id,1),$Mark_Text);
				  //解析动态人气标签
				  $Mark_Text=str_replace("[topic:hits]","<script src='".hitslink('hits/dt/hits/'.$id.'/topic','vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[topic:yhits]","<script src='".hitslink('hits/dt/yhits/'.$id.'/topic','vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[topic:zhits]","<script src='".hitslink('hits/dt/zhits/'.$id.'/topic','vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[topic:rhits]","<script src='".hitslink('hits/dt/rhits/'.$id.'/topic','vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[topic:shits]","<script src='".hitslink('hits/dt/shits/'.$id.'/topic','vod')."'></script>",$Mark_Text);
				  //增加人气
			      $Mark_Text=hits_js($Mark_Text,cscmslink('vod'));

                  //生成
			      write_file(FCPATH.$Htmllink,$Mark_Text);
				  echo "&nbsp;<font style=font-size:10pt;>生成视频专题:<font color=red>".$row['name']."</font>成功:<a href=".$Htmllinks." target=_blank>".$Htmllinks."</a></font><br/>";
                  ob_flush();flush();

			}

    	    $url=site_url('vod/admin/html/topicshow_save').$uri.'&page='.($page+1);
			$str="&nbsp;&nbsp;<b>暂停".Html_StopTime."秒后继续&nbsp;>>>>&nbsp;&nbsp;<a href='".$url."'>如果您的 浏览器没有跳转，请点击继续...</a></b>";
			echo("</br>".$str."<script>setTimeout('updatenext();',".Html_StopTime."000);function updatenext(){location.href='".$url."';}</script>");
	}

    //列表页
	public function type()
	{
            $this->load->view('html_type.html');
	}

    //列表页生成操作
	public function type_save()
	{
            if($this->huri['lists']['check']==0){
				admin_msg('视频分类未开启生成~!','javascript:history.back();','no');
			}
            $ac  = $this->input->get_post('ac',true); //方式
            $cid = $this->input->get_post('cid',true); //需要生成的分类ID
            $fid = $this->input->get_post('fid',true); //需要生成的排序方式
            $nums= intval($this->input->get('nums')); //分类已完成数量
            $numx= intval($this->input->get('numx')); //排序方式已完成数量
            $start= intval($this->input->get('start')); //当前页生成编号
            $pagesize= intval($this->input->get('pagesize')); //每页多少条
            $pagejs= intval($this->input->get('pagejs')); //总页数
            $datacount= intval($this->input->get('datacount')); //数据总数
            $page= intval($this->input->get('page')); //当前页
			if($start==0) $start=1;
			if(empty($fid)){
				$fid=($ac=='all')?'id,news,reco,hits,yhits,zhits,rhits,xhits,shits,dhits,chits,phits':'id';
			}

            //生成全部分类获取全部分类ID
            if($ac=='all' && $nums==0){
				   $cid=array();
                   $query = $this->db->query("SELECT id FROM ".CS_SqlPrefix."vod_list where yid=0 order by xid asc"); 
                   foreach ($query->result() as $rowc) {
                       $cid[]=$rowc->id;
                   }
			}
            //将数组转换成字符
			if(is_array($cid)){
			     $cid=implode(',', $cid);
			}
			if(is_array($fid)){
			     $fid=implode(',', $fid);
			}
			//没有选择分类
			if(empty($cid)){
                    admin_msg('请选择要生成的分类~!',site_url('vod/admin/html/type'));
			}

            //公众URI
			$uri='?ac='.$ac.'&cid='.$cid.'&fid='.$fid.'&pagesize='.$pagesize.'&pagejs='.$pagejs.'&datacount='.$datacount;

            //分割分类ID
	        $arr = explode(',',$cid);
	        $len = count($arr);
            //分割排序方式
	        $arr2 = explode(',',$fid);
	        $len2 = count($arr2);

            //全部生成完毕
	        if($nums>=$len){
                    admin_msg('所有分类全部生成完毕~!',site_url('vod/admin/html/type'));
			}

			$id=$arr[$nums]; //当前分类ID
			$type=$arr2[$numx]; //当前排序

			//重新定义模板路径
			$this->load->get_templates('vod',2);

			//获取分类信息
			$row=$this->CsdjDB->get_row_arr('vod_list','*',$id);
            $template=!empty($row['skins'])?$row['skins']:'list.html';

			$arr['cid']=getChild($id);
			$arr['fid']=($row['fid']==0)?$row['id']:$row['fid'];
			$arr['sid']=$arr['fid'];

            if($datacount==0){
				 $template=$this->load->view($template,'',true);
				 $pagesize=10;
			     preg_match_all('/{cscms:([\S]+)\s+(.*?pagesize=\"([\S]+)\".*?)}([\s\S]+?){\/cscms:\1}/',$template,$page_arr);
		         if(!empty($page_arr) && !empty($page_arr[3][0])){
					    $pagesize=$page_arr[3][0];
				 }
				 $sqlstr="select id from ".CS_SqlPrefix."vod where yid=0 and hid=0 and cid IN (". getChild($id).")";
                 $datacount=$this->db->query($sqlstr)->num_rows(); //总数量
    	         $pagejs = ceil($datacount/$pagesize);
			}
            if($datacount==0) $pagejs=1;


            echo '<LINK href="'.base_url().'packs/admin/css/style.css" type="text/css" rel="stylesheet"><br>';
			echo '&nbsp;&nbsp;<b>正在开始生成分类<font color=red> '.$row["name"].' </font>按<font color=red> '.$this->gettype($type).' </font>排序的列表,分<font color=red>'.ceil($pagejs/Html_PageNum).'</font>次生成，当前第<font color=red>'.ceil($start/Html_PageNum).'</font>次</b><br/>';

            $n=1;
            $pagego=1;
            if($page>0 && $page<$pagejs){
    	        $pagejs=$page;
            }

            for($i=$start;$i<=$pagejs;$i++){
                  ob_end_flush();//关闭缓存 
                  //获取静态路径
				  $Htmllinks=LinkUrl('lists',$type,$id,$i,'vod');
				  //转换成生成路径
				  $Htmllink=adminhtml($Htmllinks,'vod');
				  $skins=empty($row['skins'])?'list.html':$row['skins'];
				  $Mark_Text=$this->CsdjTpl->plub_list($row,$id,$type,$i,$arr,true,$skins,'lists','vod',$row['name'],$row['name']);
			      write_file(FCPATH.$Htmllink,$Mark_Text);
				  echo "<a target='_blank' href='".$Htmllinks."'>&nbsp;&nbsp;第".$i."页&nbsp;&nbsp;".$Htmllinks."<font color=green>生成完毕~!</font></a><br/>";
                  $n++;
                  ob_flush();flush();

                  if(Html_PageNum==$n){
			           $pagego=2;
        	           break;
                  }
			}

   		    if($pagego==2){ //操作系统设置每页数量，分多页生成
				  $url=site_url('vod/admin/html/type_save').$uri.'&nums='.$nums.'&numx='.$numx.'&start='.($i+1);
			      exit("</br>&nbsp;&nbsp;<b>暂停".Html_StopTime."秒后继续下一页&nbsp;>>>>&nbsp;&nbsp;<a target='_blank' href='".$url."'>如果您的 浏览器没有跳转，请点击继续...</a></b><script>setTimeout('updatenext();',".Html_StopTime."000);function updatenext(){location.href='".$url."';}</script>");
    		}else{  
    	          $url=site_url('vod/admin/html/type_save').$uri.'&nums='.($nums+1);
			      $str="&nbsp;&nbsp;<b>暂停".Html_StopTime."秒后继续&nbsp;>>>>&nbsp;&nbsp;<a target='_blank' href='".$url."'>如果您的 浏览器没有跳转，请点击继续...</a></b>";
            }

            //判断生成完毕
			if(($numx+1)>=$len2){ //全部完成
                $url=site_url('vod/admin/html/type_save').$uri.'&nums='.($nums+1);
				$str="&nbsp;&nbsp;<b><font color=#0000ff>所有排序页面全部生成完毕!</font>&nbsp;>>>>&nbsp;&nbsp;<a href='".site_url('vod/admin/html/type_save').$uri.'&nums='.($nums+1)."'>如果您的 浏览器没有跳转，请点击继续...</a></b>";
			}else{ //当前排序方式完成
                $url=site_url('vod/admin/html/type_save').$uri.'&nums='.$nums.'&numx='.($numx+1);
				$str='&nbsp;&nbsp;<b>按<font color=#0000ff>'.$this->gettype($type).'排序</font>分类生成完毕!&nbsp;>>>>&nbsp;&nbsp;<a href="'.site_url('vod/admin/html/type_save').$uri.'&nums='.$nums.'&numx='.($numx+1).'">如果您的 浏览器没有跳转，请点击继续...</a></b>';
			}
			echo("</br>".$str."<script>setTimeout('updatenext();',".Html_StopTime."000);function updatenext(){location.href='".$url."';}</script>");
	}

    //内容页
	public function show()
	{
            $this->load->view('html_show.html');
	}

    //内容页生成操作
	public function show_save()
	{
            if($this->huri['show']['check']==0){
				admin_msg('视频内容页未开启生成~!','javascript:history.back();','no');
			}
            $day = intval($this->input->get_post('day',true)); //最近几天
            $ids = $this->input->get_post('ids',true); //需要生成的数据ID
            $cid = $this->input->get_post('cid',true); //需要生成的分类ID
            $newid= intval($this->input->get_post('newid')); //最新个数
            $ksid= intval($this->input->get_post('ksid')); //开始ID
            $jsid= intval($this->input->get_post('jsid')); //结束ID
            $kstime= $this->input->get_post('kstime',true); //开始日期
            $jstime= $this->input->get_post('jstime',true); //结束日期
            $pagesize= intval($this->input->get('pagesize')); //每页多少条
            $pagejs= intval($this->input->get('pagejs')); //总页数
            $datacount= intval($this->input->get('datacount')); //数据总数
            $page= intval($this->input->get('page')); //当前页
			if($page==0) $page=1;
			$str='';

            //将数组转换成字符
			if(is_array($cid)){
			     $cid=implode(',', $cid);
			}
			if(is_array($ids)){
			     $ids=implode(',', $ids);
			}

            if($day>0){
				 $times=time()-86400*$day;
                 $str.=' and addtime>'.$times.'';
			}
            if(!empty($cid)){
                 $str.=' and cid in ('.$cid.')';
			}
            if(!empty($ids)){
                 $str.=' and id in ('.$ids.')';
			}
            if($ksid>0 && $jsid>0){
                 $str.=' and id>'.($ksid-1).' and id<'.($jsid+1).'';
			}
            if(!empty($kstime) && !empty($jstime)){
				 $ktime=strtotime($kstime)-86400;
				 $jtime=strtotime($jstime)+86400;
                 $str.=' and addtime>'.$ktime.' and addtime<'.$jtime.'';
			}

			$limit='';
            if($newid>0){
                 $limit=' order by id desc limit '.$newid;
			}
            if($datacount==0){
				 $sqlstr="select id from ".CS_SqlPrefix."vod where yid=0 and hid=0 ".$str.$limit;
                 $datacount=$this->db->query($sqlstr)->num_rows(); //总数量
    	         $pagejs = ceil($datacount/Html_PageNum);
			}
            if($datacount==0) $pagejs=1;

            $pagesize=Html_PageNum;
            if($datacount<$pagesize){
    	        $pagesize=$datacount;
            }

            //全部生成完毕
	        if($page>$pagejs){
                   admin_msg('所有内容页全部生成完毕~!',site_url('vod/admin/html/show'));
			}


            //公众URI
			$uri='?day='.$day.'&cid='.$cid.'&ids='.$ids.'&newid='.$newid.'&ksid='.$ksid.'&jsid='.$jsid.'&kstime='.$kstime.'&jstime='.$jstime.'&pagesize='.$pagesize.'&pagejs='.$pagejs.'&datacount='.$datacount;

			//重新定义模板路径
			$this->load->get_templates('vod',2);

            echo '<LINK href="'.base_url().'packs/admin/css/style.css" type="text/css" rel="stylesheet"><br>';
			echo '&nbsp;&nbsp;<b>正在开始生成视频内容,分<font color=red>'.$pagejs.'</font>次生成，当前第<font color=red>'.$page.'</font>次</b><br/>';

            $sql_string="select * from ".CS_SqlPrefix."vod where yid=0 and hid=0 ".$str." order by id desc";
            $sql_string.=' limit '. $pagesize*($page-1) .','. $pagesize;
			$query = $this->db->query($sql_string);

			//获取播放页是否需要生成
			$html=config('Html_Uri','vod');

            foreach ($query->result_array() as $row) {
                  ob_end_flush();//关闭缓存 
				  $id=$row['id'];
                  //获取静态路径
				  $Htmllinks=LinkUrl('show','id',$row['id'],0,'vod');
				  //转换成生成路径
				  $Htmllink=adminhtml($Htmllinks,'vod');

				  //摧毁部分需要超级链接字段数组
				  $rows=$row; //先保存数组保留下面使用
				  unset($row['zhuyan']);
				  unset($row['daoyan']);
				  unset($row['yuyan']);
				  unset($row['diqu']);
				  unset($row['tags']);
				  unset($row['year']);
				  //静态模式动态人气
			      unset($row['hits']);
			      unset($row['yhits']);
			      unset($row['zhits']);
			      unset($row['rhits']);
			      unset($row['shits']);
			      unset($row['xhits']);
			      unset($row['dhits']);
			      unset($row['chits']);
			      unset($row['pfen']);

				  $arr['cid']=getChild($row['cid']);
				  $arr['uid']=$row['uid'];
				  $arr['singerid']=$row['singerid'];
				  $arr['tags']=$rows['tags'];
			      $skins=getzd('vod_list','skins2',$row['cid']);
			      if(empty($skins)) $skins='show.html';
				  //装载模板并输出
	        	  $Mark_Text=$this->CsdjTpl->plub_show('vod',$row,$arr,TRUE,$skins,$row['name'],$row['name']);
				  //评论
				  $Mark_Text=str_replace("[vod:pl]",get_pl('vod',$id),$Mark_Text);
				  //分类地址、名称
				  $Mark_Text=str_replace("[vod:link]",LinkUrl('show','id',$row['id'],1,'vod'),$Mark_Text);
				  $Mark_Text=str_replace("[vod:classlink]",LinkUrl('lists','id',$row['cid'],1,'vod'),$Mark_Text);
				  $Mark_Text=str_replace("[vod:classname]",$this->CsdjDB->getzd('vod_list','name',$row['cid']),$Mark_Text);
				  //主演、导演、标签、年份、地区、语言加超级连接
				  $Mark_Text=str_replace("[vod:zhuyan]",SearchLink($rows['zhuyan'],'zhuyan'),$Mark_Text);
				  $Mark_Text=str_replace("[vod:daoyan]",SearchLink($rows['daoyan'],'daoyan'),$Mark_Text);
				  $Mark_Text=str_replace("[vod:yuyan]",SearchLink($rows['yuyan'],'yuyan'),$Mark_Text);
				  $Mark_Text=str_replace("[vod:diqu]",SearchLink($rows['diqu'],'diqu'),$Mark_Text);
				  $Mark_Text=str_replace("[vod:tags]",SearchLink($rows['tags']),$Mark_Text);
				  $Mark_Text=str_replace("[vod:year]",SearchLink($rows['year'],'year'),$Mark_Text);
				  //解析动态人气标签
				  $Mark_Text=str_replace("[vod:hits]","<script src='".hitslink('hits/dt/hits/'.$id,'vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[vod:yhits]","<script src='".hitslink('hits/dt/yhits/'.$id,'vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[vod:zhits]","<script src='".hitslink('hits/dt/zhits/'.$id,'vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[vod:rhits]","<script src='".hitslink('hits/dt/rhits/'.$id,'vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[vod:shits]","<script src='".hitslink('hits/dt/shits/'.$id,'vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[vod:xhits]","<script src='".hitslink('hits/dt/xhits/'.$id,'vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[vod:dhits]","<script src='".hitslink('hits/dt/dhits/'.$id,'vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[vod:chits]","<script src='".hitslink('hits/dt/chits/'.$id,'vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[vod:pfen]","<script src='".hitslink('hits/dt/pfen/'.$id,'vod')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[vod:pfenbi]","<script src='".hitslink('hits/dt/pfenbi/'.$id,'vod')."'></script>",$Mark_Text);
				  //解析播放下载地址
           	   	  $Mark_Text=Vod_Playlist($Mark_Text,'play',$id,$row['purl']);
           	  	  $Mark_Text=Vod_Playlist($Mark_Text,'down',$id,$row['durl']);

                  //生成
			      write_file(FCPATH.$Htmllink,$Mark_Text);
				  echo "&nbsp;<font style=font-size:10pt;>生成影片:<font color=red>".$row['name']."</font>成功:<a href=".$Htmllinks." target=_blank>".$Htmllinks."</a></font><br/>";

				  //判断是否生成播放页
				  if($html['play']['check']==1){
				        $this->getplay($rows);
				  }
                  ob_flush();flush();
			}
            if(!empty($ids)){
                $url=$_SERVER['HTTP_REFERER'];
			    $str="&nbsp;&nbsp;<b>全部生成完毕&nbsp;>>>>&nbsp;&nbsp;<a href='".$url."'>如果您的 浏览器没有跳转，请点击继续...</a></b>";
			}else{ 
    	        $url=site_url('vod/admin/html/show_save').$uri.'&page='.($page+1);
			    $str="&nbsp;&nbsp;<b>暂停".Html_StopTime."秒后继续&nbsp;>>>>&nbsp;&nbsp;<a href='".$url."'>如果您的 浏览器没有跳转，请点击继续...</a></b>";
			}
			echo("</br>".$str."<script>setTimeout('updatenext();',".Html_StopTime."000);function updatenext(){location.href='".$url."';}</script>");
	}

    //其他页
	public function opt()
	{
		    //获取自定义模板
			$Skins_Dir=config('Skins_Dir','vod');
			$this->load->helper('directory');
            $path=FCPATH.'plugins/vod/skins/'.$Skins_Dir;
            $dir_arr=directory_map($path, 1);
            $skinsdirs=array();
		    if ($dir_arr) {
			    foreach ($dir_arr as $t) {
				    if (!is_dir($path.$t)) {
						if(substr($t,0,4)=='opt-'){
					        $skinsdirs[] = $t;
						}
				    }
			    }
		    }
	        $data['skins_skins'] = $skinsdirs;
            $this->load->view('html_opt.html',$data);
	}

    //其他页生成操作
	public function opt_save()
	{

            $path = $this->input->post('path',true); //方式

			//重新定义模板路径
			$this->load->get_templates('vod',2);

            echo '<LINK href="'.base_url().'packs/admin/css/style.css" type="text/css" rel="stylesheet"><br>';

            if(!empty($path)){
              foreach ($path as $t) {
                  ob_end_flush();//关闭缓存 
				  $t=str_replace(".html","",$t);
				  $t=str_replace("opt-","",$t);
				  $Mark_Text=$this->CsdjTpl->opt($t,true);

				  $Htmllink=Web_Path.'opt/'.$t.'_vod.html';

                  //生成
			      write_file(FCPATH.$Htmllink,$Mark_Text);
				  echo "&nbsp;<font style=font-size:10pt;>生成自定义页面:<font color=red>".$Htmllink."</font>成功:<a href=".$Htmllink." target=_blank>".$Htmllink."</a></font><br/>";
                  ob_flush();flush();
			  }
			}

    	    $url=site_url('vod/admin/html/opt');
			echo("</br><b>&nbsp;自定义页面全部生成完毕~!</b><script>setTimeout('updatenext();',".Html_StopTime."000);function updatenext(){location.href='".$url."';}</script>");
	}

    //生成播放页
	public function getplay($row){
			//评论
			$dance_pl=get_pl('vod',$row['id']);
			$rows=$row; //先保存数组保留下面使用
			$id=$rows['id'];
		    //播放页
		    if(!empty($row['purl'])){
	               $Data_Arr=explode("#cscms#",$row['purl']);
				   for($i=0;$i<count($Data_Arr);$i++){
 
		                  $DataList_Arr=explode("\n",$Data_Arr[$i]);
						  for($j=0;$j<count($DataList_Arr);$j++){
			                       //摧毁部分需要超级链接字段数组
			                       unset($row['zhuyan']);
			                       unset($row['daoyan']);
			                       unset($row['yuyan']);
			                       unset($row['diqu']);
			                       unset($row['tags']);
			                       unset($row['year']);
								   //动态人气
			                       unset($row['hits']);
			                       unset($row['yhits']);
			                       unset($row['zhits']);
			                       unset($row['rhits']);
			                       unset($row['dhits']);
			                       unset($row['chits']);
			                       unset($row['xhits']);
			                       unset($row['shits']);

			                       $arr['cid']=getChild($row['cid']);
			                       $arr['uid']=$row['uid'];
			                       $arr['singerid']=$row['singerid'];
			                       $arr['tags']=$rows['tags'];
			                       $skins=$row['skins'];
			                       if(empty($skins) || $skins=='play.html'){
			                            $skins=getzd('vod_list','skins3',$row['cid']);
			                       }
			                       if(empty($skins)) $skins='play.html';
			                       //装载模板并输出
	                               $Mark_Text=$this->CsdjTpl->plub_show('vod',$row,$arr,TRUE,$skins,$row['name'],$row['name']);
			                       $Mark_Text=str_replace("[vod:pl]",$dance_pl,$Mark_Text);
			                       //分类地址、名称
			                       $Mark_Text=str_replace("[vod:link]",LinkUrl('show','id',$row['id'],1,'vod'),$Mark_Text);
			                       $Mark_Text=str_replace("[vod:classlink]",LinkUrl('lists','id',$row['cid'],1,'vod'),$Mark_Text);
			                       $Mark_Text=str_replace("[vod:classname]",$this->CsdjDB->getzd('vod_list','name',$row['cid']),$Mark_Text);
			                       //主演、导演、标签、年份、地区、语言加超级连接
			                       $Mark_Text=str_replace("[vod:zhuyan]",SearchLink($rows['zhuyan'],'zhuyan'),$Mark_Text);
			                       $Mark_Text=str_replace("[vod:daoyan]",SearchLink($rows['daoyan'],'daoyan'),$Mark_Text);
			                       $Mark_Text=str_replace("[vod:yuyan]",SearchLink($rows['yuyan'],'yuyan'),$Mark_Text);
			                       $Mark_Text=str_replace("[vod:diqu]",SearchLink($rows['diqu'],'diqu'),$Mark_Text);
			                       $Mark_Text=str_replace("[vod:tags]",SearchLink($rows['tags']),$Mark_Text);
			                       $Mark_Text=str_replace("[vod:year]",SearchLink($rows['year'],'year'),$Mark_Text);
                                   $Mark_Text=Vod_Playlist($Mark_Text,'play',$id,$row['purl']);
			                       //播放器
                                   if($i>=count($Data_Arr)) $i=0;
		                           $DataList_Arr=explode("\n",$Data_Arr[$i]);
		                           $Dataurl_Arr=explode('$',$DataList_Arr[$j]);

		                           $laiyuan=str_replace("\r","",@$Dataurl_Arr[2]); //来源
		                           $url=$Dataurl_Arr[1];  //地址
		                           $pname=$Dataurl_Arr[0];  //当前集数
		                           $Mark_Text=str_replace("[vod:qurl]",$url,$Mark_Text);
		                           $Mark_Text=str_replace("[vod:laiy]",$laiyuan,$Mark_Text);
		                           $Mark_Text=str_replace("[vod:ji]",$pname,$Mark_Text);

		                           if(count($DataList_Arr)>($j+1)){
			                           $DataNext=$DataList_Arr[($j+1)];
			                           $DataNextArr=explode('$',$DataNext);
			                           if(count($DataNextArr)==2) $DataNext=$DataNextArr[1];
			                           $xurl=VodPlayUrl('play',$id,$i,($j+1));
		                               $Dataurl_Arr2=explode('$',$DataList_Arr[($j+1)]);
		                           }else{
			                           $DataNext=$DataList_Arr[$j];
			                           $DataNextArr=explode('$',$DataNext);
			                           if(count($DataNextArr)==2) $DataNext=$DataNextArr[1];			
			                           $xurl=VodPlayUrl('play',$id,$i,$j);
		                           }
                                   if($j==0){
			                           $surl=VodPlayUrl('play',$id,$i,$j);
                                   }else{
			                           $surl=VodPlayUrl('play',$id,$i,($j-1));
                                   }
                                   $psname='';
			                       for($a=0;$a<count($Data_Arr);$a++){
				                          $jis='';
			                              $Ji_Arr=explode("\n",$Data_Arr[$a]);
                                          for($k=0;$k<count($Ji_Arr);$k++){
			                                   $Ly_Arr=explode('$',$Ji_Arr[$k]);
						                       $jis.=$Ly_Arr[0].'$$'.@$Ly_Arr[2].'====';
				                          }
				                          $psname.=substr($jis,0,-4).'#cscms#';
			                       }
                                   $player_arr=str_replace("\r","",substr($psname,0,-7));
	                               if($laiyuan=='xgvod'||$laiyuan=='jjvod'||$laiyuan=='yyxf'||$laiyuan=='bdhd'||$laiyuan=='qvod'){
	                                   $url=str_replace("+","__",base64_encode($url));
			                       }else{
	                                   $url=escape($url);
			                       }
		                           $player="<script type='text/javascript' src='".site_url('vod/play/pay/'.$id.'/'.$i.'/'.$j)."'></script><script type='text/javascript' src='".hitslink('play/form','vod')."'></script><script type='text/javascript'>var cs_playlink='".VodPlayUrl('play',$id,$i,$j,1)."';var cs_did='".$id."';var player_name='".$player_arr."';var cs_pid='".$j."';var cs_zid='".$i."';var cs_vodname='".$row['name']." - ".$pname."';var cs_root='http://".Web_Url.Web_Path."';var cs_width=".CS_Play_sw.";var cs_height=".CS_Play_sh.";var cs_surl='".$surl."';var cs_xurl='".$xurl."';var cs_laiy='".$laiyuan."';var cs_adloadtime='".CS_Play_AdloadTime."';</script>
								   <iframe border=\"0\" name=\"cscms_vodplay\" id=\"cscms_vodplay\" src=\"".Web_Path."packs/vod_player/play.html\" marginwidth=\"0\" framespacing=\"0\" marginheight=\"0\" noresize=\"\" vspale=\"0\" style=\"z-index: 9998;\" frameborder=\"0\" height=\"".(CS_Play_sh+30)."\" scrolling=\"no\" width=\"100%\"></iframe>";
		                           $Mark_Text=str_replace("[vod:player]",$player,$Mark_Text);
		                           $Mark_Text=str_replace("[vod:surl]",$surl,$Mark_Text);
		                           $Mark_Text=str_replace("[vod:xurl]",$xurl,$Mark_Text);
				                   //解析动态人气标签
				                   $Mark_Text=str_replace("[vod:hits]","<script src='".hitslink('hits/dt/hits/'.$id,'vod')."'></script>",$Mark_Text);
				                   $Mark_Text=str_replace("[vod:yhits]","<script src='".hitslink('hits/dt/yhits/'.$id,'vod')."'></script>",$Mark_Text);
				                   $Mark_Text=str_replace("[vod:zhits]","<script src='".hitslink('hits/dt/zhits/'.$id,'vod')."'></script>",$Mark_Text);
				                   $Mark_Text=str_replace("[vod:rhits]","<script src='".hitslink('hits/dt/rhits/'.$id,'vod')."'></script>",$Mark_Text);
				                   $Mark_Text=str_replace("[vod:shits]","<script src='".hitslink('hits/dt/shits/'.$id,'vod')."'></script>",$Mark_Text);
				                   $Mark_Text=str_replace("[vod:xhits]","<script src='".hitslink('hits/dt/xhits/'.$id,'vod')."'></script>",$Mark_Text);
				                   $Mark_Text=str_replace("[vod:dhits]","<script src='".hitslink('hits/dt/dhits/'.$id,'vod')."'></script>",$Mark_Text);
				                   $Mark_Text=str_replace("[vod:chits]","<script src='".hitslink('hits/dt/chits/'.$id,'vod')."'></script>",$Mark_Text);
				                   $Mark_Text=str_replace("[vod:pfen]","<script src='".hitslink('hits/dt/pfen/'.$id,'vod')."'></script>",$Mark_Text);
				                   $Mark_Text=str_replace("[vod:pfenbi]","<script src='".hitslink('hits/dt/pfenbi/'.$id,'vod')."'></script>",$Mark_Text);
			                       //增加人气
			                       $Mark_Text=hits_js($Mark_Text,hitslink('hits/ids/'.$id,'vod'));
                                   //替换后台控制器
                                   $Mark_Text=str_replace(SELF,'index.php',$Mark_Text);

                                   //获取静态路径
				                   $Htmllinks=VodPlayUrl('play',$id,$i,$j);
								   //生成地址转换
								   $Htmllink=adminhtml($Htmllinks,'vod');
                                   //生成
			                       write_file(FCPATH.$Htmllink,$Mark_Text);
						  }
				          echo "&nbsp;&nbsp;&nbsp;<font style=font-size:9pt;color:red;>--生成第".($i+1)."组播放器：<a href=".$Htmllinks." target=_blank>".$Htmllinks."</a></font><br/>";
				   }
		    }
	}

    //获取排序名称
	public function gettype($fid){
		    $str="ID";
            switch($fid){
                   case 'id':$str="ID";break;
                   case 'news':$str="更新时间";break;
                   case 'reco':$str="最新推荐";break;
                   case 'hits':$str="总人气";break;
                   case 'yhits':$str="月人气";break;
                   case 'zhits':$str="周人气";break;
                   case 'rhits':$str="日人气";break;
                   case 'shits':$str="收藏";break;
                   case 'xhits':$str="下载";break;
                   case 'dhits':$str="被顶";break;
                   case 'chits':$str="被踩";break;
                   case 'phits':$str="评分";break;
			}
			return $str;
	}
}