<?php
//控制器
$lang['plub_01']	= 'The system configuration is not reasonable, the post_max_size value must be greater than upload_max_filesize, otherwise there will be "progress bar 100% stuck", solution：<a style="color:red" target="_blank" href="http://bbs.chshcms.com/show/1.html">Here</a>';
$lang['plub_02']	= 'The system environment is only allowed to upload the%sMB file, you can set the upload_max_filesize value increased upload size, solution：<a style="color:red" target="_blank" href="http://bbs.chshcms.com/show/1.html">Here</a>';
$lang['plub_03']	= 'The system environment requires time to publish content of not more than%sMB (including file), can set the post_max_size value increased release of size, solution：<a style="color:red" target="_blank" href="http://bbs.chshcms.com/show/1.html">Here</a>';
$lang['plub_04']	= 'If the management account background leakage, vulnerable to attack, in order to system security, modify the admin.php file name';
$lang['plub_05']	= 'Remote image cannot be saved to the local. Solution: in the php.ini file allow_url_fopen is set to On';
$lang['plub_06']	= 'PHP does not support CURL expansion, a key login may not be able to log on, unable to access the cloud platform, online upgrade. Solution: in the php.ini; extension=php_curl.dll the semicolon removed';
$lang['plub_07']	= 'PHP does not support OpenSSL, QQ may not use log. Solution: php.ini; extension=php_openssl.dll the semicolon removed';
$lang['plub_08']	= 'The mail server is not set, may be unable to send mail notification system, set mode: System - > configuration - > > open mail sent mail system and modify the configuration';
$lang['plub_09']	= 'Not through (%s) access to the data table structure, modules of the system cannot be used, the solution: open SHOW TABLE STATUS permissions for the Mysql account';
$lang['plub_10']	= 'Not through (%s) access to the data table structure, modules of the system cannot be used, the solution: open SHOW FULL COLUMNS permissions for the Mysql account';

//视图
$lang['tpl_01']	= 'The system of health examination';
$lang['tpl_02']	= 'The basic detection';
$lang['tpl_03']	= 'All testing is complete';
