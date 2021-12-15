<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admindao extends CI_Model
{
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->library(array('session'));
		$this->configsite();
	}
	function configsite(){
		if(!@$this->session->userdata('siteconfigs')){
			$q = $this->db->get('nuy_config');
			$configsite = $q->result_array();
			$arrConfigs= array();
			foreach ($configsite as $key => $value) {
				$arrConfigs[$value['name']] = $value['value'];
			}
			$this->session->set_userdata('siteconfigs',$arrConfigs);
		}
	}
	public function getConfigSite($key,$def){
		$configs = $this->session->userdata('siteconfigs');
		$key = strtoupper($key);
		if(@$configs && array_key_exists($key, $configs)){
			return $configs[$key];
		}
		return $def;
	}
	public function getAllFieldInTable($where,$ord=""){
		$sql = "select a.rpp_admin, b.* from nuy_table a, nuy_detail_table b where 1 =1 ";
		$condition =[];
		if(is_array($where)){
			foreach ($where as $w) {
				$sql .=" and ".$w["key"].$w["compare"].$w["value"];
			}
		}
		if($ord!=NULL && strlen($ord)>0){
			$sql .=" order by ".$ord;
		}
		$q = $this->db->query($sql,$condition);
		return $q->result_array();
	}
	public function getDataInTable($input,$table, $where,$number,$offset, $order="",$inputs=[]){
		$type_configs = substr($table,0,7);
		if($type_configs=='configs'){
			$order = " order by ord asc, id asc ";
		}
		$table ="`".$table."`";	
		$condition =[];
		$input = (strlen($input)<=0?$table.".*":$input);
		$input = addslashes($input);
		$sql = "select distinct %s from %s ";
		$params = [];
		array_push($params, $input);
		array_push($params, $table);
		$pivots = [];
		if(is_array($where)){
			foreach ($where as $k => $w) {
				$key = $w["key"];
				$compare = $w["compare"];
				$value = $w["value"];
				if(strpos($key, "pivot_")===0){
					if($value=="'-1'" || $value=='-1'){
						unset($where[$k]);
						continue;
					}
					$value = str_replace("'", "", $value);
					$psql = " inner join pivots on %s.id = pivots.sub_id where pivots.field_map = ? and pivots.parent_table = ? and pivots.parent_id = ? ";
					$psql = sprintf($psql,$table);
					$field_map = str_replace("pivot_", "", $key);
					$dataField = "";
					foreach ($inputs as $ki => $vi) {
						if($vi['name']==$key){
							$dataField =$vi["default_data"];
						}
					}
					$dataField = json_decode($dataField,true);
					$dataField = @$dataField['data']['value']?$dataField['data']['value']:[];
					$parent_table = array_key_exists('table', $dataField) ?$dataField['table']:"";
					array_push($condition, $field_map);
					array_push($condition, $parent_table);
					array_push($condition, $value);
					unset($where[$k]);
					$sql .=$psql;
					$pivots[$k] = $w;
				}
			}
		}
		if(count($pivots)==0){
			$sql .=" where 1=1 ";
		}

		if(is_array($where)){
			foreach ($where as $w) {
				$key = $w["key"];
				$compare = $w["compare"];
				$value = $w["value"];
				$sql .=" and ".$key.$compare."?";
				array_push($condition, $value);
			}
		}
		if(strlen($order)>0){
			$sql .=" ".$order;
		}
		else{
			$sql .=" order by id desc ";
		}
		if(strlen($number)>0 && strlen($offset)>0){
			$number = (int) $number;
			$offset = (int) $offset;
			$sql .=' limit %s,%s ';	
			array_push($params, $offset);
			array_push($params, $number);
		}
		$sql = vsprintf($sql, $params);
		$sql = trim($sql);
		$q = $this->db->query($sql,$condition);
		return $q->result_array();
	}
	public function getNumDataInTable($input,$table, $where,$inputs=[]){
		$table ="`".$table."`";	
		$condition =[];
		$params =[];
		$sql = "select count(DISTINCT (%s.id)) count from %s ";
		array_push($params, $table);
		array_push($params, $table);
		$pivots = [];
		if(is_array($where)){
			foreach ($where as $k => $w) {
				$key = $w["key"];
				$compare = $w["compare"];
				$value = $w["value"];
				if(strpos($key, "pivot_")===0){
					if($value=="'-1'" || $value=='-1'){
						unset($where[$k]);
						continue;
					}
					$value = str_replace("'", "", $value);
					$psql = " inner join pivots on %s.id = pivots.sub_id where pivots.field_map = ? and pivots.parent_table = ? and pivots.parent_id = ? ";
					$psql = sprintf($psql,$table);
					$field_map = str_replace("pivot_", "", $key);
					$dataField = "";
					foreach ($inputs as $ki => $vi) {
						if($vi['name']==$key){
							$dataField =$vi["default_data"];
						}
					}
					$dataField = json_decode($dataField,true);
					$dataField = @$dataField['data']['value']?$dataField['data']['value']:[];
					$parent_table = array_key_exists('table', $dataField) ?$dataField['table']:"";
					array_push($condition, $field_map);
					array_push($condition, $parent_table);
					array_push($condition, $value);
					unset($where[$k]);
					$sql .=$psql;
					$pivots[$k] = $w;
				}
			}
		}
		if(count($pivots)==0){
			$sql .=" where 1=1 ";
		}
		if(is_array($where)){
			foreach ($where as $w) {
				$key = $w["key"];
				$compare = $w["compare"];
				$value = $w["value"];
				$sql .=" and ".$key.$compare." ? ";
				array_push($condition, $value);
			}
		}
		$sql = vsprintf($sql, $params);
		$sql = trim($sql);
		$q = $this->db->query($sql,$condition);
		$results = $q->result_array();
		if(count($results)>0){
			return (int)$results[0]["count"];
		}
		return 0;
	}
	public function getRawDataInTable($table){
	
		$q = $this->db->get($table);
		return $q->result_array();
	}
	public function arrayToStringName($array,$key){
		$str ="";
		for($i=0;$i<count($array);$i++){
			$a = $array[$i];
			$str .=$a[$key];
			if($i<count($array)-1){
				$str .=",";
			}
		}
		return $str;
	}
	function runSQLCommand($sql){
		$q = $this->db->query($sql);
		return $q->result_array();
	}
	function recursiveTable($input="",$table,$field,$basefield,$fieldValue,$where,$ord=""){
		
		$sql = "select ".(strlen($input)>0?$input:"id, name")." from ".$table." where 1= 1 ";
		if($fieldValue!="-1"){
			$sql .=" and ".$field." = '".$fieldValue."'";
		}
		if(is_array($where)){
			foreach ($where as $k =>$v) {
				$sql .=" and ".$k." = ".$v."";
			}
		}
		if(!isNull($ord)){
			$sql .=" order by ".$ord;
		}
		$q = $this->db->query($sql);
		$arr = $q->result_array();
		$r = array();
		foreach ($arr as $item) {
			$obj = new stdClass();
			$obj->item = $item;
			if(array_key_exists($basefield, $item) && $fieldValue!="-1"){	
				$obj->childs= $this->recursiveTable($input,$table,$field,$basefield,$item[$basefield],$where);
			}
			else{
				$obj->childs=array();
			}
			array_push($r, $obj);
		}
		return $r;
	}
	function getRegionField($table){
		$sql = "select c.* from nuy_detail_table a, nuy_table b, nuy_detail_region c where a.act =1 and a.parent = b.id and a.region = c.id and b.map_table='$table' group by c.id";
		return $this->db->query($sql)->result_array();
	}
	function getRegionField2($table){
		$sql = "select c.* from $table a,nuy_detail_region c where c.id = a.region  group by a.region ";
		return $this->db->query($sql)->result_array();
	}
	function updateData($dataUpdate,$table,$where){
		$this->db->trans_start();
		if(is_array($where)){
			$str="";
			for($i=0;$i<count($where);$i++){
				$w = $where[$i];
				$str .=$w['key']." ".$w['compare']." ".$w['value']." ";
				if($i<count($where)-1)
					$str .=" and ";
			}
			$this->db->where($str);
		}
		$this->db->update($table,$dataUpdate);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		    return false;
		}
		else
		{
		    $this->db->trans_commit();
		    return true;
		}
	}
	function insertData($dataUpdate,$table){
		$this->db->trans_start();
		$this->db->insert($table,$dataUpdate);
		$lastId = $this->db->insert_id();
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		    return -1;
		}
		else
		{
		    $this->db->trans_commit();
		    return $lastId;
		}
	}
	function insertDataBatch($dataUpdate,$table){
		$this->db->trans_start();
		$this->db->insert_batch($table,$dataUpdate);
		$lastId = $this->db->insert_id();
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		    return -1;
		}
		else
		{
		    $this->db->trans_commit();
		    return $lastId;
		}
	}
	function deleteData($table,$where){
		$this->db->trans_start();
		
		if(count($where)>0){
			foreach ($where as $swhere) {
				foreach ($swhere as $key => $value) {
					$this->db->where($key,$value);		
				}
			}
			
			$this->db->delete($table);
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		    return false;
		}
		else
		{
		    $this->db->trans_commit();
		    return true;
		}
	}
	function updateOneField($data,$table,$where){
		$this->db->trans_start();
		if(count($where)>0){
			foreach ($where[0] as $key => $value) {
				$this->db->where($key,$value);		
			}
			$this->db->update($table,$data);
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		    return false;
		}
		else
		{
		    $this->db->trans_commit();
		    return true;
		}
	}
	function insertTableToSystem($p){
		$this->db->trans_start();
		$data['name']=$p["info_name"];
		$data['note']=$p["info_note"];
		$data['map_table']=$p["info_map_table"];
		$data['act']=$p['info_act'];
		$data['edit']=$p['info_edit'];
		$data['table_parent']=$p['info_table_parent'];
		$data['rpp_view']=$p['info_rpp_view'];
		$data['copy']=$p['info_copy'];
		$data['pagination']=$p['info_pagination'];
		$data['showinmenu']=$p['info_showinmenu'];
		$data['type']=$p['info_type'];
		$data['table_child']=$p['info_table_child'];
		$data['controller']=$p['info_controller'];
		$data['rpp_admin']=$p['info_rpp_admin'];
		$data['showinmenu']=$p['info_showinmenu'];
		$data['help']=$p['info_help'];
		$data['delete']=$p['info_delete'];
		$data['search']=$p['info_search'];
		$data['orient']=1;
		$data['create_time']=time();
		$nuytableid= $this->insertData($data,'nuy_table');
		$data = array();
		$arr = $this->selectAllColumnTable($p['table']);
		
		foreach ($arr as $item) {
			$primary = $item['COLUMN_TYPE']=='PRI';
			$data['name']= $item['column_name'];
			$data['required']= $primary===true?1:0;
			$data['note']= @$item['column_comment']?$item['column_comment']:$item['column_name'];
			$data['ord']= $item['ORDINAL_POSITION'];
			$type = $item['COLUMN_TYPE'];
			$it = strpos($type, "(");
			if($it===FALSE){
				$it = $type;
				$length = -1;
			}
			else{
				$length = str_replace("(", "", substr($type, $it+1)) ;
				$length = str_replace(")", "", $length) ;
					$type = substr($type, 0,$it);
			}
			$data['length']= $length;
			$data['create_time']=time();
			$data['update_time']=time();
			$data['link']=$p['table'];
			$data['parent']=$nuytableid;
			$data['help']=$data['note'];
			$data['region']=1;
			$data['type']=$primary===true?"PRIMARYKEY":"TEXT";
			$result= $this->insertData($data,'nuy_detail_table');
			
		}
		$data = array();
		$data['name']=$p['role_name'];
		$data['note']=$p['role_note'];
		$data['link']=$p['role_link'];
		$data['act']=1;
		$data['is_server']=0;
		$data['parent']=$p['role_parent'];
$last= $this->insertData($data,'nuy_group_module');
		$data = array();
		$data['group_module_id'] = $last;
		$data['role'] = 31;
		$data['group_user_id']  = $this->session->userdata('userdata')['user']['parent'];
		$result= $this->insertData($data,'nuy_role');
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		    return false;
		}
		else
		{
		    $this->db->trans_commit();
		    return true;
		}
	}
	function getExistTable($table){
		$sql = "select * from information_schema.`TABLES` where TABLE_NAME ='".$table."' AND TABLE_SCHEMA ='".$this->db->database."'";
		$q1= $this->db->query($sql)->num_rows();
		$sql = "select * from nuy_table where name = '".$table."'";
		$q2= $this->db->query($sql)->num_rows();		
		if($q1>0 || $q2>0){
			return 1;
		}
		else{
			return 0;
		}
	}
	function selectAllTableCanInsert(){
		$sql =" select table_name name, (case when ( TABLE_COMMENT is null or LENGTH(TRIM(TABLE_COMMENT)) =0 ) then table_name else table_comment end) comment from information_schema.`TABLES` a ";
		$sql .=" where a.TABLE_SCHEMA='".$this->db->database."' ";
		$sql .=" and table_name not in (select name from nuy_table) ";
		$q = $this->db->query($sql);
		return $q->result_array();
	}
	function selectAllColumnTable($table){
		$sql ="select  column_name, data_type,column_comment, CHARACTER_MAXIMUM_LENGTH, ORDINAL_POSITION, COLUMN_TYPE, COLUMN_KEY from information_schema.columns  where table_schema = '".$this->db->database."' and table_name = '".$table."'";
		$q = $this->db->query($sql);
		return $q->result_array();
	}
	public function getAllGroupUserOnlyId(){
		$gid = $this->session->userdata("userdata")['user']["parent"];
		$q = $this->db->query("select getAllGroupUser(".$gid.") idl");//group_id in() 
		$tmp =$q->result_array()[0]["idl"];
		if(strlen($tmp)>0){
			$str =$tmp;
		}
		else{
			$str = "";
		}
		return $str;
	}
	public function getAllModuleAccessByUser($parentModule){
		$gid = $this->session->userdata("userdata")['user']["parent"];
		$sql = "select * from nuy_module a, nuy_role b where a.parent = $parentModule and b.group_user_id= $gid and a.parent = b.group_module_id and (b.role & a.code )>0 group by a.id";
		$q = $this->db->query($sql);
		return $q->result_array();
	}
	public function getAllGroupUser(){
		$str = $this->getAllGroupUserOnlyId();
		$q=$this->db->query("select a.*, (select note from nuy_group_user b where b.id = a.parent) parentname from nuy_group_user a where FIND_IN_SET(id,'$str')>0 ");
		$ret = $q->result_array();
		return $ret;
	}
	function checkPermisstionModule($module){
		$userdata = getAdminUser();
		$permis = $userdata["permission"];
		$isServer = isAdminServer();
		foreach($permis as $item){
			if($item["name"]===$module["name"] || $isServer)
			{
				$idModule = $item["id"];
				$actionRole = $item["role"];
				if(((int)$actionRole)>0)
				{
					return true;
				}
			}
		}
		return false;
	}
	function checkPermissionAction($module,$action){
		$userdata = $this->session->userdata("userdata");
		$permis = $userdata["permission"];
		if(is_array($permis) && count($permis)>0){
			$ret = false;
			foreach($permis as $item){
				if($item["name"]===$module)
				{
					$ret = true;
					$idModule = $item["group_module_id"];
					$actionRole = $item["role"];
					break;
				}
			}
			if($ret){
				$q = $this->db->query("select code from nuy_module where parent = ".$idModule." and name ='".$action."'");
				$arr = $q->result_array();
				$code=0;
				if(count($arr)>0)
				{
					$code= (int) $arr[0]["code"];
				}
				return ((int) $actionRole & $code);
			}
			return $ret;
		}
		else return false;
	}
	public function getAllRoleGroupModule($groupUser){
		$query = "select * from nuy_group_module a,nuy_role b where a.id = b.group_module_id ";
		$query .="and b.group_user_id = ".$groupUser;
		$q= $this->db->query($query);
		return $q->result_array();
	}
	public function getMenuByUser(){
		$query = "select * from nuy_group_module where parent =0 order by ord";
		$q= $this->db->query($query);
		$arr = $q->result_array();
		
		$ret = array();
		foreach ($arr as $key) {
			$isServer = @$this->session->userdata('user_from_sv')?$this->session->userdata('user_from_sv'):0;
			$query = "select * from nuy_group_module where ".($isServer==0?"is_server=0":" 1=1 ")." and parent =".$key['id'].' order by ord';
			$q= $this->db->query($query);
			$arr1 = $q->result_array();	
			$parent = new stdClass();
			$tmp1 = array();
			foreach ($arr1 as $key1) {
				$item = $this->checkPermisstionModule($key1);
				if($item){
					array_push($tmp1, $key1);
				}
			}
			if(count($tmp1)>0)
			{
				$parent->name=$key["note"];
				$parent->name_en=$key["note_en"];
				$parent->icon = $key['icon'];
				$parent->childs=$tmp1;
				array_push($ret, $parent);
			}
			
		}
		return $ret;
	}
	public function checkUserLogin($username,$password){
		
		$this->db->where('username',$username);
		$this->db->where('act',1);
		$q = $this->db->get('nuy_user');
		$arr =  $q->result_array();
		if(count($arr)>0){
			if($this->bcrypt->check_password($password,$arr[0]['password'])){
				return $arr;
			}
			else return array();
		}
		else{
			return array();
		}
	}
	public function getTagInNuyRountes($link,$id,$table,$ext){
		$sql = "select * from nuy_routes where 1 = 1 ";
		if(!isNull($link)){
			$sql .=" and link = '".$link."'";
		}
		if(!isNull($id)){
			$sql .=" and tag_id != ".$id;
		}
		if(!isNull($table)){
			$sql .= " and table = '".$table."'";
		}
		$q= $this->db->query($sql);
		return $q->result_array();
	}
	public function getOneUserGroupSuper(){
		$sql = "select * from nuy_user where parent =1 and act=1 limit 1";
		$q = $this->db->query($sql);
		$q= $q->result_array();
		if(count($q)>0){
			return $q;
		}
		else{
			$sql = "select * from nuy_user where act=1 order by parent desc limit 1";
			$q = $this->db->query($sql);
			$q= $q->result_array();
			return $q;
		}
	}
	public function getControllerPath($table){
		$this->db->select('controller');
		$this->db->where('map_table',$table);
		$q=$this->db->get('nuy_table');
		return $q->result_array();
	}
	private function insertOneMenu($data,$table,$parent,$lv,$group){
  foreach ($data as $key => $value) {
   $dataInsert=$value;
   unset($dataInsert['children']);
   $dataInsert['parent']=$parent;
   $dataInsert['ord']=$lv;
   $dataInsert['group_id']= $group;
   $this->db->insert($table,$dataInsert); 
   $lastId = $this->db->insert_id();
   $lv++;
   $this->insertOneMenu($value['children'],$table,$lastId,$lv,$group);
  }
 }
	public function insertMenu($data,$table,$parent,$lv,$group){
		$this->db->trans_start();
		
		$this->insertOneMenu($data,$table,$parent,$lv,$group);
		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		    return -1;
		}
		else
		{
		    $this->db->trans_commit();
		    return 1;
		}
		
	}
	public function getPivotParent($currentRecordId,$currentTable,$parentTable){
		$this->db->select("parent_id");
		$this->db->where("sub_id",$currentRecordId);
		$this->db->where("sub_table",$currentTable);
		$this->db->where("parent_table",$parentTable);
		$resultPivots = $this->db->get("pivots")->result_array();
		$valueDb= array_column($resultPivots,'parent_id');
		return $valueDb;
	}
	
}	