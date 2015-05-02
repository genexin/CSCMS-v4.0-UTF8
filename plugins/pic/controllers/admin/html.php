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
		    $this->load->helper('pic');
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
			$this->load->get_templates('pic',2);
		    $Mark_Text=$this->CsdjTpl->plub_index('pic','index.html',true);
			write_file(FCPATH.$index_url,$Mark_Text);
            admin_msg('板块首页生成完毕,<a target="_blank" href="'.Web_Path.$index_url.'"><font color="red">浏览</font></a>','###');
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
				admin_msg('相册分类未开启生成~!','javascript:history.back();','no');
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
				$fid=($ac=='all')?'id,news,reco,hits,yhits,zhits,rhits,xhits,shits':'id';
			}

            //生成全部分类获取全部分类ID
            if($ac=='all' && $nums==0){
				   $cid=array();
                   $query = $this->db->query("SELECT id FROM ".CS_SqlPrefix."pic_list where yid=0 order by xid asc"); 
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
                    admin_msg('请选择要生成的分类~!',site_url('pic/admin/html/type'));
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
                    admin_msg('所有分类全部生成完毕~!',site_url('pic/admin/html/type'));
			}

			$id=$arr[$nums]; //当前分类ID
			$type=$arr2[$numx]; //当前排序

			//重新定义模板路径
			$this->load->get_templates('pic',2);

			//获取分类信息
			$row=$this->CsdjDB->get_row_arr('pic_list','*',$id);
            $template=!empty($row['skins'])?$row['skins']:'list.html';
			$ids['cid']=getChild($id); //获取子分类

            if($datacount==0){
				 $template=$this->load->view($template,'',true);
				 $pagesize=10;
			     preg_match_all('/{cscms:([\S]+)\s+(.*?pagesize=\"([\S]+)\".*?)}([\s\S]+?){\/cscms:\1}/',$template,$page_arr);
		         if(!empty($page_arr) && !empty($page_arr[3][0])){
					    $pagesize=$page_arr[3][0];
				 }
				 $sqlstr="select id from ".CS_SqlPrefix."pic_type where yid=0 and hid=0 and cid IN (". getChild($id).")";
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
				  $Htmllinks=LinkUrl('lists',$type,$id,$i,'pic');
				  //转换成生成路径
				  $Htmllink=adminhtml($Htmllinks,'pic');
				  $skins=empty($row['skins'])?'list.html':$row['skins'];
				  $Mark_Text=$this->CsdjTpl->plub_list($row,$id,$type,$i,$ids,true,$skins,'lists','pic',$row['name'],$row['name']);
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
				  $url=site_url('pic/admin/html/type_save').$uri.'&nums='.$nums.'&numx='.$numx.'&start='.($i+1);
			      exit("</br>&nbsp;&nbsp;<b>暂停".Html_StopTime."秒后继续下一页&nbsp;>>>>&nbsp;&nbsp;<a target='_blank' href='".$url."'>如果您的 浏览器没有跳转，请点击继续...</a></b><script>setTimeout('updatenext();',".Html_StopTime."000);function updatenext(){location.href='".$url."';}</script>");
    		}else{  
    	          $url=site_url('pic/admin/html/type_save').$uri.'&nums='.($nums+1);
			      $str="&nbsp;&nbsp;<b>暂停".Html_StopTime."秒后继续&nbsp;>>>>&nbsp;&nbsp;<a target='_blank' href='".$url."'>如果您的 浏览器没有跳转，请点击继续...</a></b>";
            }

            //判断生成完毕
			if(($numx+1)>=$len2){ //全部完成
                $url=site_url('pic/admin/html/type_save').$uri.'&nums='.($nums+1);
				$str="&nbsp;&nbsp;<b><font color=#0000ff>所有排序页面全部生成完毕!</font>&nbsp;>>>>&nbsp;&nbsp;<a href='".site_url('pic/admin/html/type_save').$uri.'&nums='.($nums+1)."'>如果您的 浏览器没有跳转，请点击继续...</a></b>";
			}else{ //当前排序方式完成
                $url=site_url('pic/admin/html/type_save').$uri.'&nums='.$nums.'&numx='.($numx+1);
				$str='&nbsp;&nbsp;<b>按<font color=#0000ff>'.$this->gettype($type).'排序</font>分类生成完毕!&nbsp;>>>>&nbsp;&nbsp;<a href="'.site_url('pic/admin/html/type_save').$uri.'&nums='.$nums.'&numx='.($numx+1).'">如果您的 浏览器没有跳转，请点击继续...</a></b>';
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
				admin_msg('相册内容页未开启生成~!','javascript:history.back();','no');
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
				 $sqlstr="select id from ".CS_SqlPrefix."pic_type where yid=0 and hid=0 ".$str.$limit;
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
                   admin_msg('所有内容页全部生成完毕~!',site_url('pic/admin/html/show'));
			}


            //公众URI
			$uri='?day='.$day.'&cid='.$cid.'&ids='.$ids.'&newid='.$newid.'&ksid='.$ksid.'&jsid='.$jsid.'&kstime='.$kstime.'&jstime='.$jstime.'&pagesize='.$pagesize.'&pagejs='.$pagejs.'&datacount='.$datacount;

			//重新定义模板路径
			$this->load->get_templates('pic',2);

            echo '<LINK href="'.base_url().'packs/admin/css/style.css" type="text/css" rel="stylesheet"><br>';
			echo '&nbsp;&nbsp;<b>正在开始生成相册内容,分<font color=red>'.$pagejs.'</font>次生成，当前第<font color=red>'.$page.'</font>次</b><br/>';

            $sql_string="select * from ".CS_SqlPrefix."pic_type where yid=0 and hid=0 ".$str." order by id desc";
            $sql_string.=' limit '. $pagesize*($page-1) .','. $pagesize;
			$query = $this->db->query($sql_string);

			//获取内容页是否需要生成
			$html=config('Html_Uri','pic');

            foreach ($query->result_array() as $row) {
                  ob_end_flush();//关闭缓存 
				  $id=$row['id'];
                  //获取静态路径
				  $Htmllinks=LinkUrl('show','id',$row['id'],0,'pic');
				  //转换成生成路径
				  $Htmllink=adminhtml($Htmllinks,'pic');

				  //摧毁部分需要超级链接字段数组
				  $rows=$row; //先保存数组保留下面使用
				  unset($row['tags']);
				  unset($row['hits']);
				  unset($row['yhits']);
				  unset($row['zhits']);
				  unset($row['rhits']);
				  unset($row['dhits']);
				  unset($row['chits']);
				  unset($row['content']);
				  //默认模板
			      $skins=empty($row['skins'])?'show.html':$row['skins'];

				  $arr['cid']=getChild($row['cid']);
				  $arr['uid']=$row['uid'];
				  $arr['tags']=$rows['tags'];
				  $arr['sid']=$row['id'];
				  //装载模板并输出
	        	  $Mark_Text=$this->CsdjTpl->plub_show('pic',$row,$arr,TRUE,$skins,$row['name'],$row['name']);
				  //评论
				  $Mark_Text=str_replace("[pic:pl]",get_pl('pic',$id),$Mark_Text);
				  //分类地址、名称
			      $Mark_Text=str_replace("[pic:link]",LinkUrl('show','id',$row['id'],1,'pic'),$Mark_Text);
			      $Mark_Text=str_replace("[pic:classlink]",LinkUrl('lists','id',$row['cid'],1,'pic'),$Mark_Text);
			      $Mark_Text=str_replace("[pic:classname]",$this->CsdjDB->getzd('pic_list','name',$row['cid']),$Mark_Text);
				  //获取上下篇
            	  preg_match_all('/[pic:slink]/',$Mark_Text,$arr);
		    	  if(!empty($arr[0]) && !empty($arr[0][0])){
                   	  $rowd=$this->db->query("Select id,cid,pic,name from ".CS_SqlPrefix."pic_type where yid=0 and hid=0 and id<".$id." order by id desc limit 1")->row();
				  	  if($rowd){
			             $Mark_Text=str_replace("[pic:slink]",LinkUrl('show','id',$rowd->id,1,'pic'),$Mark_Text);
			             $Mark_Text=str_replace("[pic:sname]",$rowd->name,$Mark_Text);
			             $Mark_Text=str_replace("[pic:sid]",$rowd->id,$Mark_Text);
			             $Mark_Text=str_replace("[pic:spic]",piclink('pic',$rowd->pic),$Mark_Text);
				   	  }else{
			             $Mark_Text=str_replace("[pic:slink]","#",$Mark_Text);
			             $Mark_Text=str_replace("[pic:sname]","没有了",$Mark_Text);
			             $Mark_Text=str_replace("[pic:sid]",0,$Mark_Text);
			             $Mark_Text=str_replace("[pic:spic]",piclink('pic',''),$Mark_Text);
				   	  }
				  }
				  unset($arr);
            	  preg_match_all('/[pic:xlink]/',$Mark_Text,$arr);
		    	  if(!empty($arr[0]) && !empty($arr[0][0])){
                  	  $rowd=$this->db->query("Select id,cid,pic,name from ".CS_SqlPrefix."pic_type where yid=0 and hid=0 and id>".$id." order by id asc limit 1")->row();
				  	  if($rowd){
			             $Mark_Text=str_replace("[pic:xlink]",LinkUrl('show','id',$rowd->id,1,'pic'),$Mark_Text);
			             $Mark_Text=str_replace("[pic:xname]",$rowd->name,$Mark_Text);
			             $Mark_Text=str_replace("[pic:xid]",$rowd->id,$Mark_Text);
			             $Mark_Text=str_replace("[pic:xpic]",piclink('pic',$rowd->pic),$Mark_Text);
				   	  }else{
			             $Mark_Text=str_replace("[pic:xlink]","#",$Mark_Text);
			             $Mark_Text=str_replace("[pic:xname]","没有了",$Mark_Text);
			             $Mark_Text=str_replace("[pic:xid]",0,$Mark_Text);
			             $Mark_Text=str_replace("[pic:xpic]",piclink('pic',''),$Mark_Text);
				   	  }
				  }
				  unset($arr);
				  //标签加超级连接
				  $Mark_Text=str_replace("[pic:tags]",SearchLink($rows['tags']),$Mark_Text);
				  //动态人气
				  $Mark_Text=str_replace("[pic:hits]","<script src='".hitslink('hits/dt/hits/'.$id,'pic')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[pic:yhits]","<script src='".hitslink('hits/dt/yhits/'.$id,'pic')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[pic:zhits]","<script src='".hitslink('hits/dt/zhits/'.$id,'pic')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[pic:rhits]","<script src='".hitslink('hits/dt/rhits/'.$id,'pic')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[pic:dhits]","<script src='".hitslink('hits/dt/dhits/'.$id,'pic')."'></script>",$Mark_Text);
				  $Mark_Text=str_replace("[pic:chits]","<script src='".hitslink('hits/dt/chits/'.$id,'pic')."'></script>",$Mark_Text);
			      //获取当前相册总数
			      $pcount=$this->db->query("Select id from ".CS_SqlPrefix."pic where sid=".$id." and hid=0 and yid=0")->num_rows();
			      $Mark_Text=str_replace("[pic:count]",$pcount,$Mark_Text);
			      //第一张图片
			      $rowp=$this->db->query("Select pic,content from ".CS_SqlPrefix."pic where sid=".$id." and hid=0 and yid=0 order by id desc limit 1")->row();
                  $pics=($rowp)?$rowp->pic:'';
                  $content=($rowp)?$rowp->content:'';
			      $Mark_Text=str_replace("[pic:url]",piclink('pic',$pics),$Mark_Text);
			      $Mark_Text=str_replace("[pic:content]",$content,$Mark_Text);
				  //增加人气
			      $Mark_Text=hits_js($Mark_Text,hitslink('hits/ids/'.$id,'pic'));
                  //生成
			      write_file(FCPATH.$Htmllink,$Mark_Text);
				  echo "&nbsp;<font style=font-size:10pt;>生成相册:<font color=red>".$row['name']."</font>成功:<a href=".$Htmllinks." target=_blank>".$Htmllinks."</a></font><br/>";
                  ob_flush();flush();

			}
            if(!empty($ids)){
                $url='javascript:history.back();';
			    $str="&nbsp;&nbsp;<b>全部生成完毕&nbsp;>>>>&nbsp;&nbsp;<a href='".$url."'>如果您的 浏览器没有跳转，请点击继续...</a></b>";
			}else{ 
    	        $url=site_url('pic/admin/html/show_save').$uri.'&page='.($page+1);
			    $str="&nbsp;&nbsp;<b>暂停".Html_StopTime."秒后继续&nbsp;>>>>&nbsp;&nbsp;<a href='".$url."'>如果您的 浏览器没有跳转，请点击继续...</a></b>";
			}
			echo("</br>".$str."<script>setTimeout('updatenext();',".Html_StopTime."000);function updatenext(){location.href='".$url."';}</script>");
	}

    //其他页
	public function opt()
	{
		    //获取自定义模板
			$Skins_Dir=config('Skins_Dir','pic');
			$this->load->helper('directory');
            $path=FCPATH.'plugins/pic/skins/'.$Skins_Dir;
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
			$this->load->get_templates('pic',2);

            echo '<LINK href="'.base_url().'packs/admin/css/style.css" type="text/css" rel="stylesheet"><br>';
            if(!empty($path)){
              foreach ($path as $t) {
                  ob_end_flush();//关闭缓存 
				  $t=str_replace(".html","",$t);
				  $t=str_replace("opt-","",$t);
				  $Mark_Text=$this->CsdjTpl->opt($t,true);

				  $Htmllink=Web_Path.'opt/'.$t.'_pic.html';

                  //生成
			      write_file(FCPATH.$Htmllink,$Mark_Text);
				  echo "&nbsp;<font style=font-size:10pt;>生成自定义页面:<font color=red>".$Htmllink."</font>成功:<a href=".$Htmllink." target=_blank>".$Htmllink."</a></font><br/>";
                  ob_flush();flush();
			  }
			}

    	    $url=site_url('pic/admin/html/opt');
			echo("</br><b>&nbsp;自定义页面全部生成完毕~!</b><script>setTimeout('updatenext();',".Html_StopTime."000);function updatenext(){location.href='".$url."';}</script>");
	}

    //获取排序名称
	public function gettype($fid){
		    $str="ID";
            switch($fid){
                   case 'id':$str="ID";break;
                   case 'pic':$str="更新时间";break;
                   case 'reco':$str="最新推荐";break;
                   case 'hits':$str="总人气";break;
                   case 'yhits':$str="月人气";break;
                   case 'zhits':$str="周人气";break;
                   case 'rhits':$str="日人气";break;
			}
			return $str;
	}
}
