<?php
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-03-10
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 第三方登录类
 */
class Denglu {

    function __construct ()
	{
		//返回地址
		$this->redirect_uri = spacelink("open/callback");
	 	$this->ci = &get_instance();
	}

    //登录
	public function login($ac,$log_state=''){
        if(CS_Appmode==1){  //官方登录
              $log_url="http://denglu.chshcms.com/denglu?ac=".$ac."&appid=".CS_Appid."&redirect_uri=".$this->redirect_uri."&state=".$log_state."&getdate=".time();
              header("Location: $log_url"); 
		}else{  //独立
			  $mode=$ac.'_login';
              $this->$mode($log_state);
		}
    }

    //返回
	public function callback($ac,$log_state=''){
        if(CS_Appmode==1){  //官方登录
              return $this->cscms_callback($ac,$log_state);
		}else{  //独立
			  $mode=$ac.'_callback';
              return $this->$mode($log_state);
		}
    }

	//QQ登录
    public  function qq_login($log_state='') {
         $scope= "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
         $login_url='https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id='.CS_Qqid.'&redirect_uri='.$this->redirect_uri.'&state='.$log_state.'&scope='.$scope;
         header("Location:$login_url");
    }

    //新浪微博登录
    public  function weibo_login($log_state='') {
         echo "
		  <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		  <html xmlns=\"http://www.w3.org/1999/xhtml\">
		  <head>
		  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
		  <title>新浪微博登入</title>
		  </head>
		  <script src=\"http://tjs.sjs.sinajs.cn/t35/apps/opent/js/frames/client.js\" language=\"JavaScript\"></script>
		  <script> 
		  function authLoad(){
			App.AuthDialog.show({
			client_id : '".CS_Wbid."',    //必选，appkey
			redirect_uri : '".$this->redirect_uri."',     //必选，授权后的回调地址
			height: 120    //可选，默认距顶端120px
			});
		  }
		  </script>
		  <body onload=\"authLoad();\">
		  </body>
		  </html>";
    }

    //百度登录
    public  function baidu_login($log_state='') {
         $login_url='https://openapi.baidu.com/oauth/2.0/authorize?response_type=code&client_id='.CS_Bdid.'&redirect_uri='.$this->redirect_uri.'&scope=netdisk&display=';
         header("Location:$login_url");
    }

    //人人登录
    public  function renren_login($log_state='') {
         $login_url="http://graph.renren.com/oauth/authorize?client_id=".CS_Rrid."&redirect_uri=".$this->redirect_uri."&response_type=code&state=".$log_state."&display=&x_renew=";
         header("Location:$login_url");
    }

    //开心登录
    public  function kaixin_login($log_state='') {
         $login_url="http://api.kaixin001.com/oauth2/authorize?response_type=code&client_id=".CS_Kxid."&redirect_uri=".$this->redirect_uri."&state=".$log_state."&scope=basic&display=popup";
         header("Location:$login_url");
    }

    //豆瓣登录
    public  function douban_login($log_state='') {
         $scope= "shuo_basic_r,shuo_basic_w,douban_basic_common";
         $login_url="https://www.douban.com/service/auth2/auth?response_type=code&client_id=".CS_Dbid."&redirect_uri=".$this->redirect_uri."&state=".$log_state."&scope=&display=".$scope;
         header("Location:$login_url");
    }

	//官方登录返回
    public  function cscms_callback($ac,$log_state='') {
         $state=$this->ci->input->get_post('state', TRUE, TRUE);
         $uid=$this->ci->input->get_post('uid', TRUE, TRUE);
         $username=$this->ci->input->get_post('username', TRUE, TRUE);
         $userlogo=$this->ci->input->get_post('userlogo', TRUE, TRUE);

         if(empty($state) || empty($uid) || empty($username)){
              msg_url('登录失败，返回参数错误~!',spacelink('login')); 
	     }

		 if($state!=$log_state){
              msg_url('非法登录~!',spacelink('login')); 
		 }
		 //查看数据库是否存在
		 $row=$this->ci->db->query("SELECT id,uid,nickname,avatar FROM ".CS_SqlPrefix."useroauth where csid=".intval($uid)."")->row();
		 if($row){
			   $_SESSION['denglu__id'] = $row->id;
			   $_SESSION['denglu__name'] = $row->nickname;
			   $_SESSION['denglu__logo'] = $row->avatar;
		       return $row->uid;
		 }else{
			   $add['cid']=$this->class_id($ac);
			   $add['csid']=$uid;
			   $add['nickname']=$username;
			   $add['avatar']=$userlogo;
			   $add['oid']='';
			   $add['access_token']='';
			   $add['refresh_token']='';
			   $ids=$this->ci->CsdjDB->get_insert('useroauth',$add);
			   $_SESSION['denglu__id'] = intval($ids);
			   $_SESSION['denglu__name'] = $username;
			   $_SESSION['denglu__logo'] = $userlogo;
		       return 0;
		 }
    }

	//QQ登录返回
    public  function qq_callback($log_state='') {
         $state=$this->ci->input->get_post('state', TRUE, TRUE);
         $code=$this->ci->input->get('code', TRUE);

         if(empty($state) || empty($code)){
              msg_url('登录失败，返回参数错误~!',spacelink('login')); 
	     }

		 if($state!=$log_state){
              msg_url('非法登录~!',spacelink('login')); 
		 }

         //获取ACCSEE_TOTEN
         $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
                      . "client_id=" . CS_Qqid. "&redirect_uri=" . urlencode($this->redirect_uri)
                      . "&client_secret=" . CS_Qqkey. "&code=" . $code;
         $response=$this->get_url_contents($token_url);

         if (strpos($response, "callback") !== false){
               msg_url('登入失败，没获取到access_token！',spacelink('login')); 
         }
         $params = array();
         parse_str($response, $params);
         $access_token=$params['access_token'];
         $refresh_token=$params['refresh_token'];
         $expire_at=$params['expire_at'];
         //获取OPENID
         $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$access_token;
         $str  = $this->get_url_contents($graph_url);
         if (strpos($str, "callback") !== false){
               $lpos = strpos($str, "(");
               $rpos = strrpos($str, ")");
               $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		 }
         $user = json_decode($str);
         if (isset($user->error)){
              msg_url('获取openid失败！',spacelink('login')); 
         }
         $qqid=$user->openid;

         //获取用户信息
         $get_user_info = "https://graph.qq.com/user/get_user_info?"
                   . "access_token=" . $access_token
                   . "&oauth_consumer_key=" . CS_Qqid
                   . "&openid=" . $qqid
                   . "&format=json";
         $info=$this->get_url_contents($get_user_info);
         $arr = json_decode($info, true);

		 //查看数据库是否存在
		 $row=$this->ci->db->query("SELECT id,uid,nickname,avatar FROM ".CS_SqlPrefix."useroauth where oid='".$qqid."' and cid=1")->row();
		 if($row){
			   $_SESSION['denglu__id'] = $row->id;
			   $_SESSION['denglu__name'] = $row->nickname;
			   $_SESSION['denglu__logo'] = $row->avatar;
		       return $row->uid;
		 }else{
			   $add['cid']=1;
			   $add['nickname']=get_bm($arr['nickname']);
			   $add['avatar']=$arr['figureurl_2'];
			   $add['oid']=$qqid;
			   $add['access_token']=$access_token;
			   $add['refresh_token']=$refresh_token;
			   $add['expire_at']=$expire_at;
			   $ids=$this->ci->CsdjDB->get_insert('useroauth',$add);
			   $_SESSION['denglu__id'] = intval($ids);
			   $_SESSION['denglu__name'] = $add['nickname'];
			   $_SESSION['denglu__logo'] = $add['avatar'];
		       return 0;
		 }
    }

    //POST 、 GET
    function get_url_contents($url,$post='',$type='get'){

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
                if($type=='post') {
		            curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
                }else{
		            curl_setopt($ch, CURLOPT_POST, 0);
                }
		        curl_setopt($ch, CURLOPT_USERAGENT, 'cscms');
		        $result = curl_exec($ch);
                curl_close($ch);
                return $result;
    }

    //获取登录分类ID
    public  function class_id($ac) {
         if($ac=='weibo'){
              $cid=2;
		 }elseif($ac=='baidu'){
              $cid=3;
		 }elseif($ac=='renren'){
              $cid=4;
		 }elseif($ac=='kaixin'){
              $cid=5;
		 }elseif($ac=='douban'){
              $cid=6;
		 }else{
              $cid=1;
		 }
		 return $cid;
	}
}


