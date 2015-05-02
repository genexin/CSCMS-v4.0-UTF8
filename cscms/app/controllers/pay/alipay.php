<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-10
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Alipay extends Cscms_Controller {

	function __construct(){
		    parent::__construct();
		    $this->load->model('CsdjUser');
			$this->lang->load('pay');
	}

    //请求支付
	public function index()
	{
            $this->CsdjUser->User_Login();
		    $id=(int)$this->uri->segment(4); //订单ID
            if($id==0)  msg_url(L('pay_01'),spacelink('pay'));
            $row=$this->CsdjDB->get_row('pay','*',$id);
			if(!$row || $row->uid!=$_SESSION['cscms__id']){
                 msg_url(L('pay_02'),spacelink('pay'));
			}

            if(defined('MOBILE')){ //手机支付
				 require_once(CSCMSPATH."pay/alipay_wap/alipay.config.php");
				 require_once(CSCMSPATH."pay/alipay_wap/lib/alipay_submit.class.php");
				 $format = "xml";
				 $v = "2.0";
				 $req_id = date('Ymdhis');
				 $notify_url = site_url("pay/alipay/notify_url");
				 $call_back_url = site_url("pay/alipay/return_url");
				 $merchant_url = site_url("user/pay/lists");
				 $seller_email = CS_Alipay_Name;
				 $out_trade_no = $row->dingdan;
				 $subject = L('pay_03',array($_SESSION['cscms__name']));
				 $total_fee = $row->rmb;
				 $req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $merchant_url . '</merchant_url></direct_trade_create_req>';
				 //构造要请求的参数数组，无需改动
				 $para_token = array(
						 "service" => "alipay.wap.trade.create.direct",
						 "partner" => trim($alipay_config['partner']),
						 "sec_id" => trim($alipay_config['sign_type']),
						 "format"	=> $format,
						 "v"	=> $v,
						 "req_id"	=> $req_id,
						 "req_data"	=> $req_data,
						 "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
				 );
				 //建立请求
				 $alipaySubmit = new AlipaySubmit($alipay_config);
				 $html_text = $alipaySubmit->buildRequestHttp($para_token);
				 $html_text = urldecode($html_text);
				 $para_html_text = $alipaySubmit->parseResponse($html_text);
				 $request_token = $para_html_text['request_token'];
				 $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
				 //构造要请求的参数数组，无需改动
				 $parameter = array(
						 "service" => "alipay.wap.auth.authAndExecute",
						 "partner" => trim($alipay_config['partner']),
						 "sec_id" => trim($alipay_config['sign_type']),
						 "format"	=> $format,
						 "v"	=> $v,
						 "req_id"	=> $req_id,
						 "req_data"	=> $req_data,
						 "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
				 );
				 //建立请求
				 $alipaySubmit = new AlipaySubmit($alipay_config);
				 $html_text = $alipaySubmit->buildRequestForm($parameter, 'get', L('pay_04'));
				 echo $html_text;
            }elseif(CS_Alipay_JK==1){ //双功能
				 require_once(CSCMSPATH."pay/alipay_trade/alipay.config.php");
				 require_once(CSCMSPATH."pay/alipay_trade/lib/alipay_submit.class.php");
        		 $payment_type = "1";
        		 $notify_url = site_url("pay/alipay/notify_url");
        		 $return_url = site_url("pay/alipay/return_url");
        		 $seller_email = CS_Alipay_Name;
        		 $out_trade_no = $row->dingdan;
       		     $subject = L('pay_03',array($_SESSION['cscms__name']));
        		 $price = $row->rmb;
        		 $quantity = "1";
        		 $logistics_fee = "0.00";
        		 $logistics_type = "EXPRESS";
        		 $logistics_payment = "SELLER_PAY";
       		     $body = $subject;
        		 $show_url = '';
        		 $receive_name = '';
        		 $receive_address = '';
        		 $receive_zip = '';
        		 $receive_phone = '';
        		 $receive_mobile = '';
				 //构造要请求的参数数组，无需改动
				 $parameter = array(
						 "service" => "trade_create_by_buyer",
						 "partner" => trim($alipay_config['partner']),
						 "payment_type"	=> $payment_type,
						 "notify_url"	=> $notify_url,
						 "return_url"	=> $return_url,
						 "seller_email"	=> $seller_email,
						 "out_trade_no"	=> $out_trade_no,
						 "subject"	=> $subject,
						 "price"	=> $price,
						 "quantity"	=> $quantity,
						 "logistics_fee"	=> $logistics_fee,
						 "logistics_type"	=> $logistics_type,
						 "logistics_payment"	=> $logistics_payment,
						 "body"	=> $body,
						 "show_url"	=> $show_url,
						 "receive_name"	=> $receive_name,
						 "receive_address"	=> $receive_address,
						 "receive_zip"	=> $receive_zip,
						 "receive_phone"	=> $receive_phone,
						 "receive_mobile"	=> $receive_mobile,
						 "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
				 );
				 //建立请求
				 $alipaySubmit = new AlipaySubmit($alipay_config);
				 $html_text = $alipaySubmit->buildRequestForm($parameter,"get", L('pay_02'));
				 echo $html_text;
			}elseif(CS_Alipay_JK==2){ //即时到账
				 require_once(CSCMSPATH."pay/alipay_direct/alipay.config.php");
				 require_once(CSCMSPATH."pay/alipay_direct/lib/alipay_submit.class.php");
				 $payment_type = "1";
        		 $notify_url = site_url("pay/alipay/notify_url");
        		 $return_url = site_url("pay/alipay/return_url");
        		 $seller_email = CS_Alipay_Name;
        		 $out_trade_no = $row->dingdan;
       		     $subject = L('pay_03',array($_SESSION['cscms__name']));
        		 $total_fee = $row->rmb;
        		 $body = $subject;
        		 $show_url = '';
        		 $anti_phishing_key = '';
        		 $exter_invoke_ip = '';
		 		 //构造要请求的参数数组，无需改动
		 		 $parameter = array(
				 		 "service" => "create_direct_pay_by_user",
				 		 "partner" => trim($alipay_config['partner']),
				 		 "payment_type"	=> $payment_type,
				 		 "notify_url"	=> $notify_url,
				 		 "return_url"	=> $return_url,
				 		 "seller_email"	=> $seller_email,
				 		 "out_trade_no"	=> $out_trade_no,
				 		 "subject"	=> $subject,
				 		 "total_fee"	=> $total_fee,
				 		 "body"	=> $body,
				 		 "show_url"	=> $show_url,
				 		 "anti_phishing_key"	=> $anti_phishing_key,
				 		 "exter_invoke_ip"	=> $exter_invoke_ip,
				 		 "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		 		 );
		 		 //建立请求
		 		 $alipaySubmit = new AlipaySubmit($alipay_config);
		 		 $html_text = $alipaySubmit->buildRequestForm($parameter,"get", L('pay_02'));
		 		 echo $html_text;

			}else{  //手动充值
				 echo '
  				 <FORM accept-charset="gbk" action="https://shenghuo.alipay.com/send/payment/fill.htm" method="post" id="form1" name="form1" onsubmit="document.charset=\'gbk\';">
  				 <INPUT type="hidden" name="optEmail" value="'.CS_Alipay_Name.'">
  				 <INPUT type="hidden" name="payAmount" value="'.$row->rmb.'">
  				 <INPUT type="hidden" name="title" value="'.get_bm('utf-8','gbk',L('pay_03',array($_SESSION['cscms__name']))).'">
  				 <INPUT type="hidden" name="memo" value="'.get_bm('utf-8','gbk',L('pay_05').$_SESSION['cscms__name'].L('pay_06').$row->dingdan).'">
  				 <script language="javascript">document.form1.submit();</script>
 				 </form>';
			}
	}

	//同步返回
	public function return_url()
	{
            $this->CsdjUser->User_Login();
            if(defined('MOBILE')){ //手机支付

				 require_once(CSCMSPATH."pay/alipay_wap/alipay.config.php");
				 require_once(CSCMSPATH."pay/alipay_wap/lib/alipay_notify.class.php");
                 $alipayNotify = new AlipayNotify($alipay_config);
                 $verify_result = $alipayNotify->verifyReturn();
                 if($verify_result) {//验证成功
					 //商户订单号
					 $out_trade_no = $this->input->get('out_trade_no',true,true);
					 //支付宝交易号
					 $trade_no = $this->input->get('trade_no',true,true);
					 //交易状态
					 $trade_status = $this->input->get('trade_status',true,true);
                     if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                         msg_url(L('pay_07').$out_trade_no,spacelink('pay'));
                     }else{
                         echo L('pay_08').$trade_status;
					 }
				 }else {
                     msg_url(L('pay_09'),spacelink('pay'));
				 }

            }elseif(CS_Alipay_JK==1){ //双功能

				 require_once(CSCMSPATH."pay/alipay_trade/alipay.config.php");
				 require_once(CSCMSPATH."pay/alipay_trade/lib/alipay_notify.class.php");
				 //计算得出通知验证结果
				 $alipayNotify = new AlipayNotify($alipay_config);
				 $verify_result = $alipayNotify->verifyReturn();
				 if($verify_result) {//验证成功
					 //商户订单号
					 $out_trade_no = $this->input->get('out_trade_no',true,true);
					 //支付宝交易号
					 $trade_no = $this->input->get('trade_no',true,true);
					 //交易状态
					 $trade_status = $this->input->get('trade_status',true,true);
    				 if($trade_status == 'WAIT_SELLER_SEND_GOODS') {  //付款成功，没有发货
                            msg_url(L('pay_10').$out_trade_no,spacelink('pay'));
    				 }elseif($trade_status == 'TRADE_FINISHED') {  //交易完成
                            msg_url(L('pay_07').$out_trade_no,spacelink('pay'));
				     }else{
                            echo L('pay_08').$trade_status;
    				 }
				 }else {
    				 msg_url(L('pay_09'),spacelink('pay'));
				 }

			}elseif(CS_Alipay_JK==2){ //即时到账

				 require_once(CSCMSPATH."pay/alipay_direct/alipay.config.php");
				 require_once(CSCMSPATH."pay/alipay_direct/lib/alipay_notify.class.php");
                 $alipayNotify = new AlipayNotify($alipay_config);
                 $verify_result = $alipayNotify->verifyReturn();
                 if($verify_result) {//验证成功
					 //商户订单号
					 $out_trade_no = $this->input->get('out_trade_no',true,true);
					 //支付宝交易号
					 $trade_no = $this->input->get('trade_no',true,true);
					 //交易状态
					 $trade_status = $this->input->get('trade_status',true,true);
                     if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                         msg_url(L('pay_07').$out_trade_no,spacelink('pay'));
                     }else{
                         echo L('pay_08').$trade_status;
					 }
				 }else {
                     msg_url(L('pay_09'),spacelink('pay'));
				 }
			}
	}

	//异步返回
	public function notify_url()
	{
            if(defined('MOBILE')){ //手机支付

				 require_once(CSCMSPATH."pay/alipay_wap/alipay.config.php");
				 require_once(CSCMSPATH."pay/alipay_wap/lib/alipay_notify.class.php");
				 $alipayNotify = new AlipayNotify($alipay_config);
				 $verify_result = $alipayNotify->verifyNotify();
				 if($verify_result) {
					 $doc = new DOMDocument();	
					 if ($alipay_config['sign_type'] == 'MD5') {
						 $doc->loadXML($_POST['notify_data']);
					 }
					 if ($alipay_config['sign_type'] == '0001') {
						 $doc->loadXML($alipayNotify->decrypt($_POST['notify_data']));
					 }
					 if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ) {
					     //商户订单号
						 $out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
						 //支付宝交易号
						 $trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
						 //交易状态
						 $trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
					     //获取订单记录
                         $row=$this->CsdjDB->get_row('pay','*',$out_trade_no,'dingdan');
						 if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
						     if($row && $row->pid!=1){
                                  //增加金钱
								  $this->db->query("update ".CS_SqlPrefix."user set rmb=rmb+".$row->rmb." where id=".$row->uid."");
								  //改变状态
								  $this->db->query("update ".CS_SqlPrefix."pay set pid=1 where id=".$row->id."");
								  //发送通知
								  $add['uida']=$row->uid;
								  $add['uidb']=0;
								  $add['name']=L('pay_11');
								  $add['neir']=L('pay_12',array($row->rmb,$out_trade_no));
								  $add['addtime']=time();
            					  $this->CsdjDB->get_insert('msg',$add);
							 }
							 echo "success";
						 }
					 }
				 }else {
                       echo "fail";
				 }

            }elseif(CS_Alipay_JK==1){ //双功能

				 require_once(CSCMSPATH."pay/alipay_trade/alipay.config.php");
				 require_once(CSCMSPATH."pay/alipay_trade/lib/alipay_notify.class.php");
				 //计算得出通知验证结果
				 $alipayNotify = new AlipayNotify($alipay_config);
                 $verify_result = $alipayNotify->verifyNotify();
				 if($verify_result) {//验证成功
					 //商户订单号
					 $out_trade_no = $this->input->post('out_trade_no',true,true);
					 //支付宝交易号
					 $trade_no = $this->input->post('trade_no',true,true);
					 //交易状态
					 $trade_status = $this->input->post('trade_status',true,true);
					 //获取订单记录
                     $row=$this->CsdjDB->get_row('pay','*',$out_trade_no,'dingdan');
    				 if($trade_status == 'WAIT_BUYER_PAY') {  //产生了交易记录，但没有付款
                            echo "success";
    				 }elseif($trade_status == 'WAIT_SELLER_SEND_GOODS') {  //等待发货
						    if($row){
                                  $this->db->query("update ".CS_SqlPrefix."pay set pid=2 where id=".$row->id."");
								  //发送通知
								  $add['uida']=$row->uid;
								  $add['uidb']=0;
								  $add['name']=L('pay_13');
								  $add['neir']=L('pay_14',array($row->rmb,$out_trade_no));
								  $add['addtime']=time();
            					  $this->CsdjDB->get_insert('msg',$add);
							}
                            echo "success";
    				 }elseif($trade_status == 'WAIT_BUYER_CONFIRM_GOODS') {  //已经发货
						    if($row){
                                  $this->db->query("update ".CS_SqlPrefix."pay set pid=3 where id=".$row->id."");
								  //发送通知
								  $add['uida']=$row->uid;
								  $add['uidb']=0;
								  $add['name']=L('pay_15');
								  $add['neir']=L('pay_16',array($out_trade_no));
								  $add['addtime']=time();
            					  $this->CsdjDB->get_insert('msg',$add);
							}
                            echo "success";
    				 }elseif($trade_status == 'TRADE_FINISHED') {  //确定收货
						    if($row && $row->pid!=1){
                                  //增加金钱
								  $this->db->query("update ".CS_SqlPrefix."user set rmb=rmb+".$row->rmb." where id=".$row->uid."");
								  //改变状态
								  $this->db->query("update ".CS_SqlPrefix."pay set pid=1 where id=".$row->id."");
								  //发送通知
								  $add['uida']=$row->uid;
								  $add['uidb']=0;
								  $add['name']=L('pay_11');
								  $add['neir']=L('pay_12',array($row->rmb,$out_trade_no));
								  $add['addtime']=time();
            					  $this->CsdjDB->get_insert('msg',$add);
							}
                            echo "success";
				     }else{
                            echo "success";
    				 }
				 }else {
    				 echo "fail";
				 }

			}elseif(CS_Alipay_JK==2){ //即时到账

				 require_once(CSCMSPATH."pay/alipay_direct/alipay.config.php");
				 require_once(CSCMSPATH."pay/alipay_direct/lib/alipay_notify.class.php");
                 $alipayNotify = new AlipayNotify($alipay_config);
                 $verify_result = $alipayNotify->verifyReturn();
                 if($verify_result) {//验证成功
					 //商户订单号
					 $out_trade_no = $this->input->post('out_trade_no',true,true);
					 //支付宝交易号
					 $trade_no = $this->input->post('trade_no',true,true);
					 //交易状态
					 $trade_status = $this->input->post('trade_status',true,true);
					 //获取订单记录
                     $row=$this->CsdjDB->get_row('pay','*',$out_trade_no,'dingdan');
                     if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
						    if($row && $row->pid!=1){
                                  //增加金钱
								  $this->db->query("update ".CS_SqlPrefix."user set rmb=rmb+".$row->rmb." where id=".$row->uid."");
								  //改变状态
								  $this->db->query("update ".CS_SqlPrefix."pay set pid=1 where id=".$row->id."");
								  //发送通知
								  $add['uida']=$row->uid;
								  $add['uidb']=0;
								  $add['name']=L('pay_11');
								  $add['neir']=L('pay_12',array($row->rmb,$out_trade_no));
								  $add['addtime']=time();
            					  $this->CsdjDB->get_insert('msg',$add);
							}
						    echo "success";
                     }else{
                            echo "success";
					 }
				 }else {
                        echo "fail";
				 }
			}
	}
}
