<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-09-19
 */

/**
 * 默认路由配置（不允许更改）
 */
if(Home_Ym==0){
   $route['([a-zA-Z0-9\_\-]+)/home/'.PLUBPATH.'/([0-9_\/]+)'] = 'home/'.PLUBPATH.'/index/$1/$2';
   $route['([a-zA-Z0-9\_\-]+)/home/'.PLUBPATH.'/(.+)'] = 'home/$2/index/$1';
   $route['([a-zA-Z0-9\_\-]+)/home/'.PLUBPATH.''] = 'home/'.PLUBPATH.'/index/$1';
}else{
   $route['home/'.PLUBPATH.'/([0-9_\/]+)'] = 'home/'.PLUBPATH.'/index/$1';
   $route['home/'.PLUBPATH.'/(.+)'] = 'home/$1/index';
   $route['home/'.PLUBPATH.''] = 'home/'.PLUBPATH.'/index';
}

if (is_file(FCPATH.'plugins/'.PLUBPATH.'/config/rewrite.php')) require FCPATH.'plugins/'.PLUBPATH.'/config/rewrite.php';
/**
 * 自定义路由
 */
 
//$route['自定义路由正则规则']	= '指向的路由URI（URI规则：控制器/方法/参数1的值/参数2的值...）';


