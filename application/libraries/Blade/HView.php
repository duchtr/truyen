<?php 
require_once "Hpreprocess.php";
use Illuminate\View\View;
class HView extends View{
	public function render(callable $callback = null,$compress = true)
    {
    	$output = parent::render($callback);
    	if($this->view=="index"){
    		$pp = new Hpreprocess();
	    	$pp -> generate_token();

            $output = $pp -> replaceHtml($output,$compress);
    	}
        $view = $this->view;
        $CI = &get_instance();
    	$resultHook = $CI->hooks->call_hook(['tech5s_before_render',"output"=>$output,"view"=>$view]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
    	return $output;
    }
}

 ?>