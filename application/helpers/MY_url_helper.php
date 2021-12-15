<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function base_url($uri = '', $protocol = NULL)
{
	$CI = &get_instance();
	$resultHook = $CI->hooks->call_hook(['tech5s_base_url','uri'=>$uri,'protocol'=>$protocol]);
    if(is_array($resultHook)){
        extract($resultHook);
    }
	return get_instance()->config->base_url($uri, $protocol);

}

function current_url()
{
    $CI =&get_instance();
    $uriString = $CI->uri->uri_string();
    $resultHook = $CI->hooks->call_hook(['tech5s_current_url','uriString'=>$uriString]);
    if(is_array($resultHook)){
        extract($resultHook);
    }
    $url = $CI->config->site_url($uriString);;
    return $_SERVER['QUERY_STRING'] ? $url.'?'.$_SERVER['QUERY_STRING'] : $url;
}
