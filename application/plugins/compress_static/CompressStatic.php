<?php
class CompressStatic extends IPlugin{
	public $hasAdmin = true;
	
	protected $config = [];
	protected $compressCss = 0;
	protected $compressJs = 0;
	protected $cssInline = 0;
	public function __construct(){
		parent::__construct();
		
		$this->config= $this->getConfigPlugins();
		$this->compressCss= isset($this->config["css_enable"])?$this->config["css_enable"]:0;
		$this->compressJs= isset($this->config["js_enable"])?$this->config["js_enable"]:0;
		$this->cssInline= isset($this->config["css_inline"])?$this->config["css_inline"]:0;
	}
	
	public function install(){

	}
	public function uninstall(){

	}
	private function endsWith($string, $endString) 
	{ 
	    $len = strlen($endString); 
	    if ($len == 0) { 
	        return true; 
	    } 
	    return (substr($string, -$len) === $endString); 
	} 
	private function startsWith ($string, $startString) 
	{ 
	    $len = strlen($startString); 
	    return (substr($string, 0, $len) === $startString); 
	} 
	private function removeComment($content){
		$regex = array(
		"`^([\t\s]+)`ism"=>'',
		"`^\/\*(.+?)\*\/`ism"=>"",
		"`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1",
		"`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n",
		"`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n"
		);
		$content = preg_replace(array_keys($regex),$regex,$content);
		return $content;
	}
	private function removeSpecialLink($url){
		$url = preg_replace('/\?.*/', '', $url);
		$url = str_replace("'", "", $url);
		$url = str_replace("\"", "", $url);
		return $url;
	}
	//Load Css via plugin with hook tech5s_load_meta will merge
	//Load css via tech5s_before_header will not merge
	private function compressCss($output){
		preg_match_all('/(<link\s(?:[^>]*rel="stylesheet")[^>]* href="(.+?)">)\R?/is', $output, $links);
		$output = preg_replace('/(<link\s(?:[^>]*rel="stylesheet")[^>]* href="(.+?)">)\R?/is', '', $output);
		$content = "";
		$linkCss = [];
		if(count($links)>2){
			$linkCss = $links[2];
		}
		$md5name = md5(implode("-", $linkCss)).".min.css";
		$finalfile ="theme/frontend/".$md5name;
		$createMinFile = false;
		if(!file_exists($finalfile)){
			$createMinFile = true;
		}
		if(!$createMinFile){
			$btime = filemtime($finalfile);
			foreach ($linkCss as $key => $link) {
				$time = filemtime($link);
				if($time>$btime){
					unlink($finalfile);
					$createMinFile = true;
					break;
				}
			}
		}
		if($createMinFile){
			$content = "";
			foreach ($linkCss as $key => $link) {
				$tmp = file_get_contents($link);
				$tmp = $this->removeComment($tmp);
				preg_match_all('/(url\((.+?)\))/', $tmp, $urls);
				$currentFileDir = dirname($link);
				if(count($urls)>2){
					$urls = $urls[2];
					foreach ($urls as $ku => $vu) {
						$vu = $this->removeSpecialLink($vu);
						if(strpos($vu, "data:")===FALSE){
							$path = realpath($currentFileDir."/".$vu);
							$newpath = str_replace(FCPATH, "", $path);
							$newpath = base_url($newpath);
							$newpath = str_replace("\\", "/", $newpath);
							$tmp = str_replace($vu, $newpath, $tmp);
						}
					}
				}
				$content .=$tmp;
			}
			$_minfile ="theme/frontend/tmp.min.css";
			file_put_contents($_minfile, $content);
			$CI= &get_instance();
			$CI->load->library('minify'); 
	    	$CI->minify->css(['tmp.min.css']);
	    	$CI->minify->deploy_css(TRUE,$md5name);
	    	unlink($_minfile);
		}
		$injectCss = "";

    	if($this->cssInline){
    		$injectCss = "<style>".file_get_contents($finalfile)."</style>";
    	}
    	else{
    		$injectCss = '<link rel="stylesheet" href="'.$finalfile.'">';
    	}

    	$output = preg_replace("/<\/head>/",$injectCss."$0", $output);
		return $output;
	}
	private function compressJs($content){
		preg_match_all('/<script(.*?)?src=(\'|")(.*?)(\'|")(.*?)?>(.*?)?<\/script>?/im', $content, $outs);
		$content = preg_replace('/<script(.*?)?src=(\'|")(.*?)(\'|")(.*?)?>(.*?)?<\/script>?/im', '', $content);
		if(count($outs)>4){
			$files = $outs[3];
			$minfile = md5(implode("-", $files)).".min.js";
			$_minfile ="theme/frontend/".$minfile;
			$createMinFile = false;
			if(!file_exists($_minfile)){
				$createMinFile = true;
			}
			if(!$createMinFile){
				$btime = filemtime($_minfile);
				foreach ($files as $key => $value) {
					$time = filemtime($value);
					if($time>$btime){
						unlink($_minfile);
						$createMinFile = true;
						break;
					}
				}
			}
			if($createMinFile){
				$finalFiles = [];
				foreach ($files as $kf => $file) {
					array_push($finalFiles, str_replace("theme/frontend/", "", $file));
				}
				$CI= &get_instance();
				$CI->load->library('minify'); 
				$CI->minify->js($finalFiles); 
				$CI->minify->deploy_js(TRUE,$minfile); 
			}
			$script =  '<script type="text/javascript" defer src="'.$_minfile.'"></script>';
			$content = preg_replace("/<\/body>/", $script."$0", $content);
		}
		return $content;
		
	}
	public function compress($args){
		$output = $args["output"];
		$view = $args["view"];
		if($view!="index") return true;
		if($this->compressCss){
			$output = $this->compressCss($output);
		}
		if($this->compressJs){
			$output = $this->compressJs($output);
		}
		return ["output"=>$output,"view"=>$view];
	}
}