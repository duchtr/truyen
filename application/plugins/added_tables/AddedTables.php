<?php
spl_autoload_register(function($class){
	if(file_exists(__dir__."/tables/$class.php")){
		require_once __dir__."/tables/$class.php";
	}
});
class AddedTables extends IPlugin
{
	public $CI;
	protected $chapter;
	protected $say_high;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->chapter = ChapterMeta::get_instance();
		$this->say_high = Say_highMeta::get_instance();
	}
	public function install()
	{
		$this->chapter->install();
		$this->say_high->install();
	}
	public function uninstall()
	{
		// $this->pro_review->uninstall();
		// $this->deliver->uninstall();
		// $this->introduce->uninstall();
	}
}