<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
require_once "HBladeCompiler.php";
require_once "HCompilerEngine.php";
require_once "HFactory.php";
require_once APPPATH."hooks/hook_plugins.php";
use Philo\Blade\Blade as BaseBlade;
use Illuminate\View\Engines\CompilerEngine;
class Blade extends BaseBlade{
	protected $CI;
	public function __construct(){
		$this->CI =& get_instance();
		$this->CI->config->load('blade');
		$views = $this->CI->config->item('path_blade_view');
		$cache = $this->CI->config->item('path_blade_cache');
		parent::__construct($views,$cache);
		$vs = $this->loadPluginViews();
		$this->CI->hooks->call_hook(['construct_blade']);
	}
	private function loadPluginViews(){
		$finder = $this->container['view.finder'];
		$plugins = HookPlugin::_loadPlugins();
		foreach ($plugins as $k => $plugin) {
			$file = PLUGIN_PATH."/".$plugin['name']."/config.json";
			if(file_exists($file)){
				$content = json_decode(file_get_contents($file),true);
				$class =  str_replace('_', '', ucwords($plugin['name'], '_'));
				if(!class_exists($class)) {
					$classFile = PLUGIN_PATH."/".$plugin['name']."/".$class.".php";
					if(!file_exists($classFile)){
						break;
					}
				}
				$pluginView = PLUGIN_PATH."/".$plugin['name']."/blade_views";
				if(file_exists($pluginView)){
					$finder->addNamespace($plugin['name'], $pluginView);
				}
			}
		}
	}
	
	public function registerBladeEngine($resolver)
	{
		$me = $this;
		$app = $this->container;
		$this->container->singleton('blade.compiler', function($app) use ($me)
		{
			$cache = $me->cachePath;
			return new HBladeCompiler($app['files'], $cache);
		});
		$resolver->register('blade', function() use ($app)
		{
			return new HCompilerEngine($app['blade.compiler'], $app['files']);
		});
	}	
	public function registerFactory()
	{
		// Next we need to grab the engine resolver instance that will be used by the
		// environment. The resolver will be used by an environment to get each of
		// the various engine implementations such as plain PHP or Blade engine.
		$resolver = $this->container['view.engine.resolver'];
		$finder = $this->container['view.finder'];
		$env = new HFactory($resolver, $finder, $this->container['events']);
		// We will also set the container instance on this view environment since the
		// view composers may be classes registered in the container, which allows
		// for great testable, flexible composers for the application developer.
		$env->setContainer($this->container);
		return $env;
	}
}
 ?>