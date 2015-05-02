<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$active_group = 'default';
$active_record = TRUE;
$db['default']['hostname'] = CS_Sqlserver;
$db['default']['username'] = CS_Sqluid;
$db['default']['password'] = CS_Sqlpwd;
$db['default']['database'] = CS_Sqlname;
$db['default']['dbdriver'] = CS_Dbdriver;
$db['default']['dbprefix'] = CS_SqlPrefix;
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = (!defined('IS_ADMIN'))?CS_Cache_On:FALSE;
$db['default']['cachedir'] = "cache/".CS_Cache_Dir."/";
$db['default']['char_set'] = CS_Sqlcharset;
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = FALSE;
$db['default']['stricton'] = FALSE;

