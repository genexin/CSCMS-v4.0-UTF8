<!DOCTYPE html>
<html lang="zh">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='viewport' content='width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no'>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo Web_Path;?>packs/images/msg.css"/>
<title>数据库错误 <?php echo Web_Url;?></title>
</head>
<body class="msg_txt">
	<div id="container">
		<h1>Database Error！</h1>
		<div class="box"><span class="code"><b class="f14"><?php echo $heading; ?></b></span></div>
		<div class="box"><span class="code"><b class="f14">错误报告：</b><font><p><?php echo str_replace(FCPATH,Web_Path,$message); ?></p></font></span></div>
		<div class="box"><span class="code"><b class="f14">解决方案：</b><font><p>您可以点击下面链接返回首页或者继续浏览上一页面：</p><p><a href='<?php echo Web_Path;?>'>返回主页 (<?php echo Web_Url;?>)</a></p><p><a href='javascript:history.back(-1)'>返回上一页</a></p></font></span></div>
	</div>
</body>
</html>

