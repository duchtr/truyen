<?php



class PictureHelper extends IPlugin{

	public $hasAdmin =true;

	protected $config;

	public function __construct(){

		parent::__construct();

		

		$this->config= $this->getConfigPlugins();

	}

	public function install(){

		$this->publishFile("theme/js/script.js");

	}



	public function uninstall(){



	}

	public function initVindex(){

		require_once 'helper.php';

		return true;

	}

	public function insertScript(){

		return '<script defer type="text/javascript" src="'.$this->urlFile("theme/js/script.js").'"></script>';

	}
	

}