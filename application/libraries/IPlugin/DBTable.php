<?php
class DBTable{
	protected $tableName;
	protected $command;
	protected $strField = "";
	protected $CI;
	public function __construct($tableName,$comment = ""){
		$this->tableName = $tableName;
		$this->CI = &get_instance();
		$this->command ="create table IF NOT EXISTS $tableName (%s) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '$comment' ROW_FORMAT = Compact;";
	}
	private function checkIsText($type){
		$arr = ["varchar","text"];
		foreach ($arr as $k => $item) {
			if(strpos($type, $item)!==FALSE){
				return true;
			}
		}
		return false;

	}
	public function dropTable(){
		$sql = "DROP TABLE if EXISTS ".$this->tableName;
		$this->CI->db->query($sql);
	}
	public function addField($name,$type='varchar(255)',$comment='',$allowNull=true,$default=''){
		$allowNull = $name == "id"?true:$allowNull;
		if(strlen($this->strField)>0){
			$this->strField.=",";
		}
		$this->strField .= "`$name` $type ".($this->checkIsText($type)?'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci':'').($allowNull?' NULL ':' NOT NULL ').($name=="id"?' AUTO_INCREMENT ':'').' DEFAULT '.($default==''?'NULL':$default)." COMMENT '".$comment."'";
		if($name=="id"){
			$this->strField.=",PRIMARY KEY (`id`) USING BTREE";
		}
		return $this;
	}
	public function modifyColumn($name,$type='varchar(255)',$allowNull=true,$default='',$comment=''){
		$sql = "ALTER TABLE $this->tableName ADD "."`$name` $type ".($this->checkIsText($type)?'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci':'').($allowNull?' NULL ':' NOT NULL ').($name=="id"?' AUTO_INCREMENT ':'').' DEFAULT '.($default==''?'NULL':$default)." COMMENT '".$comment."'";
		$this->CI->db->query($sql);
	}
	public function build(){
		$sql = sprintf($this->command,$this->strField);
		$this->CI->db->query($sql);
		return $this;
	}

}