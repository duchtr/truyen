<?php
class Pro_reviewsMeta{
	protected $table = "pro_reviews";
	protected static $instance;
	private function __construct(){}
	public static function get_instance(){
		if(self::$instance){
			return self::$instance;
		}
		else return $instance = new self;
	}
	public function install(){
		$this->addTable();
		$this->createModule();
		$this->createTableInfo();
	}
	public function uninstall(){
		$this->removeTable();
		$this->removeModule();
		$this->removeTableInfo();
	}

	private function addTable(){
		$dttable = new DBTable($this->table);
		$dttable->addField("id","int","id");$dttable->addField("name","varchar(255)","Tên reviews sản phẩm");$dttable->addField("img","text(65535)","Ảnh sản phẩm");$dttable->addField("short_content","text(65535)","Nội dung ngắn");$dttable->addField("act","tinyint","Kích hoạt");$dttable->addField("ord","int","Sắp xếp");$dttable->addField("create_time","bigint","Thời gian tạo");$dttable->addField("update_time","bigint","Thời gian chỉnh sửa");
		$dttable->build();
	}
	private function removeTable(){
		$dttable = new DBTable($this->table);
		$dttable->dropTable();
	}
	private function createModule(){
		$m = new DBTech5sModule($this->table);
		$m->insertGroupModule('Đánh giá sản phẩm',"view/".$this->table,53,'icon-gift',2);
	}
	private function removeModule(){
		$m = new DBTech5sModule($this->table);
		$m->removeModule();
	}
	private function createTableInfo(){
		$m = new DBTech5sTable($this->table);
		$pid = $m->insertNuyTable(
			[
				"name"=>$this->table,
				"note"=>"Đánh giá sản phẩm",
				"note_en"=>"ProReviews",
				"map_table"=>$this->table,
				"table_parent"=>"",
				"table_child"=>"",
			]
		);
		$columns = $m->getAllColumns();
		foreach ($columns as $k => $column) {
			if($column['name']=="parent"){
				$column["type"]= "select";
				$column["default_data"] = $m->getDefaultData($column,"",0);
			}
			if($column['name']=="name"){
				$column["referer"] = $m->getRefererSlug();
			}
			$m->insertNuyDetailTable($column,$pid);
		}

	}
	private function removeTableInfo(){
		$m = new DBTech5sTable($this->table);
		$m->removeTable();
	}
}