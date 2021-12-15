<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Iteration count
|--------------------------------------------------------------------------
|
| How many iterations of hashing should occur?
|
| Default: 8
|
*/
$config['path_uploads'] = 'uploads/';
$config['max_size']='200000';
$config['max_width']  = '40000';
$config['max_height']  = '40000';
$config['base_path'] = '';//base path after domain
$config['ext_img'] = array( 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg' );
$config['ext_file'] =  array( 'doc', 'docx', 'rtf', 'pdf', 'xls', 'xlsx', 'txt', 'csv', 'html', 'xhtml', 'psd', 'sql', 'log', 'fla', 'xml', 'ade', 'adp', 'mdb', 'accdb', 'ppt', 'pptx', 'odt', 'ots', 'ott', 'odb', 'odg', 'otp', 'otg', 'odf', 'ods', 'odp', 'css', 'ai' );
$config['ext_video'] =  array( 'mov', 'mpeg', 'm4v', 'mp4', 'avi', 'mpg', 'wma', "flv", "webm" );
$config['ext_music'] = array( 'mp3', 'm4a', 'ac3', 'aiff', 'mid', 'ogg', 'wav' );
$config['ext_misc'] = array( 'zip', 'rar', 'gz', 'tar', 'iso', 'dmg' );
$config["webp"] =true;


