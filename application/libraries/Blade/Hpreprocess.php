<?php 
class HPreProcess{
	private $CI;
	private static $token_name = 'csrf_tech5s_name';

  	private static $token;
	public function __construct()
	{
		$this->CI =& get_instance();
		
		 $this->CI->load->helper(array('tech5s'));
	}
	private function addAnalytic($output){
		if(strpos(strtolower(uri_string()), "techsystem") !==FALSE){
			return $output;
		}
		else{
			$analytic = $this->CI->Dindex->getSettings('ANALYTICS');
			$ana ="";
			if($analytic!=""){
				$ana .= "<script>";
				$ana .="(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){";
				$ana .="(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),";
				$ana .="m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)";
				$ana .="})(window,document,'script','//www.google-analytics.com/analytics.js','ga');";

				$ana .="ga('create', '".$analytic."', 'auto');";
				$ana .="ga('require', 'displayfeatures');";
				$ana .="ga('send', 'pageview');";

				$ana .="</script>";
			}
			$addHeader = "";
			$resultHook = $this->CI->hooks->call_hook(['tech5s_before_header']);
            if(is_string($resultHook)){
            	$addHeader .=$resultHook;
            }
			$output = preg_replace("/<\/head>/",TECH5S_HEADER().$ana.$addHeader."$0", $output);
			$addFooter = "";
			$resultHook = $this->CI->hooks->call_hook(['tech5s_before_footer']);
            if(is_string($resultHook)){
            	$addFooter .=$resultHook;
            }
			
			$output = preg_replace("/<\/body>/", TECH5S_FOOTER().$addFooter."$0", $output);
			return $output;
		}
	}
	public function compressHtml($output){
		$excepts = ["techsystem"];
		$resultHook = $this->CI->hooks->call_hook(['tech5s_before_compress_html',"excepts"=>$excepts]);
		
        if(is_array($resultHook)){
        	extract($resultHook);
        }
        $uri = strtolower(uri_string());
        foreach ($excepts as $k => $except) {
        	if(strpos($uri, $except) !==FALSE){
				return $output;
			}
        }
		$search = array(
		    '/\>[^\S ]+/s', 
		    '/[^\S ]+\</s', 
		     '/(\s)+/s', 
		  '#(?://)?<!\[CDATA\[(.*?)(?://)?\]\]>#s' 
		  );
		 $replace = array(
		     '>',
		     '<',
		     '\\1',
		  "//&lt;![CDATA[\n".'\1'."\n//]]>"
		  );
		 
		 $output = preg_replace($search, $replace, $output);
		return $output;
	}
	public function inject_tokens($output)
	{
	    $output = preg_replace('/(<(form|FORM)[^>]*(method|METHOD)="(post|POST)"[^>]*>)/',
	                           '$0<input type="hidden" name="' . self::$token_name . '" value="' . self::$token . '">', 
	                           $output);
	    $output = preg_replace('/(<\/head>)/',
	                           '<meta name="csrf-name" content="' . self::$token_name . '">' . "\n" . '<meta name="csrf-token" content="' . self::$token . '">' . "\n" . '$0', 
	                           $output);
	   	return $output;
	}
	public function generate_token()
	  {
	    self::$token = $this->CI->security->get_csrf_hash();
	    self::$token_name= $this->CI->security->get_csrf_token_name();
	    $this->CI->load->library(array('session'));
	    $this->CI->session->set_userdata(self::$token_name,self::$token);
	  }
	public function replaceHtml($output,$compress){
		$output = $this->addAnalytic($output);
		$output = $this->inject_tokens($output);
		if($compress){
			$output = $this->compressHtml($output);
		}

		$resultHook = $this->CI->hooks->call_hook(['tech5s_before_display_html',"output"=>$output]);
        if(!is_bool($resultHook) && is_array($resultHook)){
        	extract($resultHook);
        }
		return $output;
	}
}

 ?>