<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed'); 
class SuperCache { 
	protected $CI;
	protected $except=[];
	protected $timeRefresh=600;
	public function __construct(){
		$this->CI = &get_instance();
	}
	private function getNameCache($slug){
		$name = md5($slug);
		$path = FCPATH."application/cache/super/";
		if(!file_exists($path)){
			mkdir($path,0777,true);
		}
		$get = $this->CI->input->get();
		$add = "";
		if(count($get)>0){
			$add = md5(json_encode($get));
		}
		return $path.$name.$add;
	}
	private function isDebug(){
		if(defined('ENVIRONMENT') )
		{
			return ENVIRONMENT == 'development';
		}
		return false;
	}
	public function getDataRequest($slug,$callback){
		if(in_array($slug, $this->except)){
			$obj = $callback();
			if($obj instanceof \Illuminate\View\View){
				return $obj->render();	
			}
			return $obj;
		}
		$cacheFile =$this-> getNameCache($slug);
		if(!$this->isDebug()){
			if ( (file_exists($cacheFile)) && (time() <= (fileatime($cacheFile) + $this->timeRefresh*1000) ))
			{ 
			    $content = file_get_contents($cacheFile);
			   $content = $this->updateCsrf($content);
			    return $content; 
			} 
			else
			{ 
			    ob_start(); 
			    $obj = $callback();
			    echo $obj;
			    $content = ob_get_contents();
			    ob_end_clean(); 
			    file_put_contents($cacheFile,$content); 
			    return $content; 
			} 
		}
		else{
			$obj = $callback();
			if($obj instanceof \Illuminate\View\View){
				return $obj->render();	
			}
			return $obj;
		}
	}
	private function updateCsrf($content){
		$token = $this->CI->security->get_csrf_hash();
	    $token_name= $this->CI->security->get_csrf_token_name();
		return preg_replace('/<(input|INPUT)[^>]*(name|NAME)="(_token|_TOKEN)"[^>]*>/',
	                           '<input type="hidden" name="'.$token_name.'" value="' . $token . '">', 
	                           $content);
	}
}