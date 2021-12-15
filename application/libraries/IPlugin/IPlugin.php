<?php
include_once "DBTable.php";
include_once "DBTech5sModule.php";
include_once "DBTech5sTable.php";
include_once "DBHelper.php";
class IPlugin{
	public $hasAdmin = false;
	public $dir = "";
	protected $CI;
	public function __construct(){
		$this->CI = &get_instance();
		$this->dir = $this->getCurrentDir();
	}
	public function install(){
		$resultHook = $this->CI->hooks->call_hook(['plugin_install','current'=>$this]);
		if(is_array($resultHook)){
			extract($resultHook);
		}
		die("Vui lòng khởi tạo hàm cài đặt!");
	}
	public function uninstall(){
		$resultHook = $this->CI->hooks->call_hook(['plugin_uninstall','current'=>$this]);
		if(is_array($resultHook)){
			extract($resultHook);
		}
		die("Vui lòng xử lý hàm hủy!");
	}
	public function viewAdmin(){

	}
	public function addRoutes($controller,$link,$extra=[]){
		$results = $this->checkRoutesExist($link);
		if(count($results)>0){
			$this->removeRoutes($link);
		}
		$this->insertRoutes($controller,$link,$extra);
	}
	protected function checkRoutesExist($link){
		$this->CI->db->where("link",$link);
		$q = $this->CI->db->get("nuy_routes",1,0);
		$results = $q->result_array();
		if(count($results)>0){
			return $results[0];
		}
		return [];
	}
	public function removeRoutes($link){
		$this->CI->db->where("link",$link);
		$this->CI->db->delete("nuy_routes");
	}
	protected function insertRoutes($controller,$link,$extra =[]){
		$data = [];
		$data["controller"] = $controller;
		$data["link"] = $link;
		$data["is_static"] = 1;
		$data = array_merge($data,$extra);
		$this->CI->db->insert("nuy_routes",$data);
	}
	
	public function publishFile($file){
		$dir = $this->dir;
		$orgfile = $dir."/".$file;
		if(file_exists($orgfile)){
			$dest = "theme/frontend/plugins/".basename($dir);
			$dest = $dest."/".$file;
			if(!file_exists(dirname($dest))){
				mkdir(dirname($dest),0777,true);
			}
			copy($orgfile, $dest);
		}
	}
	public function copyFile($file, $nameFolder){
		if(file_exists($file)){
			$pathFileTo = APPPATH."/views/$nameFolder/";
			$nameFileTo = basename($file);
			$fileTo = $pathFileTo.$nameFileTo;
			if(!file_exists($pathFileTo)){
				mkdir($pathFileTo,0777,true);
			}
			copy($file, $fileTo);
		}
	}
	/*
	Delete all static file in theme/frontend
	*/
	public function removeFile(){
		$dir = $this->dir;
		$dest = "theme/frontend/plugins/".basename($dir);
		if(file_exists($dest)){
			$this->recurseRmdir($dest);
		}
	}
	function recurseRmdir($dir) {
		if (!file_exists($dir)) {
			return;
		}
	  	$files = array_diff(scandir($dir), array('.','..'));
	  	foreach ($files as $file) {
	    	(is_dir("$dir/$file")) ? $this->recurseRmdir("$dir/$file") : unlink("$dir/$file");
	  	}
	 	return rmdir($dir);
	}
	protected function getCurrentDir($file=""){
		$class_info = new ReflectionClass($this);
		$dir = dirname($class_info->getFileName());
		$dir = substr($dir, strpos($dir, "application"));
		$dir = str_replace("\\", "/", $dir);
		return $dir.($file!=""?"/".$file:"");
	}
	public function urlFile($file){
		$dir = $this->dir;
		$dest = "theme/frontend/plugins/".basename($dir);
		$dest = $dest."/".$file;
		return $dest;
	}
	protected function getConfigPlugins(){
		return getConfigPlugin(basename($this->dir));
	}
}