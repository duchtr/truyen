<?php 
class PreProcess{
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
			$ana = "<script>";
			$ana .="(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){";
			$ana .="(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),";
			$ana .="m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)";
			$ana .="})(window,document,'script','//www.google-analytics.com/analytics.js','ga');";

			$ana .="ga('create', '".$this->CI->Dindex->getSettings('ANALYTICS')."', 'auto');";
			$ana .="ga('require', 'displayfeatures');";
			$ana .="ga('send', 'pageview');";

			$ana .="</script>";
			$output = preg_replace("/<\/head>/",TECH5S_HEADER().$ana."$0", $output);
			
			$editthtml ="";
			if($this->CI->Admindao->checkPermissionAction('edithtmlnow','edit')){
				$editthtml = '<script defer type="text/javascript" src="theme/admin/static/edithtml/editor.js"></script>';
				$editthtml.='<link rel="stylesheet" type="text/css" href="theme/admin/static/edithtml/editor.css">';
				$editthtml.='<div class="rightmenu">';
				$editthtml.='<ul>';
				$editthtml.='<li dt-action="editstyle">Chỉnh sửa Style</li>';
				$editthtml.='</ul>';
				$editthtml.='</div>';
				$editthtml.='<div class="bg-dialog" style="display:none;">';
				$editthtml.='<div class="dialog">';
				$editthtml.='<h3 class="title">Chỉnh sửa style</h3>';
				$editthtml.='<table class="tablestyle">';
				$editthtml.='</table>';
				$editthtml.='<input onclick="createElementStyle();" type="button" class="save" value="Save"/>';
				$editthtml.='<span onclick="closeDialog();" class="btnclose">X</span>';
				$editthtml.='</div>';
				$editthtml.='</div>';
				$editthtml.='<div id="bg-load" style="display:none;">';
				$editthtml.='<div class="loader">';
				$editthtml.='<div class="miniloader"></div>';
				$editthtml.='</div>';
				$editthtml.='</div>';
				$editthtml.='<button onclick="submitServer()" class="submitstyle">Submit Style</button>';
			}
			if(file_exists(FCPATH.'theme/frontend/style3.css')){
				$editthtml .=' <link rel="stylesheet" href="theme/frontend/style3.css">';	
			}
			$output = preg_replace("/<\/body>/", TECH5S_FOOTER().$editthtml."$0", $output);
			return $output;
		}
	}
	private function compressHtml($output){
		if(strpos(strtolower(uri_string()), "techsystem") !==FALSE){

			return $output;
		}
		else{
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
		
		}
		return $output;
	}
	public function inject_tokens($output)
	{
	    $output = preg_replace('/(<(form|FORM)[^>]*(method|METHOD)="(post|POST)"[^>]*>)/',
	                           '$0<input type="hidden" name="' . self::$token_name . '" value="' . self::$token . '">', 
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
	public function replaceHtml(){
		$output = $this->CI->output->get_output();
		$output = $this->addAnalytic($output);
		$output = $this->inject_tokens($output);


		$output = $this->compressHtml($output);
		$this->CI->output->_display($output);
		
	}
}

 ?>