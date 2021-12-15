<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

$config['max_lockcount']=3;//Số lần tối đa vi phạm, quá số lần này sẽ khóa vĩnh viễn 
$config['max_connect']=20;//Số kết nối tối đa trên thời gian quy định ở config['time_limit'] 
$config['time_limit']=1;//Thời gian tối đa được phép truy cập nhiều lần 
$config['time_wait']=20;//Thời gian mở lock cho vi pham 
$config['proxy'] = true; 
$config['folder']='firewall/'; 
$config['htaccess']='.htaccess';//Đường dẫn file htaccess 
$config['htaccess_bak']='firewall/htaccess_bak';//Đường dẫn file htaccess nguồn. file sạch để mở khóa ip vĩnh viễn
?>