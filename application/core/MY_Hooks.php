<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
define("PLUGIN_PATH", FCPATH."/application/plugins");
class MY_Hooks extends CI_Hooks{
	private $systemHooks = ["pre_system","pre_controller","post_controller_constructor","post_controller","display_override","cache_override","post_system"];
	public function __construct()
	{
		$CFG =& load_class('Config', 'core');
		log_message('info', 'Hooks Class Initialized');
		if ($CFG->item('enable_hooks') === FALSE)
		{
			return;
		}
		if (file_exists(APPPATH.'config/hooks.php'))
		{
			include(APPPATH.'config/hooks.php');
		}
		if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/hooks.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/hooks.php');
		}

		if ( ! isset($hook) OR ! is_array($hook))
		{
			return;
		}
		$this->hooks =& $hook;
		$this->enabled = TRUE;
	}

	public function call_hook($whichs = '')
	{
		if(is_string($whichs)){
			$which = $whichs;
			$params = [];
		}else{
			$which = count($whichs)>0?$whichs[0]:'';
			$params = array_slice($whichs,1);
		}
		if ( ! $this->enabled OR ! isset($this->hooks[$which]))
		{
			return FALSE;
		}

		if (is_array($this->hooks[$which]) && ! isset($this->hooks[$which]['function']))
		{
			
			if(in_array($which, $this->systemHooks)){
				foreach ($this->hooks[$which] as $val)
				{
					$this->_run_hook($val);	

				}
			}
			else{

				$tmphooks = $this->hooks[$which];
				usort($tmphooks,function($a,$b){
					$order1 = array_key_exists("order", $a)?$a['order']:0;
					$order2 = array_key_exists("order", $b)?$b['order']:0;
					return $order1 - $order2;
				});
				$bret = true;
				for ($i=0; $i < count($tmphooks); $i++) { 
					if($which=="tech5s_vindex_init"){
						$tmphooks[$i]["prevent_progress"] = true;
					}
					$ret = $this->_run_hook_custom($tmphooks[$i],$params);
					if(is_array($ret)){
						if(!is_array($bret)){
							$bret = [];
						}
						$bret = array_replace($bret, $ret);
						$params = array_replace($params,$ret);
					}
					else if(is_string($ret)){
						if(!is_string($bret)){
							$bret = "";
						}
						$bret .=$ret;
					}
					else {
						$bret = $ret;
					}
				}
				return $bret;
			}
		}
		else
		{
			if(in_array($which, $this->systemHooks)){
				 $this->_run_hook($this->hooks[$which]);

			}
			else{
				return $this->_run_hook_custom($this->hooks[$which],$params);
			}
			
		}

		return TRUE;
	}
	protected function _run_hook_custom($data,$outparams)
	{

		// Closures/lambda functions and array($object, 'method') callables
		if (is_callable($data))
		{
			if(is_array($data)){
				return $data[0]->{$data[1]}($outparams);
			}
			else{
				return $data($outparams);
			}

			return TRUE;
		}
		elseif ( ! is_array($data))
		{
			return FALSE;
		}
		// -----------------------------------
		// Safety - Prevents run-away loops
		// -----------------------------------

		// If the script being called happens to have the same
		// hook call within it a loop can happen
		$prevent_progress = array_key_exists('prevent_progress', $data)?$data['prevent_progress']:FALSE;
		if (!$prevent_progress && $this->_in_progress === TRUE)
		{
			return;
		}

		// -----------------------------------
		// Set file path
		// -----------------------------------

		if ( ! isset($data['filepath'], $data['filename']))
		{
			return FALSE;
		}

		$filepath = APPPATH.$data['filepath'].'/'.$data['filename'];

		if ( ! file_exists($filepath))
		{
			return FALSE;
		}

		// Determine and class and/or function names
		$class		= empty($data['class']) ? FALSE : $data['class'];
		$function	= empty($data['function']) ? FALSE : $data['function'];
		$params		= isset($data['params']) ? $data['params'] : '';
		$params = $outparams;
		if (empty($function))
		{
			return FALSE;
		}

		// Set the _in_progress flag
		$this->_in_progress = TRUE;

		// Call the requested class and/or function

		if ($class !== FALSE)
		{
			// The object is stored?
			if (isset($this->_objects[$class]))
			{
				if (method_exists($this->_objects[$class], $function))
				{
					$this->_in_progress = FALSE;
					return $this->_objects[$class]->$function($params);
				}
				else
				{
					return $this->_in_progress = FALSE;
				}
			}
			else
			{
				class_exists($class, FALSE) OR require_once($filepath);

				if ( ! class_exists($class, FALSE) OR ! method_exists($class, $function))
				{
					return $this->_in_progress = FALSE;
				}

				// Store the object and execute the method
				if(!array_key_exists($class, $this->_objects)){
					$this->_objects[$class] = new $class();
				}
				$this->_in_progress = FALSE;
				return $this->_objects[$class]->$function($params);
			}
		}
		else
		{
			function_exists($function) OR require_once($filepath);

			if ( ! function_exists($function))
			{
				return $this->_in_progress = FALSE;
			}
			$this->_in_progress = FALSE;
			return $function($params);
		}

		$this->_in_progress = FALSE;
		return TRUE;
	}
	protected function _run_hook($data)
	{
		// Closures/lambda functions and array($object, 'method') callables
		if (is_callable($data))
		{
			is_array($data)
				? $data[0]->{$data[1]}()
				: $data();

			return TRUE;
		}
		elseif ( ! is_array($data))
		{
			return FALSE;
		}

		// -----------------------------------
		// Safety - Prevents run-away loops
		// -----------------------------------

		// If the script being called happens to have the same
		// hook call within it a loop can happen
		if ($this->_in_progress === TRUE)
		{
			return;
		}

		// -----------------------------------
		// Set file path
		// -----------------------------------

		if ( ! isset($data['filepath'], $data['filename']))
		{
			return FALSE;
		}

		$filepath = APPPATH.$data['filepath'].'/'.$data['filename'];

		if ( ! file_exists($filepath))
		{
			return FALSE;
		}

		// Determine and class and/or function names
		$class		= empty($data['class']) ? FALSE : $data['class'];
		$function	= empty($data['function']) ? FALSE : $data['function'];
		$params		= isset($data['params']) ? $data['params'] : '';

		if (empty($function))
		{
			return FALSE;
		}

		// Set the _in_progress flag
		$this->_in_progress = TRUE;

		// Call the requested class and/or function
		if ($class !== FALSE)
		{
			// The object is stored?
			if (isset($this->_objects[$class]))
			{
				if (method_exists($this->_objects[$class], $function))
				{
					$this->_objects[$class]->$function($params);
				}
				else
				{
					return $this->_in_progress = FALSE;
				}
			}
			else
			{

				class_exists($class, FALSE) OR require_once($filepath);

				if ( ! class_exists($class, FALSE) OR ! method_exists($class, $function))
				{
					return $this->_in_progress = FALSE;
				}

				// Store the object and execute the method
				$this->_objects[$class] = new $class();
				$this->_objects[$class]->$function($params);

			}
		}
		else
		{
			function_exists($function) OR require_once($filepath);

			if ( ! function_exists($function))
			{
				return $this->_in_progress = FALSE;
			}

			$function($params);
		}

		$this->_in_progress = FALSE;
		return TRUE;
	}
}