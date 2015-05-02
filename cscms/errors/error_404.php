<!DOCTYPE html>
<html lang="zh">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='viewport' content='width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no'>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo Web_Path;?>packs/images/msg.css"/>
<title>抱歉，您访问的页面不存在 <?php echo Web_Url;?></title>
</head>
<body class="msg_txt">
	<div id="container">
		<h1>Error 404！</h1>
		<div class="box"><span class="code"><b class="f14">错误报告：</b><font><p><?php echo $message; ?></p></font></span></div>
		<div class="box"><span class="code"><b class="f14">错误报告：</b><font><p><p>非常的抱歉，并没有找到您需要的页面。最可能的原因是：</p><p>1、地址栏的地址可能是错误的。</p><p>1、页面内容涉嫌违规，被管理员删除。</p><p>3、您点击链接的不存在或者存在错误，请<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo Admin_QQ;?>&amp;site=qq&amp;menu=yes" title='点击联系'>联系管理员</a>解决。</p></p></font></span></div>
		<div class="box"><span class="code"><b class="f14">解决方案：</b><font><p>您可以点击下面链接返回首页或者继续浏览上一页面：</p><p><a href='<?php echo Web_Path;?>'>返回主页 (<?php echo Web_Url;?>)</a></p><p><a href='javascript:history.back(-1)'>返回上一页</a></p></font></span></div>
	</div>
</body>
</html>

