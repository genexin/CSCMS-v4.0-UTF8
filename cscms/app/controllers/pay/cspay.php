<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-04-10
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cspay extends Cscms_Controller {

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
	  		$pay_url		 = 'http://pay.chshcms.com/pay';		        /* CSPAY支付请求地址 */
	  		$pay_sid		 = '1';			/* 交易方式，业务代码，1为支付宝，2为财付通，3为网银 */
	  		$pay_mode		 = '';
      		$body			 = L('pay_03',array($_SESSION['cscms__name']));    /* 商品名 */	
	  		$out_trade_no	 = $row->dingdan;                        /* 商户订单号*/       		 
      		$total_fee	     = $row->rmb;			                /* 总金额 */
	  		$partner		 = CS_Cspay_ID;        /* 商户号 */	
	  		$key			 = CS_Cspay_Key;			/* 商户加密key */      
      		$return_url	     = site_url('pay/tenpay/return_url');			/* 同步返回地址 */
	  		$notify_url	     = site_url('pay/tenpay/notify_url');			/* 异步返回地址 */
      		$remark          = '';                                /* 自定义字段 */

      		/* 数字签名 */
      		$sign_text = "pay_sid=" . $pay_sid . "&out_trade_no=" . $out_trade_no . "&total_fee=" . intval($total_fee) .
                   "&partner=" . $partner . "&key=" . $key .  "&return_url=" . $return_url . "&notify_url=" . $notify_url;

      		$sign = strtoupper(md5($sign_text));
      		/* 交易参数 */
      		$parameter = array(
            'charset'                   => 'utf-8',  
            'pay_sid'                   => $pay_sid,            
            'body'                      => $body,                      
            'out_trade_no'              => $out_trade_no,   
            'total_fee'                 => $total_fee,              
            'partner'                   => $partner,		         
            'return_url'                => $return_url,		
            'notify_url'                => $notify_url,                  
            'remark'                    => $remark,                  
            'sign'                      => $sign                         
      		);
      		$button  = '<form name="CsPayForm" method="post" style="text-align:left;" action="' . $pay_url . '" style="margin:0px;padding:0px" >';
      		foreach ($parameter AS $key=>$val)
      		{
          		$button  .= "<input type='hidden' name='$key' value='$val'/>";
      		}
	  		$formstr = $button . '</form><script>document.CsPayForm.submit();</script>';
	  		echo $formstr;
	}

	//同步返回
	public function return_url()
	{
            $this->CsdjUser->User_Login();
	        /*取返回参数*/
            $cspay_id        = intval($this->input->get('cspay_id',TRUE));        //CSPAY交易号
            $cspay_pid       = intval($this->input->get('cspay_pid',TRUE));       //CSPAY交易结果
            $pay_sid     	 = intval($this->input->get('pay_sid',TRUE));         //交易方式，业务代码
            $out_trade_no    = $this->input->get('out_trade_no',TRUE,TRUE);    //定单号
            $total_fee   	 = $this->input->get('total_fee',TRUE,TRUE);       //金额
            $remark	 	 	 = $this->input->get('remark',TRUE,TRUE);          //自定义字段
            $sign            = $this->input->get('sign',TRUE,TRUE);            //加密数字签名
	        $partner		 = CS_Cspay_ID;                    	
	        $key			 = CS_Cspay_Key;			    
            $return_url		 = site_url('pay/tenpay/return_url');			
	        $notify_url		 = site_url('pay/tenpay/notify_url');			
		
            /* 检查数字签名是否正确 */
            $sign_text = "cspay_id=". $cspay_id ."&cspay_pid=". $cspay_pid ."&pay_sid=" . $pay_sid . "&out_trade_no=" . $out_trade_no . "&total_fee=" . $total_fee .
                     "&partner=" . $partner . "&key=" . $key .  "&return_url=" . $return_url . "&notify_url=" . $notify_url;
            $sign_md5 = strtoupper(md5($sign_text));
            //支付状态验证
            if ($sign_md5 == $sign && $cspay_pid==1){  //验证支付成功
                 msg_url(L('pay_07').$out_trade_no,spacelink('pay'));
			} else {  //验证支付失败
                 msg_url(L('pay_09'),spacelink('pay'));
			}
	}

	//异步返回
	public function notify_url()
	{
	        /*取返回参数*/
            $cspay_id        = intval($this->input->post('cspay_id',TRUE));        //CSPAY交易号
            $cspay_pid       = intval($this->input->post('cspay_pid',TRUE));       //CSPAY交易结果
            $pay_sid     	 = intval($this->input->post('pay_sid',TRUE));         //交易方式，业务代码
            $out_trade_no    = $this->input->post('out_trade_no',TRUE,TRUE);    //定单号
            $total_fee   	 = $this->input->post('total_fee',TRUE,TRUE);       //金额
            $remark	 	 	 = $this->input->post('remark',TRUE,TRUE);          //自定义字段
            $sign            = $this->input->post('sign',TRUE,TRUE);            //加密数字签名
	        $partner		 = CS_Cspay_ID;                    	
	        $key			 = CS_Cspay_Key;			    
            $return_url		 = site_url('pay/tenpay/return_url');			
	        $notify_url		 = site_url('pay/tenpay/notify_url');			
		
            /* 检查数字签名是否正确 */
            $sign_text = "cspay_id=". $cspay_id ."&cspay_pid=". $cspay_pid ."&pay_sid=" . $pay_sid . "&out_trade_no=" . $out_trade_no . "&total_fee=" . $total_fee .
                     "&partner=" . $partner . "&key=" . $key .  "&return_url=" . $return_url . "&notify_url=" . $notify_url;
            $sign_md5 = strtoupper(md5($sign_text));
            if ($getcspay != 'ok') {  //支付状态检测
				echo "NO";
			}else{
				if ($sign_md5 == $sign && $cspay_pid==1){
				   $row=$this->CsdjDB->get_row('pay','*',$out_trade_no,'dingdan');
				   if($row && $row->pid!=1){
                        //增加金钱
						$this->db->query("update ".CS_SqlPrefix."user set rmb=rmb+".$row->rmb." where id=".$row->uid."");
						//改变状态
						$this->db->query("update ".CS_SqlPrefix."pay set pid=1 where id=".$row->id."");
						//发送通知
						$add['uida']=$row->uid;
						$add['uidb']=0;
						$add['name']=L('pay_11');
						$add['neir']=L('pay_17',array($row->rmb,$out_trade_no));
						$add['addtime']=time();
            			$this->CsdjDB->get_insert('msg',$add);
				   }
				   echo "OK";
				}else{
				   echo "NO";
				}
			}
	}

    //远程查询交易结果
	public function get_cspayurl($cspay_id){
        if(empty($cspay_id)) return '';
        $url = "http://pay.chshcms.com/pay/notify_query?key=".CS_Cspay_Key."&cspay_id=".$cspay_id;
        $FileContent=@file_get_contents($url);
        if(empty($FileContent)){
           $ch = curl_init($url);
           curl_setopt($ch, CURLOPT_TIMEOUT, 1);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           $FileContent = curl_exec($ch);
           curl_close ($ch);
        }
        return $FileContent;
	}
}
