<?php
function __getIntSize($size){
    return substr($size, 0,strpos($size, "x"));
}
function __getIntMedia($media){
    return preg_replace('/\D/', '', $media);
}
function __checkBrowserWebp(){
    $CI = &get_instance();
    if($CI->session->has_userdata("picture_helper_browser_support")){
        return $CI->session->userdata("picture_helper_browser_support");
    }
    $browsers = $CI->config->item("webp_browsers");
    $CI->load->library('user_agent');
    if ($CI->agent->is_browser())
    {
        $agent = $CI->agent->browser();
    }
    elseif ($CI->agent->is_robot())
    {
        $agent = $CI->agent->robot();
    }
    elseif ($CI->agent->is_mobile())
    {
        $agent = $CI->agent->mobile();
    }
    else
    {
        $agent = 'Unidentified User Agent';
    }
    $version = $CI->agent->version();
    $agent = strtolower($agent);
    $CI->session->set_userdata("picture_helper_browser_support",false);
    foreach ($browsers as $k => $browser) {
        if($browser[0]==$agent){
            if(count($browser)>1){
                if(version_compare($version,$browser[1])){
                    $CI->session->set_userdata("picture_helper_browser_support",true);
                    break;
                }
            }
            else{
                $CI->session->set_userdata("picture_helper_browser_support",true);
                break;
            }
        }
    }
    return $CI->session->userdata("picture_helper_browser_support");
}

function webpImg($item,$key,$echo = true,$size="-1",$class='img-fluid'){

    $sizes = explode("_", $size);
    if(count($sizes)==1) $sizes = [-1,$size];
    if(count($sizes)==0) $sizes = [-1,-1];
    if(!__checkBrowserWebp()){
        $str = '<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="%s" title="%s" alt="%s" class="%s lazyload">';
        $str = sprintf($str,imgv2($item,$key,$sizes[1]),echor($item,'#i#'.$key.'#title',1),echor($item,'#i#'.$key.'#alt',1),$class);
        if($echo){
            echo $str;
        }
        else{
            return $str;
        }
    }
    else{
        $config = getConfigPlugin("picture_helper");
        $groupName = $sizes[0];
        $groupConfig = webpGetConfig($groupName);
       
        $params = [];
        $str =  '<picture>';
            foreach ($groupConfig as $k => $conf) {
                $str .= '<source media="(%s)" data-srcset="%s">';
                array_push($params, $conf['media']);
                array_push($params, imgv2($item,$key,$conf['size'],true));
            }
            $str .= '<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="%s" title="%s" alt="%s" class="%s lazyload">';
            array_push($params, imgv2($item,$key,$sizes[1]));
            array_push($params, echor($item,'#i#'.$key.'#title',1));
            array_push($params, echor($item,'#i#'.$key.'#alt',1));
            array_push($params, $class);
        $str .= '</picture>';
        $str = vsprintf($str, $params);
        if($echo){
            echo $str;
        }
        else{
            return $str;
        }
    }
}
function webpGetConfig($groupName){
    $config = getConfigPlugin("picture_helper");
    $default = [["media"=>"min-width:1200px","size"=>"-1"]];
    $groupConfig = array_key_exists($groupName, $config)?$config[$groupName]["items"]:$default;
    usort($groupConfig,function($a,$b){
        $width1 = __getIntMedia($b["media"]);
        $width2 = __getIntMedia($a["media"]);
        return $width1- $width2;
    });
    return $groupConfig;
}
function webpShowPicture($key,$webp,$sizes,$class="img-fluid"){
    $CI = &get_instance();
    
    if($webp){
        $groupName = $sizes[0];
        $groupConfig = webpGetConfig($groupName);
        
        $params = [];
        $str =  '<picture>';
            foreach ($groupConfig as $k => $conf) {
                $str .= '<source media="(%s)" data-srcset="%s">';
                array_push($params, $conf['media']);
                array_push($params, webpGetSettingImage($key,$webp,$conf['size'],false));
            }
            $str .= '<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="%s" title="%s" alt="%s" class="%s lazyload">';
            array_push($params, webpGetSettingImage($key,false,$sizes[1],false));
            array_push($params, $CI->Dindex->getSettingImage($key."#title",false,$sizes[1],false));
            array_push($params, $CI->Dindex->getSettingImage($key."#alt",false,$sizes[1],false));
            array_push($params, $class);
        $str .= '</picture>';
        $str = vsprintf($str, $params);
        echo $str;
    }
    else{    
        $str = "<img src='%s' alt='%s' title='%s'/>";
        echo sprintf($str,webpGetSettingImage($key,false,$sizes[1],false),$CI->Dindex->getSettingImage($key."#alt",$webp,$sizes[1],false),$CI->Dindex->getSettingImage($key."#title",$webp,$sizes[1],false));
    }
}
function webpGetSettingImage($key,$webp=false,$size='',$isShow = false){
    if($webp && function_exists("__checkBrowserWebp")){
        $webp = __checkBrowserWebp();
    }
    $sizes = explode("_", $size);
    if(count($sizes)==1) $sizes = [-1,$size];
    if(count($sizes)==0) $sizes = [-1,-1];
    $CI = &get_instance();
    $tmp = explode("#", $key);
    $key = $tmp[0];
    $subkey = count($tmp)>1?$tmp[1]:"";
    $tmpValue = $CI->Dindex->getSettings($key);
    $tmpValue = json_decode($tmpValue,true);
    $tmpValue = @$tmpValue?$tmpValue:[];
    if(count($tmpValue)==0) return '';
    if($subkey==""){
        $returnPath =base_url('theme/admin/images/no-image.svg');
        if(array_key_exists('path', $tmpValue) && array_key_exists('file_name', $tmpValue)){
            $path = '';
            if($size[1]=='-1' ||$size[1]==''){
                $path = $tmpValue['path'].$tmpValue['file_name'];
                $path = changeImgWebpExt($path,$webp);
            }
            else{
                $path = $tmpValue['path'].$size[1].'/'.$tmpValue['file_name'];
                $path = changeImgWebpExt($path,$webp);
                $def = $tmpValue['path'].$tmpValue['file_name'];
                $def = changeImgWebpExt($def,$webp);
                if(!file_exists($path)){
                    $path = $def;
                }
            }
            if(file_exists($path)){
                $returnPath= base_url($path);
            }
        }
        if($isShow){
            return webpShowPicture($key,$webp,$sizes);
        }
        return $returnPath;
    }
    else{
        return array_key_exists($subkey, $tmpValue)?$tmpValue[$subkey]:'';
    }
}
function webpLibImageAttribute($json,$key){
    if(is_string($json)){
        $json = json_decode($json,true);
        $json = @$json?$json:[];    
    }
    else if(!is_array($json)){
        $json = [];
    }
    
    return array_key_exists($key, $json)?$json[$key]:"";
}
function webpLibImage($json,$size,$checkWebp=false){
    $webp = false;
    if($checkWebp && function_exists("__checkBrowserWebp")){
        $webp = __checkBrowserWebp();
    }
    $sizes = explode("_", $size);
    if(count($sizes)==1) $sizes = [-1,$size];
    if(count($sizes)==0) $sizes = [-1,-1];
    $config = getConfigPlugin("picture_helper");
    $groupName = $sizes[0];
    $groupConfig = webpGetConfig($groupName);
    if($webp){
        $params = [];
        $str =  '<picture>';
            foreach ($groupConfig as $k => $conf) {
                $str .= '<source media="(%s)" data-srcset="%s">';
                array_push($params, $conf['media']);
                array_push($params, imgSingle($json,$sizes[1],true));
            }
            $str .= '<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="%s" title="%s" alt="%s" class="%s lazyload">';
            array_push($params, imgSingle($json,$sizes[1],true));
            array_push($params, webpLibImageAttribute($json,'title'));
            array_push($params, webpLibImageAttribute($json,'alt'));
            array_push($params, 'img-fluid img-responsive');
        $str .= '</picture>';
        $str = vsprintf($str, $params);
        return $str;
    }
    else{
        $params = [];
        $str = '<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="%s" title="%s" alt="%s" class="%s lazyload">';
        array_push($params, imgSingle($json,$sizes[1],false));
        array_push($params, webpLibImageAttribute($json,'title'));
        array_push($params, webpLibImageAttribute($json,'alt'));
        array_push($params, 'img-fluid img-responsive');
        $str = vsprintf($str, $params);
        return $str;
    }
}