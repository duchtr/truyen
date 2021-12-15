<?php
class MetaTable extends IPlugin{
	public $hasAdmin = true;
	protected $CI;

	public function __construct(){
		parent::__construct();
		$this->CI = &get_instance();
	}
	public function install(){
	}
	public function uninstall(){	
	}


	public function createMeta($args){
		$code = $args["code"];
		if($code==="create_meta"){
			$post = $this->CI->input->post();
			if(!isset($post)) return;
			$table = $post["table"];
			$name = $post["name"];
			$icon = $post["icon"];
			$table_child = $post["table_child"];
			$table_parent = $post["table_parent"];
			$position = isset($post['position']) ? $post['position'] : 32;
			$name = strlen($name)==0?$table:$name;
			$template = file_get_contents(PLUGIN_PATH."/meta_table/template.txt");
			$template = str_replace("#CLASS_NAME#", ucfirst($table)."Meta",$template);
			$template = str_replace("#TABLE#", $table,$template);
			$template = str_replace("#ADD_TABLE#", $this->getAddTableQuery($table), $template);
			$template = str_replace("#NAME#", $name, $template);
			$template = str_replace("#ICON#", $icon, $template);
			$template = str_replace("#NAME_EN#", str_replace('_', '', ucwords($table, '_')), $template);
			$template = str_replace("#TABLE_PARENT#", $table_parent, $template);
			$template = str_replace("#TABLE_CHILD#", $table_child, $template);
			$template = str_replace("#POSITION#", $position, $template);
			echo $template;
			


		}
		return true;
	}
	public function getAddTableQuery($table){
		$dbTech5sTable = new DBTech5sTable($table);

		$columns = $dbTech5sTable->getAllColumns();
		$str = "";
		foreach ($columns as $k => $column) {
			$addType = isset($column['length']) && (int)$column['length']>0 ? '('.$column['length'].')':'';
			$str .= '$dttable->addField("'.$column["name"].'","'.$column["dtype"].$addType.'","'.$column['comment'].'");';
		}

		return $str;
	}
}