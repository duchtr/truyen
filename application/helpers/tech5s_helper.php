<?php 
function TECH5S_HEADER(){
	$CI = &get_instance();
	$result = $CI->Dindex->getSettings('CMS_HEADER');

	$resultHook = $CI->hooks->call_hook(['tech5s_helper_header',"result"=>$result]);
    if( !is_bool($resultHook) && is_array($resultHook)){
    	extract($resultHook);
    }
	
	return $result =="CMS_HEADER"?"":$result;
}
function TECH5S_FOOTER(){
	$CI = &get_instance();
	$result =  $CI->Dindex->getSettings('CMS_FOOTER');
	$resultHook = $CI->hooks->call_hook(['tech5s_helper_footer',"result"=>$result]);
    if( !is_bool($resultHook) && is_array($resultHook)){
    	extract($resultHook);
    }
	return $result =="CMS_FOOTER"?"":$result;
}
function getFieldSeoByLang($key,$dataitem,$def){
	if(!@$dataitem) return $def;
	$CI= &get_instance();
	$default_language = $CI->config->item( 'default_language' );
	$lang = $CI->session->has_userdata("lang")?$CI->session->userdata("lang"):$default_language;
	$clang = "_".$lang;
	if(array_key_exists($key.$clang, $dataitem)&&!isNull($dataitem[$key.$clang])){
		return $dataitem[$key.$clang];
	}
	else{
		if($lang == $default_language){
			$clang = "";
		}
		if($clang==""){
			if(array_key_exists($key, $dataitem)&&!isNull($dataitem[$key])){
				return $dataitem[$key];
			}
		}
		$key="name";
		if(array_key_exists($key.$clang, $dataitem)&&!isNull($dataitem[$key.$clang])){
			return $dataitem["name".$clang];
		}
		return $def;
	}
}
function CMS_TITLE($dataitem,$masteritem,$datatable){
	$CI = &get_instance();
	$ret=  "<base href='".base_url()."'/>";
	$resultHook = $CI->hooks->call_hook(['tech5s_before_cms_title',"ret"=>$ret,"dataitem"=>$dataitem,"masteritem"=>$masteritem,"datatable"=>$datatable]);
    if(!is_bool($resultHook) && is_array($resultHook)){
    	extract($resultHook);
    }
	$ret.=  tech5sGetNofollow();
	$tmp = $CI->Dindex->getSettings('TITLE_SEO');
	if(current_url()==base_url())
		$dataitem=Null;
	$titleSEO = getFieldSeoByLang("s_title",$dataitem,$tmp);
	$tmp = $CI->Dindex->getSettings('DES_SEO');
	$desSEO = getFieldSeoByLang("s_des",$dataitem,$tmp);
	$tmp = $CI->Dindex->getSettings('KEY_SEO');
	$keySEO = getFieldSeoByLang("s_key",$dataitem,$tmp);


	$ret .= sprintf('<title>%s</title>',addslashes($titleSEO));

    $ret .= sprintf('<meta name="description" content="%s">',addslashes($desSEO));
    $ret .= '<meta name="keywords" content="'.addslashes($keySEO).'">';
    $ret .= sprintf('<meta name="keywords" content="%s">',addslashes($keySEO));
    $siteName = $CI->Dindex->getSettings('SITE_NAME');
    $siteName = (isNull($siteName)?$titleSEO:$siteName);
	$ret .= sprintf('<meta property="og:site_name" content="%s">',$siteName);
	$ret .= sprintf('<meta property="og:url" content="%s">',current_url());
	$ret .= sprintf('<meta name="twitter:url" content="%s">',current_url());
	$ret .= '<meta property="og:type" content="article">';
	$ret .= sprintf('<meta property="og:title" content="%s">',addslashes($titleSEO));
	$ret .= sprintf('<meta name="twitter:title" content="%s">',addslashes($titleSEO));

	$ret .= sprintf('<meta property="og:description" content="%s">',addslashes($desSEO));
	$ret .= sprintf('<meta name="twitter:description" content="%s">',addslashes($desSEO));

	if(base_url()==current_url()){
		$img = $CI->Dindex->getSettings('FBSHARE');
		$img = json_decode($img,true);


		if(@$img){
			$img = $img["path"].$img["file_name"];
		}
		else{
			$logo = json_decode($CI->Dindex->getSettings("LOGO"),true);
			$img = @$logo ? $logo["path"].$logo["file_name"]:"";
		}
		$pos = strpos($img , 'http');
		if($pos === FALSE) $img = base_url().$img;
	}
	else{
		$img = (@$dataitem && @$dataitem['img'])?$dataitem['img']:"";
		if(isNull($img)){
			$tmp = (@$dataitem && @$dataitem['content'])?$dataitem['content']:"";
			$img = $CI->Dindex->getSettings('FBSHARE');
			$img = json_decode($img,true);


			if(@$img){
				$img = $img["path"].$img["file_name"];
			}
			else{
				$img = "";
			}
			$img = getImageFromContent($tmp,$img);
			if(isNull($img) || $img =='FBSHARE'){
				$logo = json_decode($CI->Dindex->getSettings("LOGO"),true);
				$img = @$logo ? $logo["path"].$logo["file_name"]:"";
			}
		}else{
			$img = json_decode($img,true);
			$img =@$img? $img["path"].$img["file_name"]:"";
		}
		$pos = strpos($img , 'http');
		if($pos === FALSE) $img = base_url().$img;
	}
	$ret .= sprintf('<meta property="og:image" content="%s">',$img);
	$ret .= sprintf('<meta name="twitter:image" content="%s">',$img);
	$ret .= sprintf('<meta property="og:image:alt" content="%s">',addslashes($titleSEO));
	$ret .= '<meta property="og:locale" content="vi_vn">';
	$wmt = $CI->Dindex->getSettings('WMT');
	if(!isNull($wmt)){
		$ret .=sprintf('<meta name="google-site-verification" content="%s" />',$wmt);
	}
	$fbappid = $CI->Dindex->getSettings('FBAPPID');
	if('FBAPPID'!=$fbappid){
		$ret .= sprintf('<meta property="fb:app_id" content="%s">',$fbappid);
	}
	$default_language = $CI->config->item( 'default_language' );
	$lang = $CI->session->has_userdata("lang")?$CI->session->userdata("lang"):$default_language;
	$ret .= sprintf('<meta name="lang" content="%s">',$lang);
	$ret .=sprintf('<link rel="canonical" href="%s">',current_url());
	$fav =json_decode($CI->Dindex->getSettings('FAVICON'),true);
	$fav = @$fav ?$fav["path"].$fav["file_name"]:"";
	if($fav !="" && file_exists($fav)){
		$fav = base_url($fav);
		$ret .= sprintf('<link rel="shortcut icon" href="%s">',$fav);
	}
	$themeColor = $CI->Dindex->getSettings('THEME_COLOR');
	if(!isNull($themeColor)){
		$ret .=sprintf('<meta name="theme-color" content="%s">',$themeColor);
	}
	$manifest  = 'manifest.json';
	if(file_exists($manifest)){
		$ret .= sprintf('<link rel="manifest" href="%s">',base_url('manifest.json'));
	}
	/*Apple*/
	$ret .='<meta name="apple-mobile-web-app-capable" content="yes">';
	$ret .='<meta name="apple-mobile-web-app-status-bar-style" content="black">';
	$ret .=sprintf('<meta name="apple-mobile-web-app-title" content="%s">',$siteName);
	$ret .=tech5sGetFavicion(72);
	$ret .=tech5sGetFavicion(96);
	$ret .=tech5sGetFavicion(128);
	$ret .=tech5sGetFavicion(144);
	$ret .=tech5sGetFavicion(152);
	$ret .=tech5sGetFavicion(192);
	$ret .=tech5sGetFavicion(384);
	$ret .=tech5sGetFavicion(512);

	$ret .=tech5sAppleIcon(152);


	$resultHook = $CI->hooks->call_hook(['tech5s_load_meta',"ret"=>$ret,"dataitem"=>$dataitem,"masteritem"=>$masteritem,"datatable"=>$datatable]);
    if(is_string($resultHook)){
    	$ret.=$resultHook;
    }
    else if(is_array($resultHook)){
    	extract($resultHook);
    }
	return $ret;
}
function tech5sGetNofollow(){
	$CI = &get_instance();
	$seg1 = $CI->uri->segment(1);
	$seg2 = $CI->uri->segment(2);
	if(isset($seg1) && $seg1 != 'tag' && isset($seg2) && $seg2 != ''){
		$seg = $seg2;
	}
   	$idx = ((isset($dataitem) && isset($dataitem["noindex"]) && $dataitem["noindex"]==0) || (!isset($seg) && isset($dataitem) && !isset($dataitem["noindex"])) || !isset($dataitem))?"index":"noindex";
   	$follow = ((isset($dataitem) && isset($dataitem["nofollow"]) && $dataitem["nofollow"]==0) || (!isset($seg) && isset($dataitem) && !isset($dataitem["nofollow"])) || !isset($dataitem))?"follow":"nofollow";
   	$idx = $idx.",".$follow;
   	return sprintf('<meta name="robots" content="%s" />',$idx);
}
function tech5sAppleIcon($size,$onlyLink = false){
	if($onlyLink){
		return tech5sGetFavicion(152,true);
	}
	$fav = tech5sGetFavicion(152,true);
	if($fav!=""){
		return sprintf('<link rel="apple-touch-icon" href="%s">',$fav);
	}
	return "";
}
function tech5sGetFavicion($size,$onlyLink = false){
	$CI = &get_instance();
	$fav =json_decode($CI->Dindex->getSettings('FAVICON'.$size),true);
	$fav = @$fav ?$fav["path"].$fav["file_name"]:"";
	if($fav == ""){
		$fav =json_decode($CI->Dindex->getSettings('FAVICON'),true);
		$fav = @$fav ?$fav["path"].$fav["file_name"]:"";
	}
	if($fav != "" && file_exists($fav)){
		$fav = base_url($fav);
		if($onlyLink){
			return $fav;
		}
		return sprintf('<link rel="icon" type="image/png" sizes="%sx%s" href="%s">',$size,$size,$fav);	
	}
	return "";
	
}
function getImageFromContent($html,$def){
    preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $html, $matches);
    $val = $def;
	foreach ($matches[1] as $key=>$value) {
	    $val = $value;
	    break;
	}
	$pos = strpos($val , 'http');
	if($pos === FALSE) $val = base_url().$val;
	return $val;
}
function concatenateFiles($files)
{
    $buffer = '';

    foreach($files as $file) {
        $buffer .= file_get_contents($file);
    }

    return $buffer;
}

function loadCss($files,$minfile = 'styles.min.css'){
	$_minfile ="theme/frontend/".$minfile;
	$createMinFile = false;
	if(!file_exists($_minfile)){
		$createMinFile = true;
	}
	if(!$createMinFile){
		$btime = filemtime($_minfile);
		foreach ($files as $key => $value) {
			$time = filemtime('theme/frontend/'.$value);
			if($time>$btime){
				unlink($_minfile);
				$createMinFile = true;
				break;
			}
		}
	}
	
	if($createMinFile){
		$CI= &get_instance();
		$CI->load->library('minify'); 
    	$CI->minify->css($files);
    	$CI->minify->deploy_css(TRUE,$minfile);
	}
		
	$str ="";
	$str .= "<style>";
	$str .= concatenateFiles(array($_minfile));
	$str.= "</style>";
	echo $str;
}
function loadJs($files, $minfile = 'scripts.min.js'){
	$_minfile ="theme/frontend/".$minfile;
	$createMinFile = false;
	if(!file_exists($_minfile)){
		$createMinFile = true;
	}
	if(!$createMinFile){
		$btime = filemtime($_minfile);
		foreach ($files as $key => $value) {
			$time = filemtime('theme/frontend/'.$value);
			if($time>$btime){
				unlink($_minfile);
				$createMinFile = true;
				break;
			}
		}
		
	}
	
	if($createMinFile){
		$CI= &get_instance();
		$CI->load->library('minify'); 
		$CI->minify->js($files); 
		$CI->minify->deploy_js(TRUE,$minfile); 
	}
	echo '<script type="text/javascript" defer src="theme/frontend/'.$minfile.'"></script>';
}




 ?>