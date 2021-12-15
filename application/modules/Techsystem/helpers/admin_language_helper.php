<?php
define("ADMIN_LANGUAGE", "admin_language");
function getDefaultAdminLanguage(){
	$CI = &get_instance();
	return $CI->config->item( 'default_admin_language' );
}
function setAdminLanguage($lang){
	$CI = &get_instance();
	$CI->session->set_userdata(ADMIN_LANGUAGE,$lang);
}
function getAdminLanguage(){
	$CI = &get_instance();
	if($CI->session->has_userdata(ADMIN_LANGUAGE)){
		return $CI->session->userdata(ADMIN_LANGUAGE);
	}
	return getDefaultAdminLanguage();	
}
function getAdminAddKey($key,$lang){
	if($lang==getDefaultAdminLanguage()){
		return $key;
	}
	return $key."_".$lang;
}
function alang($key){
	return lang("ADMIN_".$key);
}
function __($key,$arr,$lang=''){
	if($lang==""){
		$lang = getAdminLanguage();
	}
	if(is_object($arr)){
		$arr = (array)$arr;
	}
	$langkey = getAdminAddKey($key,$lang);
	if(array_key_exists($langkey, $arr)){
		return $arr[$langkey];
	}
	if(array_key_exists($key, $arr)){
		return $arr[$key];
	}
}