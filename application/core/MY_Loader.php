<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";
class MY_Loader extends MX_Loader {

	public function view($view, $vars = array(), $return = FALSE) 
	{
		list($path, $_view) = Modules::find($view, $this->_module, 'views/');
		if ($path != FALSE) 
		{
			$this->_ci_view_paths = array($path => TRUE) + $this->_ci_view_paths;
			$view = $_view;
		}
		list($path, $_view)= $this->_findViewPlugins($view);
		if ($path != FALSE) 
		{
			$this->_ci_view_paths = array($path => TRUE) + $this->_ci_view_paths;
			$view = $_view;
		}
		return (method_exists($this, '_ci_object_to_array') ? $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return)) : $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_prepare_view_vars($vars), '_ci_return' => $return)));
	}
	public function pview($view, $vars = array(), $return = FALSE){
		list($path, $_view)= $this->_findViewPlugins($view);
		if ($path != FALSE) 
		{
			$this->_ci_view_paths = array($path => TRUE) + $this->_ci_view_paths;
			$view = $_view;
		}
		return (method_exists($this, '_ci_object_to_array') ? $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return)) : $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_prepare_view_vars($vars), '_ci_return' => $return)));

	}
	//plugin_name.view_name
	//File view dạng 
	private function _findViewPlugins($view){
		$views = explode(".", $view);
		if(count($views)<2)return;
		$dir = $views[0];
		unset($views[0]);
		$path = PLUGIN_PATH."/".$dir."/views/".implode('/', $views).".php";
		if(file_exists($path)){
			return [PLUGIN_PATH."/".$dir."/views/",implode('/', $views)];
		}

	}
}