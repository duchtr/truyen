<?php
class DBTech5sModule{
	protected $tableName;
	protected $CI;
	public function __construct($tableName){
		$this->tableName = $tableName;
		$this->CI = &get_instance();
	}
	private function insertModule($parent){
		$sql = "select * from INFORMATION_SCHEMA.TRIGGERS where EVENT_OBJECT_TABLE='nuy_group_module' and trigger_schema = database() and trigger_name='tr_gr_module_in';";
		$q = $this->CI->db->query($sql);
		$exist = $q->num_rows()>0;
		if($exist){
			return;
		}
		$data =[];
		$data["note"] = "Xem";
		$data["name"] = "view";
		$data["parent"] = $parent;
		$data["code"] = 1;
		$this->CI->Dindex->insertDataRet("nuy_module",$data);
		$data =[];
		$data["note"] = "Thêm";
		$data["name"] = "insert";
		$data["parent"] = $parent;
		$data["code"] = 2;
		$this->CI->Dindex->insertDataRet("nuy_module",$data);
		$data =[];
		$data["note"] = "Sửa";
		$data["name"] = "edit";
		$data["parent"] = $parent;
		$data["code"] = 4;
		$this->CI->Dindex->insertDataRet("nuy_module",$data);
		$data =[];
		$data["note"] = "Xóa";
		$data["name"] = "delete";
		$data["parent"] = $parent;
		$data["code"] = 8;
		$this->CI->Dindex->insertDataRet("nuy_module",$data);
		$data =[];

		$data["note"] = "Copy";
		$data["name"] = "copy";
		$data["parent"] = $parent;
		$data["code"] = 16;
		$this->CI->Dindex->insertDataRet("nuy_module",$data);
	}
	private function insertRole($gid){
		$data  = [];
		$data["group_module_id"] = $gid;
		$data["group_user_id"] = 1;
		$data["role"] = 255;
		$this->CI->Dindex->insertDataRet("nuy_role",$data);
	}
	public function insertGroupModule($note,$link='',$parent = 0,$icon = 'icon-paper-clip',$ord=5){
		$data["name"] = $this->tableName;
		$data["note"] = $note;
		$data["link"] = $link;
		$data["parent"] = $parent;
		$data["act"] = 1;
		$data["icon"] = $icon;
		$data["ord"] = $ord;
		$id = $this->CI->Dindex->insertDataRet("nuy_group_module",$data);
		$this->insertModule($id);
		$this->insertRole($id);
	}
	public function removeModule(){
		$this->CI->db->where("name",$this->tableName);
		$q = $this->CI->db->get("nuy_group_module",1,0);
		$ms = $q->result_array();
		if(count($ms)==0) return;
		$ms = $ms[0];
		$idms = $ms["id"];
		$this->CI->db->where("group_module_id",$idms);
		$this->CI->db->delete("nuy_role");

		$this->CI->db->where("id",$idms);
		$this->CI->db->delete("nuy_group_module");

		$this->CI->db->where("parent",$idms);
		$this->CI->db->delete("nuy_module");


	}
}