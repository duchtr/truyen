<?php
class LoadingBar extends IPlugin{
	public $hasAdmin = true;
	public $file ;
	public $config ;
	public function __construct(){
		parent::__construct();
		$this->config= $this->getConfigPlugins();
		$this->file = count($this->config)>0?$this->config[0]:'line3color';
	}
	public function install(){
		$this->publishFile("theme/js/cube.js");
		$this->publishFile("theme/js/line.js");
		$this->publishFile("theme/js/line3color.js");
		$this->publishFile("theme/js/wave.js");
	}
	public function uninstall(){
		$this->removeFile();
	}
	public function insertScript(){
		return '<script defer type="text/javascript" src="'.$this->urlFile("theme/js/".$this->file.".js").'"></script>';
	}
}