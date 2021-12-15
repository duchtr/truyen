<?php
class DBTech5sTable{
	protected $CI;
	protected $tableName;
	public function __construct($tableName,$comment = ""){
		$this->CI = &get_instance();
		$this->tableName = $tableName;
	}
	public function getAllColumns(){
		$sql = "select column_name name,data_type dtype,(case when length(column_comment)>0 then column_comment else column_name end) comment,character_maximum_length length from information_schema.`COLUMNS` where table_name = '".$this->tableName."' and table_schema = database()";
		$q = $this->CI->db->query($sql);
		return $q->result_array();

	}
	public function getTableInfo(){
		$sql = "select table_name name,(case when length(table_comment)>0 then table_comment else table_name end) comment from information_schema.`TABLES` where table_name = '$this->tableName' and table_schema = database()";
		$q = $this->CI->db->query($sql);
		return $q->result_array();
	}
	public function insertNuyTable($tableinfos){

		$data = [];
		$data["name"] = $tableinfos['name'];
		$data["note"] = $tableinfos['note'];
		$data["note_en"] = array_key_exists("note_en", $tableinfos)?$tableinfos['note_en']:$tableinfos['note'];
		$data["map_table"] = $tableinfos['name'];
		$data["create_time"] =time();
		$data["act"] = 1;
		$data["pagination"] = array_key_exists("pagination", $tableinfos)?$tableinfos['pagination']:0;
		$data["table_parent"] = array_key_exists("table_parent", $tableinfos)?$tableinfos['table_parent']:'';
		$data["table_child"] = array_key_exists("table_child", $tableinfos)?$tableinfos['table_child']:'';
		$data["controller"] = $tableinfos['name'].".view";
		$data["rpp_admin"] = array_key_exists("rpp_admin", $tableinfos)?$tableinfos['rpp_admin']:'10';
		$data["rpp_view"] = array_key_exists("rpp_view", $tableinfos)?$tableinfos['rpp_view']:'10';
		$data["orient"] = 1;
		$data["type"] = array_key_exists("type", $tableinfos)?$tableinfos['type']:'1';
		$data["insert"] = array_key_exists("insert", $tableinfos)?$tableinfos['insert']:'1';
		$data["delete"] = array_key_exists("delete", $tableinfos)?$tableinfos['type']:'1';
		$data["edit"] = array_key_exists("edit", $tableinfos)?$tableinfos['edit']:'1';
		$data["help"] = array_key_exists("help", $tableinfos)?$tableinfos['help']:'1';
		$data["search"] = array_key_exists("search", $tableinfos)?$tableinfos['search']:'1';
		$data["quickpost"] = 0;
		$data["copy"] = array_key_exists("copy", $tableinfos)?$tableinfos['copy']:'1';
		$data["showinmenu"] = array_key_exists("showinmenu", $tableinfos)?$tableinfos['showinmenu']:'0';
		$data["dashboard"] = array_key_exists("dashboard", $tableinfos)?$tableinfos['dashboard']:'NULL';
		$data["quickaccess"] = array_key_exists("quickaccess", $tableinfos)?$tableinfos['quickaccess']:'NULL';
		$data["ext"] = array_key_exists("ext", $tableinfos)?$tableinfos['ext']:'NULL';
		return $this->CI->Dindex->insertDataRet("nuy_table",$data);

	}
	public function insertNuyDetailTable($iteminfos,$parent){
		$data = [];
		$data["name"] = $iteminfos["name"];
		$data["note"] = $iteminfos["comment"];
		$data["note_en"] = array_key_exists("comment_en", $iteminfos)?$iteminfos['comment_en']:(str_replace('_', ' ', ucwords($iteminfos["name"], '_')));
		$data["type"] = array_key_exists("type", $iteminfos)?$iteminfos["type"]:$this->getSuggestType($iteminfos);
		$data["create_time"] = time();
		$data["update_time"] = time();
		$data["link"] = $this->tableName;
		$data["view"] = array_key_exists("view", $iteminfos)?$iteminfos["view"]:$this->getViewable($iteminfos);
		$data["editable"] = array_key_exists("editable", $iteminfos)?$iteminfos["editable"]:$this->getEditable($iteminfos);
		$data["simple_searchable"] = array_key_exists("simple_searchable", $iteminfos)?$iteminfos["simple_searchable"]:'0';
		$data["searchable"] = array_key_exists("searchable", $iteminfos)?$iteminfos["searchable"]:'0';
		$data["quickpost"] = array_key_exists("quickpost", $iteminfos)?$iteminfos["quickpost"]:'0';
		$data["is_upload"] = array_key_exists("is_upload", $iteminfos)?$iteminfos["is_upload"]:'0';
		$data["parent"] =$parent;
		$data["default_data"] = array_key_exists("default_data", $iteminfos)?$iteminfos["default_data"]:'';
		$data["region"] = array_key_exists("region", $iteminfos)?$iteminfos["region"]:'1';
		$data["help"] = array_key_exists("help", $iteminfos)?$iteminfos["help"]:$data['note'];
		$data["ord"] = array_key_exists("ord", $iteminfos)?$iteminfos["ord"]:time();
		$data["act"] = array_key_exists("act", $iteminfos)?$iteminfos["act"]:'1';
		$data["referer"] = array_key_exists("referer", $iteminfos)?$iteminfos["referer"]:'';
		$id = $this->CI->Dindex->insertDataRet("nuy_detail_table",$data);
	}
	private function getSuggestType($item){
		switch ($item['name']) {
			case 'id':
				return "PRIMARYKEY";
			case 'create_time':
			case 'update_time':
				return "DATETIME";
			case 'act':
				return "CHECKBOX";
			case 'content':
				return "EDITOR";
			case 'file':
				return "FILEV2";
			case 'img':
				return "IMGV2";
			case 'parent':
				return "SELECT";
			default:
				switch ($item['dtype']) {
					case 'text':
						return 'EDITOR';
					default:
						return "TEXT";
				}
				return "TEXT";
		}
	}
	public function getDefaultData($item,$tableParent,$recur = 0){
		switch (strtolower($item["type"])) {
			case 'select':
				if($recur == 0){
					$json = '{
							"data": {
								"source": "database",
								"value": {
									"table": "'.$tableParent.'",
									"select": "id,name",
									"field": "parentd",
									"base_field": "idd",
									"field_value": "-1",
									"where": [{
										"1": "1"
									}]
								}
							},
							"config": {
								"searchbox": 1,
								"multiplie": 0
							}
						}';
				}
				else{
					$json =  '{"data": {
						  "source": "database",
						  "value": {
						   "table": "'.$tableParent.'",
						   "select": "id,name",
						   "field": "parent",
						   "base_field": "id",
						   "field_value": 0
						  }
						 },
						 "config": {
						  "searchbox": 1
						 }
						}';
				}
				
				return $json;
			
			default:
			return '';
		}
	}
	public function getRefererSlug(){
		return '[{
			"target": "this",
			"event": "input",
			"function": "count"
		}, {
			"target": "slug",
			"event": "input",
			"function": "slug",
			"when": "6"
		}]';
	}
	private function getViewable($item){
		if(in_array($item['name'], ["name","act","ord"])){
			return "1";
		}
		return "0";
	}
	private function getEditable($item){
		if(in_array($item['name'], ["name","act","ord"])){
			return "1";
		}
		return "0";
	}
	public function removeTable(){
		$this->CI->db->where("link",$this->tableName);
		$this->CI->db->delete("nuy_detail_table");

		$this->CI->db->where("name",$this->tableName);
		$this->CI->db->delete("nuy_table");

	}
}