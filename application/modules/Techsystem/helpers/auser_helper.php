<?php
function adminIsLogged(){
	$CI = &get_instance();
	return $CI->session->has_userdata('userdata');
}
function isAdminServer(){
	$CI = &get_instance();
	return ($CI->session->has_userdata('user_from_sv') && $CI->session->userdata('user_from_sv')==1) || ENVIRONMENT == "development";
}
function adminLogout(){
	$CI = &get_instance();
	$CI->session->unset_userdata("userdata");
	$CI->session->unset_userdata("user_from_sv");
}
function setAdminUser($data){
	$CI = &get_instance();
	$CI->session->set_userdata("userdata",$data);
}
function setAdminServer($data){
	$CI = &get_instance();
	$CI->session->set_userdata("user_from_sv",$data);
}
function getAdminUser(){
	if(adminIsLogged()){
		$CI = &get_instance();
		return $CI->session->userdata("userdata");
	}
	return [];
}
function getAdminUserId(){
	$user = getAdminUser();
	if(array_key_exists("user", $user)){
		return $user["user"]["id"];
	}
	return 0;
}