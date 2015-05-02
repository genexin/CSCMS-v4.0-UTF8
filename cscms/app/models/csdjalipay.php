<?php
/**
 * @Cscms 3.5 open source management system
 * @copyright 2009-2013 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2013-04-27
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CsdjAlipay extends CI_Model {

    var $gateway;
    var $partner;
    var $security_code;
    var $seller_email;
    var $_input_charset;
    var $transport;
    var $notify_url;
    var $return_url;
    var $show_url;
    var $sign_type;
    var $mainname;
    var $antiphishing;
    var $parameter;
    var $mysign;

    // 构造函数 & 配置设置
    function __construct() {
	    parent:: __construct ();
        $this->load->helper('form');
        $this->load->helper('url');

        $this->partner = CS_Alipay_ID; //合作身份者ID
        $this->security_code = CS_Alipay_Key; //安全检验码
        $this->seller_email = CS_Alipay_Name; //签约支付宝账号或卖家支付宝帐户
        $this->_input_charset=utf-8; //字符编码格式 目前支持 GBK 或 utf-8

        //双功能交易
        $this->notify_url2 = site_url('user/pay/notify_page_1'); //交易过程中服务器通知的页面，即异步通知，请实际服务器环境中，要用 http://格式的完整路径，不得含有参数
        $this->return_url2 = site_url('user/pay/return_page_1'); //交易过程中服务器通知的页面，即异步通知，请实际服务器环境中，要用 http://格式的完整路径，不得含有参数

        //及时倒帐
        $this->notify_url = site_url('user/pay/notify_page_2'); //交易过程中服务器通知的页面，即异步通知，请实际服务器环境中，要用 http://格式的完整路径，不得含有参数
        $this->return_url = site_url('user/pay/return_page_2'); //交易过程中服务器通知的页面，即异步通知，请实际服务器环境中，要用 http://格式的完整路径，不得含有参数
        $this->show_url = "http://".$_SERVER['HTTP_HOST'].""; //网站商品的展示地址，不允许加 ?id=123 这类自定义参数

        $this->sign_type = "MD5"; //加密方式 不需修改
        $this->mainname = "GooCarlos"; //收款方名称，如：公司名称、网站名称、收款人姓名等

        $this->transport = "http";
        $this->gateway = "https://www.alipay.com/cooperate/gateway.do?"; // 网关地址

        $this->antiphishing = "0"; //防钓鱼功能开关，'0'表示该功能关闭，'1'表示该功能开启。默认为关闭
        /**
         * 一旦开启，就无法关闭，根据商家自身网站情况请慎重选择是否开启。
         * 申请开通方法：联系我们的客户经理或拨打商户服务电话0571-88158090，帮忙申请开通。
         * 开启防钓鱼功能后，服务器、本机电脑必须支持远程XML解析，请配置好该环境。
         * 若要使用防钓鱼功能，建议使用POST方式请求数据，且请打开class文件夹中alipay_function.php文件，找到该文件最下方的query_timestamp函数.
         */
    }

    /**
     * 生成签名结果
     * @param <数组> $sort_array 要加密的数组
     * @param <数组> $security_code 要加密的数组
     * @param <数组> $sign_type 要加密的数组
     * @return <字符串> $mysgin 签名结果字符串
     */
    function build_mysign($sort_array, $security_code, $sign_type = "MD5") {
        $prestr = $this->create_linkstring($sort_array); //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $prestr . $security_code; //把拼接后的字符串再与安全校验码直接连接起来
        $mysgin = $this->sign($prestr, $sign_type); //把最终的字符串加密，获得签名结果
        return $mysgin;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param <数组> $array 需要拼接的数组
     * @return <字符串> $arg 拼接完成以后的字符串
     */
    function create_linkstring($array) {
        $arg = "";
        while (list ($key, $val) = each($array)) {
            $arg.=$key . "=" . $val . "&";
        }
        $arg = substr($arg, 0, count($arg) - 2); //去掉最后一个&字符
        return $arg;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * 使用场景：GET方式请求时，对URL的中文进行编码
     * @param <数组> $array 需要拼接的数组
     * @return <字符串> $arg 拼接完成以后的字符串
     */
    function create_linkstring_urlencode($array) {
        $arg = "";
        while (list ($key, $val) = each($array)) {
            if ($key != "service" && $key != "_input_charset") {
                $arg.=$key . "=" . urlencode($val) . "&";
            } else {
                $arg.=$key . "=" . $val . "&";
            }
        }
        $arg = substr($arg, 0, count($arg) - 2);       //去掉最后一个&字符
        return $arg;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param <数组> $parameter 加密参数组
     * @return <数组> $para 去掉空值与签名参数后的新加密参数组
     */
    function para_filter($parameter) {
        $para = array();
        while (list ($key, $val) = each($parameter)) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $para[$key] = $parameter[$key];
            }
        }
        return $para;
    }

    /**
     * 对数组排序
     * @param <数组> $array 排序前的数组
     * @return <数组> $array 排序后的数组
     */
    function arg_sort($array) {
        ksort($array);
        reset($array);
        return $array;
    }

    /**
     * 加密字符串
     * @param <字符串> $prestr 需要加密的字符串
     * @param <字符串> $sign_type 加密类型
     * @return <字符串> $sign 加密结果
     */
    function sign($prestr, $sign_type) {
        $sign = '';
        if ($sign_type == 'MD5') {
            $sign = md5($prestr);
        } elseif ($sign_type == 'DSA') {
            //DSA 签名方法待后续开发
            die("DSA 签名方法待后续开发，请先使用MD5签名方式");
        } else {
            die("支付宝暂不支持" . $sign_type . "类型的签名方式");
        }
        return $sign;
    }

    /**
     * 实现多种字符编码方式
     * @param <字符串> $input 需要编码的字符串
     * @param <字符串> $_output_charset 输出的编码格式
     * @param <字符串> $_input_charset 输入的编码格式
     * @return <字符串> $output 编码后的字符串
     */
    function charset_encode($input, $_output_charset, $_input_charset) {
        $output = "";
        if (!isset($_output_charset)) {
            $_output_charset = $_input_charset;
        }
        if ($_input_charset == $_output_charset || $input == null) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
        } elseif (function_exists("iconv")) {
            $output = iconv($_input_charset, $_output_charset, $input);
        } else
            die("sorry, you have no libs support for charset change.");
        return $output;
    }

    /**
     * 实现多种字符解码方式
     * @param <字符串> $input 需要解码的字符串
     * @param <字符串> $_input_charset 输出的解码格式
     * @param <字符串> $_output_charset 输入的解码格式
     * @return <字符串> $output 解码后的字符串
     */
    function charset_decode($input, $_input_charset, $_output_charset) {
        $output = "";
        if (!isset($_input_charset)) {
            $_input_charset = $_input_charset;
        }
        if ($_input_charset == $_output_charset || $input == null) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
        } elseif (function_exists("iconv")) {
            $output = iconv($_input_charset, $_output_charset, $input);
        } else
            die("sorry, you have no libs support for charset changes.");
        return $output;
    }

    /**
     * 用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
     * 注意：由于低版本的PHP配置环境不支持远程XML解析，因此必须服务器、本地电脑中装有高版本的PHP配置环境。建议本地调试时使用PHP开发软件
     * @param <字符串> $partner 合作身份者ID
     * @return <字符串> $encrypt_key 时间戳字符串
     */
    function query_timestamp($partner) {
        $URL = "https://mapi.alipay.com/gateway.do?service=query_timestamp&partner=" . $partner;
        $encrypt_key = "";
        //若要使用防钓鱼，请取消下面的4行注释
        //$doc = new DOMDocument();
        //$doc->load($URL);
        //$itemEncrypt_key = $doc->getElementsByTagName( "encrypt_key" );
        //$encrypt_key = $itemEncrypt_key->item(0)->nodeValue;
        //return $encrypt_key;
    }

    // 付款过程中服务器通知

    /**
     * 对notify_url的认证
     * @return <布尔> 验证结果
     */
    function notify_verify() {
        $config['uri_protocol'] = "PATH_INFO";
        parse_str($_SERVER['QUERY_STRING'], $_POST);

        // 生成签名结果
        if (empty($_POST)) {
            //判断POST来的数组是否为空
            return false;
        } else {
            $post = $this->para_filter($_POST); //对所有POST返回的参数去空
            $sort_post = $this->arg_sort($post); //对所有POST反馈回来的数据排序
            $this->mysign = $this->build_mysign($sort_post, $this->security_code, $this->sign_type); //生成签名结果

            if ($this->mysign == $_POST["sign"]) {
                return true; // 签名符合
            } else {
                return false; // 签名不符
            }
        }
    }

    /**
     * 对return_url的认证
     * @return <布尔> 验证结果
     */
    function return_verify() {
        $config['uri_protocol'] = "PATH_INFO";
        parse_str($_SERVER['QUERY_STRING'], $_GET);

        // 生成签名结果
        if (empty($_GET)) {
            // 判断GET来的数组是否为空
            return false;
        } else {
            $get = $this->para_filter($_GET); //对所有GET反馈回来的数据去空
            $sort_get = $this->arg_sort($get); //对所有GET反馈回来的数据排序
            $this->mysign = $this->build_mysign($sort_get, $this->security_code, $this->sign_type);    //生成签名结果

            if ($this->mysign == $_GET["sign"]) {
                return true; // 签名符合
            } else {
                return false; // 签名不符
            }
        }
    }

    /**
     * GET 请求处理
     */
    function create_url($parameter) {
        // 获取数组
        $this->parameter = $this->para_filter($parameter);
        // 获取编码
        $this->_input_charset = $this->parameter['_input_charset'];
        // 获取签名结果
        $sort_array = $this->arg_sort($this->parameter);
        $this->mysign = $this->build_mysign($sort_array, $this->security_code, $this->sign_type);

        // 生成跳转链接
        $url = $this->gateway;
        $sort_array = array();
        $sort_array = $this->arg_sort($this->parameter);
        $arg = $this->create_linkstring_urlencode($sort_array); //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        //把网关地址、已经拼接好的参数数组字符串、签名结果、签名类型，拼接成最终完整请求url
        $url.= $arg . "&sign=" . $this->mysign . "&sign_type=" . $this->sign_type;
        return $url;
    }

    /**
     * POST 请求处理
     */
    function build_postform($parameter) {
        // 获取数组
        $this->parameter = $this->para_filter($parameter);
        // 获取编码
        $this->_input_charset = $this->parameter['_input_charset'];
        // 获取签名结果
        $sort_array = $this->arg_sort($this->parameter);
        $this->mysign = $this->build_mysign($sort_array, $this->security_code, $this->sign_type);

        // 生成提交表单
        $_extension = array('name' => 'alipay_form');
        $_post_url = $this->gateway . "_input_charset=" . $this->parameter['_input_charset'];
        $payform_html = form_open($_post_url, $_extension);

        // 输出支付表单隐藏项
        while (list ($key, $val) = each($this->parameter)) {
            $payform_html.= form_hidden($key, $val);
        }

        $payform_html.= form_hidden('sign', $this->mysign);
        $payform_html.= form_hidden('sign_type', $this->sign_type);
        $payform_html.= form_close();
        $_button_js = 'onClick=document.forms["alipay_form"].submit();';
        $payform_html.= form_button('submit', 'Go to Alipay Now', $_button_js);

        return $payform_html;
    }

}

/* End of file alipay_model.php */
/* Location: ./application/model/alipay_model.php */

