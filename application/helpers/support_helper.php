<?php 
function goRedirect($type, $message, $url = false, $data = []){
	$CI = &get_instance();
	$CI->session->set_flashdata('typeNotify', $type);
	$CI->session->set_flashdata('messageNotify', $message);
	if(!$url){
		$CI->load->library('user_agent');
		$url = $CI->agent->referrer();
	}
	if($data) $CI->session->set_flashdata('data', $data);
	redirect($url);
}
function dataTableByKey($table,$key='id'){
	$CI = &get_instance();
	$data = $CI->Dindex->getDataDetail(array(
            'table'=>$table,
            'order'=>'ord asc, id desc'
        ));
	$result = [];
	foreach ($data as $k => $item) {
		$result[$item[$key]][]  = $item;
	}
	return $result;
}
function getCmsImageSize($src){
	if(strpos($src, "http")===0){
		if(strpos($src, base_url())===0){
			$src = str_replace(base_url(), '', $src);
		}
	}
	if(strpos($src, "http")===0){
		return [4,3];
	}
	$path = pathinfo ( $src);
	$dirname= $path["dirname"];
	$basename= $path["basename"];
	$extension= $path["extension"];
	$filename= $path["filename"];
	$ext = strtolower($extension);
	if($ext == "webp"){
		$src = $dirname."/".$filename.".jpg";
		if(!file_exists($src)){
			$src = $dirname."/".$filename.".png";
		}
	}
	if(file_exists($src)){
		list($width, $height, $type, $attr) = getimagesize($src);
		return [$width,$height];
	}
	return [4,3];
}
function isHome(){
	return current_url()==base_url();
}
function isPage($dataitem,$page){
	if(!isset($dataitem)) return false;
	return $dataitem["name"]==$page;
}