<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

$config['profiler_enable']=false;

/*Bật Fire Wall hay không*/
$config['firewall_enable']=true;
/*Bật Check Setting hay không*/
$config['check_setting_enable']=false;
/*Bật Update online hay không*/
$config['update_online_enable']=false;


/*Cấu hình cho đa ngôn ngữ*/
$config['multi_language']=true;
$config['languages']=["vi","en","cn","jp"];
$config['default_language']="vi";


$config['default_admin_language']="vi";

/*Plugins*/
$config['webp_browsers']=[["chrome","10"]];
