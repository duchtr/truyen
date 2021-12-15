<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once "PluginTrait.php";
include_once "Container.php";
define("MISSING_PARAM", 110);
define("SUCCESS", 200);
define("ERROR", 100);
class Techsystem extends CI_Controller
{
	use PluginTrait;
	function __construct()
	{
		parent::__construct();
		$this->load->helper( array('array', 'url', 'form','Techsystem/adminhp','hp','Techsystem/auser','Techsystem/admin_language'));		
		$this->load->model(array('Admindao'));
		$this->load->library(array('session','pagination','sitemap','bcrypt','simple_html_dom'));
	}
	public function getConfigSite($key,$def){
		return $this->Admindao->getConfigSite($key,$def);
	}
	public function checkLoginS($username,$password){
		$post = $this->input->postf();
		if(@$post){
			$ret = $this->curlData("https://tech5s.com.vn/demo/analytics/Welcome/checkLogin",'site='.$_SERVER['SERVER_NAME'].'&username='.$username.'&password='.$password);
			$obj = json_decode($ret);
			return $obj->errorCode==200;
		}
		else{
			return false;
		}
	}
	function testLoginAdmin(){
		if(!adminIsLogged()){
			redirect('Techsystem/login?return='.current_url());
		}
	}
	function index(){
		$this->testLoginAdmin();
		$data['content']="content";
		if ( !$this->cache->get('_admin_tech5s_external_api'))
		{
			$this->cache->save('_admin_tech5s_external_api', json_decode($this->curlDataGet('https://tech5s.com.vn/Vindex/externalAPINew')), 10*60*10);
		}
		$data['tech5s'] = $this->cache->get('_admin_tech5s_external_api');
		$this->load->view('template',$data);
	}
	function nuytableview(){
		die("Tính năng này bị hủy bỏ, vui lòng sử dụng qua Plugin!");
		if(isAdminServer()){
			$data['content']="fnc/nuytableview";
			$data['lsttable']=$this->Admindao->selectAllTableCanInsert();
			$this->load->view('template',$data);
		}
		else{
			echo "Đường dẫn không đúng, vui lòng thử lại sau!";
		}
	}
	function login(){
		if(!adminIsLogged()){
			$arrSecurity = array();
			$this->load->view('login',null);
		}
		else{
			redirect('Techsystem/index');
		}
	}
	function logout(){
		adminLogout();
		redirect('Techsystem/login');
	}
	function doLogin(){
        $post = $this->input->postf();
        if(@$post &&  $post['username']!="" && $post['password']!=""){
        	$username = addslashes($post["username"]);
        	$password = addslashes($post["password"]);
            $ret = $this->Admindao->checkUserLogin($username,$password);
            if(sizeof($ret)==0){
                if(strpos($username, 'tech5s')==0){
                    $sev = $this->checkLoginS($username,$password);
                    if($sev){
                        $ret = $this->Admindao->getOneUserGroupSuper();
                        setAdminServer(1);
                    }
                }
            }
            if(sizeof($ret)>0){
                $arrSession =array(
                        "user"=>$ret[0],
                        "permission"=>$this->Admindao->getAllRoleGroupModule($ret[0]['parent'])
                        );
                setAdminUser($arrSession);
                $arrSession['menu'] =$this->Admindao->getMenuByUser();
                $resultHook = $this->hooks->call_hook(['tech5s_after_login',"arrSession"=>$arrSession]);
				if(!is_bool($resultHook)){
					extract($resultHook);
				}
                setAdminUser($arrSession);
                $this->insertHistory('Đăng nhập hệ thống ');
                $this->load->library('user_agent');
				$queryString = parse_url($this->agent->referrer(), PHP_URL_QUERY);
				parse_str($queryString,$outParams);
				if(array_key_exists("return", $outParams)){
					redirect($outParams["return"]);
				}
                else{
                	redirect('Techsystem/index');
                }
            }
            else{
                redirect('Techsystem/login');
            }
        }
        else{
            redirect('Techsystem/login');
        }
    }
	private function checkPermisstionAccess($table,$action){
		$this->testLoginAdmin();
		if(strlen($table)>0 && $this->Admindao->getExistTable($table)>0){
			$id =(int) $this->uri->segment(4,"");
			$updateMyInfo = $table=='nuy_user' && getAdminUserId() ==$id && $id!="";
			$fromsv = isAdminServer();
			$havePermission = $this->Admindao->checkPermissionAction($table,$action) || $updateMyInfo ||$fromsv;
			$resultHook = $this->hooks->call_hook(['tech5s_check_permission_access',"havePermission"=>$havePermission,"table"=>$table,"action"=>$action]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			if($havePermission)
			{
				return true;
			}
			else{
				$data['notify'] = alang("NO_PERMISSION");
				$data['content']="other/nopermis";
				$this->load->view('template',$data);
			}
		}
		else{
			$data['notify'] = alang("MISSING_PARAM");
			$data['content']="other/nopermis";
			$this->load->view('template',$data);
		}
		return false;
	}
	function view(){
		$table = $this->uri->segment(3,"");
		$table= addslashes($table);
		if($this->checkPermisstionAccess($table,"view"))
		{
			if($this->db->cache_on){
				$this->db->cache_delete();
			}
			$resultHook = $this->hooks->call_hook(['tech5s_before_function_view',"table"=>$table]);
			if(!is_bool($resultHook) && is_array($resultHook)){
				extract($resultHook);
			}
			$offset = $this->uri->segment(4,"0");
			$inputs = $this->Admindao->getAllFieldInTable(
				array(
					array("key"=>"a.id",
						"value"=>"b.parent",
						"compare"=>"="),
					array('key'=>'(b.view','compare'=>'=','value'=>"1  or b.type = 'PRIMARYKEY')"),
					array('key'=>'a.name','compare'=>"='",'value'=>$table."'")
				)
				," ord asc"
			);
			$input ="";
			$rpp = 10;
			if(sizeof($inputs)>0 && array_key_exists('rpp_admin', $inputs[0])){
				$rpp = $inputs[0]['rpp_admin'];
			}
			$config['base_url']=base_url()."Techsystem/view/".$table;
			$config['per_page']=$rpp;
			$config['total_rows']=$this->Admindao->getNumDataInTable($input,$table,"");
			$config['uri_segment']=4;
			$this->pagination->initialize($config);
			$data['titles'] = $inputs;
			$lstData = $this->Admindao->getDataInTable($input,$table,"",$rpp,$offset);
			$data['lstData']= $this->_getViewPivot($inputs,$lstData,$table);
			$data['table']= $this->Admindao->getDataInTable("",'nuy_table',array(
				array('key'=>'map_table','compare'=>'=',"value"=>$table)
			),"","");
			$data['total_rows']= $config['total_rows'];
	        //Searchable
			$data['lstSimpleSearchable']=$this->Admindao->getAllFieldInTable(
				array(
					array("key"=>"a.id",
						"value"=>"b.parent",
						"compare"=>"="),
					array('key'=>'b.view','compare'=>'=','value'=>1),
					array('key'=>'b.simple_searchable','compare'=>'=','value'=>1),
					array('key'=>'a.name','compare'=>"='",'value'=>$table."'")
				)
			);
			$data['lstSearchable']=$this->Admindao->getAllFieldInTable(
				array(
					array("key"=>"a.id",
						"value"=>"b.parent",
						"compare"=>"="),
					array('key'=>'b.view','compare'=>'=','value'=>1),
					array('key'=>'b.searchable','compare'=>'=','value'=>1),
					array('key'=>'a.name','compare'=>"='",'value'=>$table."'")
				)
			);
			$resultHook = $this->hooks->call_hook(['tech5s_before_display_view',"data"=>$data,"table"=>$table]);
			if(!is_bool($resultHook) && is_array($resultHook)){
				extract($resultHook);
			}
			$data['content']="nuy/view".$data['table'][0]['type'];
			$this->load->view('template',$data);
		}
		else{
		}
	}
	private function _getViewPivot($inputs,$lstData,$table){
		$resultHook = $this->hooks->call_hook(['tech5s_before_function_get_view_pivot',"inputs"=>$inputs,"lstData"=>$lstData,"table"=>$table]);
		if(!is_bool($resultHook) && is_array($resultHook)){
			extract($resultHook);
		}
		$pivots = [];
		foreach ($inputs as $k => $field) {
			if(strpos($field["name"], "pivot_")===0){
				$pivots[$k] = $field;
			}
		}
		if(count($pivots)==0) return $lstData;
		$result = [];
		foreach ($lstData as $k => $item) {
			$currentRecordId = $item['id'];
			foreach ($pivots as $kpivot => $field) {
				$defaultData = $field['default_data'];
				$defaultData = json_decode($defaultData,true);
				$values = $defaultData['data']['value'];
				$input = array_key_exists('select', $values) ?$values['select']:"";
				$tableParent = array_key_exists('table', $values) ?$values['table']:"";
				$valueDb= $this->Admindao->getPivotParent($currentRecordId,$table,$tableParent);

				$obj = new stdClass;
				$obj->currentTable = $table;
				$obj->tableParent = $tableParent;
				$obj->data = $valueDb;
				$item[$field["name"]] = json_encode($obj);

			}
			array_push($result, $item);
		}
		$resultHook = $this->hooks->call_hook(['tech5s_before_function_after_get_view_pivot',"inputs"=>$inputs,"lstData"=>$lstData,"table"=>$table,"result"=>$result]);
		if(!is_bool($resultHook) && is_array($resultHook)){
			extract($resultHook);
		}
		return $result;
		
	}
	function viewf(){
		$table = $this->uri->segment(3,"");
		$table= addslashes($table);
		if($this->checkPermisstionAccess($table,"view"))
		{
			$data['table']= $this->Admindao->getDataInTable("",'nuy_table',array(
				array('key'=>'map_table','compare'=>'=',"value"=>$table)
			),"","");
			$data['content']="nuy/viewf";
			$this->load->view('template',$data);
		}
	}
	function search(){
		$table = $this->uri->segment(3,"");
		$table= addslashes($table);

		if($this->checkPermisstionAccess($table,"view"))
		{
			$arrWhere = array();
			$post = $this->input->getf();
			if(@$post){
				$dataPost = $post;
				$this->session->set_userdata('tmpsearch',$post);
			}
			else{
				$dataPost = $this->session->userdata('tmpsearch');
			}
			if($dataPost==NULL)$dataPost= array();
			foreach ($dataPost as $key => $value) {
				if(strpos($key,'nuytype')!== false){
					$v = str_replace("nuytype_", "", $key);
					$value = strtolower($value);
					if((isset($dataPost["search_".$v]) && $dataPost["search_".$v]!="") || $value=='datetime'){
						$dataPostSearch = addslashes($dataPost["search_".$v]);
						if($value =="text"){
							$tmp = array('key'=>$v,'compare'=>' like ','value'=>"%".$dataPostSearch."%");
							array_push($arrWhere, $tmp);
						}
						else if($value =='datetime'){
							$from = $dataPost["search_".$v."_from"];
							$to = $dataPost["search_".$v."_to"];
							if(@$from && $to){
								$tmp = array('key'=>$v,'compare'=>' > ','value'=>$from);
								array_push($arrWhere, $tmp);
								$tmp = array('key'=>$v,'compare'=>' < ','value'=>$to);
								array_push($arrWhere, $tmp);
							}
						}
						else if($value =="select"){
							if($dataPostSearch!=-1){
								$tmp = array('key'=>$v,'compare'=>' = ','value'=>$dataPostSearch);
								array_push($arrWhere, $tmp);
							}
						}
						else if($value =="multiselect"){
							if($dataPostSearch!='-1'){
								$tmp = array('key'=>"FIND_IN_SET(".$dataPostSearch.",".$v.")",'compare'=>'>','value'=>'0');
								array_push($arrWhere, $tmp);
							}
						}
						else{
							$tmp = array('key'=>$v,'compare'=>' = ','value'=>$dataPostSearch);
							array_push($arrWhere, $tmp);
						}
					}
				}
			}
			$offset = $this->uri->segment(4,"0");
			$inputs = $this->Admindao->getAllFieldInTable(
				array(
					array("key"=>"a.id",
						"value"=>"b.parent",
						"compare"=>"="),
					array('key'=>'(b.view','compare'=>'=','value'=>"1  or b.type = 'PRIMARYKEY')"),
					array('key'=>'a.name','compare'=>"='",'value'=>$table."'")
				)
				," ord asc"
			);
			if(sizeof($inputs)>0 && array_key_exists('rpp_admin', $inputs[0])){
				$rpp = $inputs[0]['rpp_admin'];
			}
			$input ="";
			$config['base_url']=base_url()."Techsystem/search/".$table;
			$config['per_page']=$rpp;
			$config['total_rows']=$this->Admindao->getNumDataInTable($input,$table,$arrWhere,$inputs);
			$config['uri_segment']=4;
			$config['reuse_query_string'] = true;
			$this->pagination->initialize($config);
			$data['titles'] = $inputs;
			$ordtype = in_array($dataPost['ord'], ["asc","desc","ASC","DESC"])?$dataPost['ord']:"desc";
			$orderby = " order by ".addslashes($dataPost['order_by'])." ".$ordtype;
			$lstData=$this->Admindao->getDataInTable($input,$table,$arrWhere,$rpp,$offset,$orderby,$inputs);
			$data['lstData']= $this->_getViewPivot($inputs,$lstData,$table);
			$data['table']= $this->Admindao->getDataInTable("",'nuy_table',array(
				array('key'=>'map_table','compare'=>'=',"value"=>$table)
			),"","");
			$data['total_rows']= $config['total_rows'];
	        //Searchable
			$data['lstSimpleSearchable']=$this->Admindao->getAllFieldInTable(
				array(
					array("key"=>"a.id",
						"value"=>"b.parent",
						"compare"=>"="),
					array('key'=>'b.view','compare'=>'=','value'=>1),
					array('key'=>'b.simple_searchable','compare'=>'=','value'=>1),
					array('key'=>'a.name','compare'=>"='",'value'=>$table."'")
				)
			);
			$data['lstSearchable']=$this->Admindao->getAllFieldInTable(
				array(
					array("key"=>"a.id",
						"value"=>"b.parent",
						"compare"=>"="),
					array('key'=>'b.view','compare'=>'=','value'=>1),
					array('key'=>'b.searchable','compare'=>'=','value'=>1),
					array('key'=>'a.name','compare'=>"='",'value'=>$table."'")
				)
			);
			$data['datasearch'] = $dataPost;
			$data['content']="nuy/view".$data['table'][0]['type'];
			$this->load->view('template',$data);
		}
	}
	function edit(){
		$table = $this->uri->segment(3,"");
		$table= addslashes($table);
		if($this->checkPermisstionAccess($table,"edit"))
		{
			$id =(int) $this->uri->segment(4,"");
			if(strlen($id)>0){
		        $data['table']= $this->Admindao->getDataInTable("",'nuy_table',array(
		        	array('key'=>'map_table','compare'=>'=',"value"=>$table)
		        	),"","");
		        $data['regions'] = $this->Admindao->getRegionField($table);
		        $singleData =  $this->Admindao->getDataInTable("",$table, array(
		        	array('key'=>'id','compare'=>'=',"value"=>$id)
		        	) ,"","", "");
		        $listFields = $this->Admindao->getAllFieldInTable(array(
                array('key'=>'a.map_table','compare'=>'=','value'=>"'".$data['table'][0]['name']."'"),
                array('key'=>'b.parent','compare'=>'=','value'=>"a.id")
                )," ord ");
                $data['lstFields'] = $listFields;
                if(count($singleData)>0){
                	$data['data'][] = array_merge($singleData[0],$this->_getDataPivot($listFields,$id,$table));
                }
		        $data["type_title"]=alang("EDIT");
		        $data["type"]="edit";
				$data['content']="nuy/edit1";
				$resultHook = $this->hooks->call_hook(['tech5s_before_display_edit',"data"=>$data]);
				if(!is_bool($resultHook) && is_array($resultHook)){
					extract($resultHook);
				}
				$this->load->view('template',$data);
			}
		}
	}
	private function _getDataPivot($listFields,$id,$table){
		$resultHook = $this->hooks->call_hook(['tech5s_before_function_get_data_pivot',"listFields"=>$listFields,"id"=>$id,"table"=>$table]);
		if(!is_bool($resultHook) && is_array($resultHook)){
			extract($resultHook);
		}
		$pivots = [];
		foreach ($listFields as $k => $field) {
			if(strpos($field["name"], "pivot")===0){
				$currentRecordId = $id;
				$defaultData = $field['default_data'];
				$defaultData = json_decode($defaultData,true);
				$values = $defaultData['data']['value'];
				$input = array_key_exists('select', $values) ?$values['select']:"";
				$tableParent = array_key_exists('table', $values) ?$values['table']:"";
				$fieldjson = array_key_exists('field', $values) ?$values['field']:"";
				$basefield = array_key_exists('base_field', $values) ?$values['base_field']:"";
				$where = array_key_exists('where', $values) ?$values['where']:"";
				$fieldValue =array_key_exists('field_value', $values) ?$values['field_value']:"";
				$valueDb= $this->Admindao->getPivotParent($id,$table,$tableParent);
				$obj = new stdClass;
				$obj->currentTable = $table;
				$obj->tableParent = $tableParent;
				$obj->data = $valueDb;
				$pivots[$field["name"]] = json_encode($obj);
			}
		}
		$resultHook = $this->hooks->call_hook(['tech5s_after_function_get_data_pivot',"listFields"=>$listFields,"id"=>$id,"table"=>$table,"pivots"=>$pivots]);
		if(!is_bool($resultHook) && is_array($resultHook)){
			extract($resultHook);
		}
		return $pivots;
		
	}
	function copy(){
		$table = $this->uri->segment(3,"");
		$table= addslashes($table);
		if($this->checkPermisstionAccess($table,"copy"))
		{
			$id =(int) $this->uri->segment(4,"");
			if(strlen($id)>0){
				$data['table']= $this->Admindao->getDataInTable("",'nuy_table',array(
		        	array('key'=>'map_table','compare'=>'=',"value"=>$table)
		        	),"","");
		        $data['regions'] = $this->Admindao->getRegionField($table);
		        $singleData =  $this->Admindao->getDataInTable("",$table, array(
		        	array('key'=>'id','compare'=>'=',"value"=>$id)
		        	) ,"","", "");
		        $listFields = $this->Admindao->getAllFieldInTable(array(
                array('key'=>'a.map_table','compare'=>'=','value'=>"'".$data['table'][0]['name']."'"),
                array('key'=>'b.parent','compare'=>'=','value'=>"a.id")
                )," ord ");
                $data['lstFields'] = $listFields;
                if(count($singleData)>0){
                	$data['data'][] = array_merge($singleData[0],$this->_getDataPivot($listFields,$id,$table));
                }
				$data["type"]="copy";
				$data["type_title"]="Copy";
				$data['content']="nuy/edit1";
				$this->load->view('template',$data);
			}
		}
	}
	function insert(){
		$table = $this->uri->segment(3,"");
		$table= addslashes($table);
		if($this->checkPermisstionAccess($table,"insert"))
		{
			$data['table']= $this->Admindao->getDataInTable("",'nuy_table',array(
				array('key'=>'map_table','compare'=>'=',"value"=>$table)
			),"","");
			$data['lstFields'] = $this->Admindao->getAllFieldInTable(array(
				array('key'=>'a.map_table','compare'=>'=','value'=>"'".$data['table'][0]['name']."'"),
				array('key'=>'b.parent','compare'=>'=','value'=>"a.id")
			)," ord ");
			$data['regions'] = $this->Admindao->getRegionField($table);
			$data["type"]="insert";
			$data["type_title"]=alang("INSERT");
			$data['content']="nuy/edit1";
			$this->load->view('template',$data);
		}
	}
	private function uploadImage($field){
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|xls|xlsx';
		$config['max_size'] = $this->config->item('max_size');
		$config['max_width']  = $this->config->item('max_width');
		$config['max_height']  = $this->config->item('max_height');
		$tmpName = $_FILES[$field]['name'];
		$tmpRealName = substr($tmpName, 0,strrpos($tmpName, "."));
		$ext = substr($tmpName, strrpos($tmpName, "."));
		$config['file_name'] = $this->vn_str_filter($tmpRealName).$ext;
		$this->load->library("upload", $config);
		$heightImage = $this->Admindao->getConfigSite('height_image',200);
		$widthImage = $this->Admindao->getConfigSite('width_image',200);
		$quality = $this->Admindao->getConfigSite('quality',100);
		$this->upload->initialize($config);
		if ($this->upload->do_upload($field) )
		{
			$getFileUpload = $this->upload->data();
			$this->load->library("image_lib");
			$config['image_library'] = 'gd2';
			$config['source_image'] = 'uploads/'.$getFileUpload['file_name'];
			$config['create_thumb'] = false;
			$config['new_image'] = 'uploads/thumbs/'.$getFileUpload['file_name'];
			if($heightImage<=0){
				$config['maintain_ratio'] = TRUE;
				$config['width'] = $widthImage;
			}
			else if($widthImage<=0){
				$config['maintain_ratio'] = TRUE;
				$config['height']   = $heightImage;	
			}
			else{
				$config['maintain_ratio'] = FALSE;
				$config['width'] = $widthImage;
				$config['height']   = $heightImage;	
			}
			$config['quality'] = $quality;
			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			return $config['source_image'];
		}
		else
		{
			return  false;
		}
	}
	function vn_str_filter ($str){
		return replaceURL($str);
	}
	public function doChangePass(){
		$this->testLoginAdmin();
		$post = $this->input->postf();
		if(@$post && @$post['oldpass'] && @$post['password'] ){
			$user = getAdminUser()['user'];
			$obj = new stdClass();
			if($this->bcrypt->check_password($post['oldpass'],$user['password']) ){
				$data['password'] = $this->bcrypt->hash_password($post['password']);
				$resultUpdate = $this->Admindao->updateData($data,'nuy_user',
					array(
						array('key'=>'id','compare'=>'=','value'=>$user['id'])
					)
				);
				$resultHook = $this->hooks->call_hook(['tech5s_admin_change_pass',"user"=>$user,"post"=>$post,"resultUpdate"=>$resultUpdate]);
				if(!is_bool($resultHook)){
					extract($resultHook);
				}
				if($resultUpdate){
					$obj->errorCode= 200;
					$obj->message = alang("PASS_CHANGE_SUCCESS");
				}
				else{
					$obj->errorCode= 100;
					$obj->message = alang("PASS_CHANGE_FAIL");
				}
			}
			else{
				$obj->errorCode= 50;
				$obj->message = alang("WRONG_OLD_PASS");
			}
			echo json_encode($obj);
		}
		else{
			echo alang("MISSING_PARAM");
		}
	}
function deleteAll(){
	$post = $this->input->postf();
	if(@$post && isset($post['ids']) && isset($post['table'])){
		$table = $post['table'];
		$table = addslashes($table);
		if($this->checkPermisstionAccess($table,"delete")){
			$ids = explode(',', $post['ids']);
			$resultHook = $this->hooks->call_hook(['tech5s_before_delete_all',"table"=>$table,"ids"=>$ids]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			foreach ($ids as $item) {
				$this->deleteInRoutes($table,$item);
				$where = array(array('id'=>$item));
				$this->Admindao->deleteData($table,$where);
			}
			$resultHook = $this->hooks->call_hook(['tech5s_after_delete_all',"table"=>$table,"ids"=>$ids]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			$this->createSitemap(false);
			echo echoJSON(SUCCESS,alang("DELETE_SUCCESS"));
		}
	}
	else{
		echo echoJSON(ERROR,alang("MISSING_PARAM"));
	}
}
function deleteImage(){
	$post = $this->input->postf();
	if(@$post['url']){
		$url = $post['url'];
		unlink($url);
		unlink(getImageLarge($url));
	}
}
	function uploadMultiFile(){
		$post = $this->input->postf();
		if(@$post && @$post['field']){
			$field = $post['field'];
		}
		else{
			return;
		}
		$this->load->config('filemanager');
		$extimgs = $this->config->item('ext_img');
		$extvideos = $this->config->item('ext_video');
		$extfiles = $this->config->item('ext_file');
		$extmusic = $this->config->item('ext_music');
		$config['upload_path']=$this->config->item('path_uploads');
		$pf = $this->session->userdata('PROCESS_FILE');
		if(@$pf && array_key_exists('CURRENT_PATH', $pf)){
			$config['upload_path'] = $pf['CURRENT_PATH'];
		}
		$config['allowed_types'] = implode("|",$extimgs)."|".implode("|",$extvideos)."|".implode("|",$extfiles)."|".implode("|",$extmusic);
		$config['max_size'] = $this->config->item('max_size');
		$config['max_width']  = $this->config->item('max_width');
		$config['max_height']  = $this->config->item('max_height');
		$this->load->library("upload", $config);
		$heightImage = $this->Admindao->getConfigSite('height_image',200);
		$widthImage = $this->Admindao->getConfigSite('width_image',200);
		$quality = $this->Admindao->getConfigSite('quality',100);
		$images = array();
		$files = $_FILES[$field];
		foreach ($files['name'] as $key => $image) {
			$tmpName = $files['name'][$key];
			$tmpRealName = substr($tmpName, 0,strrpos($tmpName, "."));
			$ext = substr($tmpName, strrpos($tmpName, "."));
			$config['file_name'] = $this->vn_str_filter($tmpRealName).$ext;
			$_FILES[$field.'[]']['name']= $files['name'][$key];
			$_FILES[$field.'[]']['type']= $files['type'][$key];
			$_FILES[$field.'[]']['tmp_name']= $files['tmp_name'][$key];
			$_FILES[$field.'[]']['error']= $files['error'][$key];
			$_FILES[$field.'[]']['size']= $files['size'][$key];
            $this->upload->initialize($config); // load new config setting 
            if ($this->upload->do_upload($field.'[]')) { // upload file here
            	$getFileUpload = $this->upload->data();
            	$this->load->library("image_lib");
            	$config['image_library'] = 'gd2';
            	$config['source_image'] = $config['upload_path'].$getFileUpload['file_name'];
            	$config['create_thumb'] = false;
            	if(!is_dir($config['upload_path']."thumbs/")){
            		mkdir($config['upload_path']."thumbs/",0777,1);
            	}
            	$config['new_image'] = $config['upload_path']."thumbs/".$getFileUpload['file_name'];
            	if($heightImage<=0){
            		$config['maintain_ratio'] = TRUE;
            		$config['width'] = $widthImage;
            	}
            	else if($widthImage<=0){
            		$config['maintain_ratio'] = TRUE;
            		$config['height']   = $heightImage;	
            	}
            	else{
            		$config['maintain_ratio'] = FALSE;
            		$config['width'] = $widthImage;
            		$config['height']   = $heightImage;	
            	}
            	$config['quality'] = $quality;
            	$this->image_lib->initialize($config);
            	$this->image_lib->resize();
            	array_push($images,$config['new_image']);
            } else {
            	echo $this->upload->display_errors();
            	array_push($images,"FALSE");
            }
        }
        echo json_encode($images);
    }
    function do_edit(){
    	$table = $this->uri->segment(3,"");
    	$table= addslashes($table);
    	if($this->checkPermisstionAccess($table,"edit"))
    	{
    		$post = $this->input->postf();
    		if(@$post){
    			$fnc = "do_edit".$post['tech5s_type'];
    			if(method_exists($this, $fnc))
    			{
    				unset($post['tech5s_type']);
    				$resultHook = $this->hooks->call_hook(['tech5s_before_display_doedit',"table"=>$table,"post"=>$post]);
					if(!is_bool($resultHook) && is_array($resultHook)){
						extract($resultHook);
					}
    				$this->$fnc($table);
    				$this->insertHistory('Chỉnh sửa nội dung '.$table.((isset($post) && isset($post['name'])) ? " : ".$post['name']:""));
    			}
    		}
    	}
    }
    private function do_edit6($table){
    	$this->do_edit1($table);
    }
    private function do_edit4($table){
    	$post = $this->input->postf();
    	if(@$post && @$post['groupuser']&& @$post['role']){
    		$ret = $this->Admindao->deleteData($table,array(array('group_user_id'=>$post['groupuser'])));
    		if($ret){
    			$json = json_decode($post['role']);
    			foreach ($json as $key => $value) {
    				$dataInsert['group_user_id'] = $post['groupuser'];
    				$dataInsert['group_module_id'] = $value->id;
    				$dataInsert['role'] = $value->code;
    				$arrper = getAdminUser()['permission'];
    				foreach ($arrper as $itemper) {
    					if($itemper['group_module_id']!=$value->id) continue;
    					if(((int)$itemper['role'] < $value->code)|| ( (int)$itemper['role'] & $value->code ==0)){
    						$dataInsert['role'] =0;
    					}
    					$ret = $this->Admindao->insertData($dataInsert,$table);
    				}
    				if(!$ret){
    					echo echoJSON(ERROR,alang("UPDATE_FAIL"));
    					return;
    				}
    			}
    			echo echoJSON(SUCCESS,"Cập nhật thành công!");
    		}
    		else{
    			echo echoJSON(ERROR,alang("UPDATE_FAIL"));
    		}
    	}
    	else{
    		echo echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
    	}
    }
    private function do_edit3($table){
    	$post = $this->input->postf();
    	if(@$post && @$post['groupmenu']){
    		$groupmenu = (int)$post['groupmenu'];
    		$ret = $this->Admindao->deleteData($table,array(array('group_id'=>$groupmenu)));
    		if($ret){
    			$arr = json_decode($post['data'],true);
    			$ret = $this->Admindao->insertMenu($arr,$table,0,0,$groupmenu);
    			if($ret){
    				echo echoJSON(SUCCESS,"Cập nhật thành công!");
    			}
    			else{
    				echo echoJSON(ERROR,alang("UPDATE_FAIL"));
    			}
    		}
    		else{
    			echo echoJSON(ERROR,alang("UPDATE_FAIL"));
    		}
    	}
    	else{
    		echo echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
    	}
    }
    private function do_edit2($table){
    	$post = $this->input->postf();
    	foreach ($post as $key => $value) {
    		if(strpos($key, 'tech5s')!==0){
    			$dataUpdate = [];
    			$posSpect = strpos($key, "_");
    			$realKey = substr($key,$posSpect+1);
    			$prefixKey =substr($key, 0,$posSpect);
    			$dataUpdate[strtolower($prefixKey.'_value')] = $value;
    			$realKey = addslashes($realKey);
    			$where = array(array('key'=>'keyword','compare' =>'=','value'=> "'".$realKey."'"));
    			$this->Admindao->updateData($dataUpdate,$table,$where);
    		}
    	}
    	echo echoJSON(SUCCESS,alang("UPDATED"));
    }
    private function do_edit1($table){
    	$post = $this->input->postf();
    	if(@$post){
    		$dataUpload = $post;
    		$arrPK = $this->Admindao->getAllFieldInTable(
    			array(
    				array("key"=>"a.id",
    					"value"=>"b.parent",
    					"compare"=>"="),
    				array('key'=>'b.type','compare'=>'=','value'=>"'PRIMARYKEY'"),
    				array('key'=>'a.name','compare'=>"='",'value'=>$table."'")
    			)
    			," ord asc"
    		);
    		$extraUrl = $this->getConfigSite('URL_EXT','');
    		$checkExistLink = false;
    		$postid = (int)$post['id'];
    		if(@$dataUpload['slug']){
    			$count = sizeof($this->Admindao->getTagInNuyRountes($dataUpload['slug'],$postid,"",$extraUrl));
    			$countTable = $this->Admindao->getNumDataInTable("",'nuy_routes',array(
    				array('key'=>'tag_id','compare'=>'=','value'=>$postid),
    				array('key'=>'`table`','compare'=>'=','value'=>$table)
    			));
    			$checkExistLink = $countTable>0;
    			$total = 0;
    			$total +=$count;
    			$ext= $dataUpload['slug'];
    			while($count>0){
    				$ext  = $dataUpload['slug'].($count>0?"-".($total+1):"");
    				$count = sizeof($this->Admindao->getTagInNuyRountes($ext,"","",$extraUrl));
    				$total +=1;
    			}
    			$dataUpload['slug']=$ext;
    		}
    		foreach ($dataUpload as $keyu => $valueu) {
    			if(strpos($keyu, 'tech5s_') ===0){
    				unset($dataUpload[$keyu]);
    			}
    		}
    		$pivots = [];
    		foreach ($dataUpload as $keyu => $valueu) {
    			if(strpos($keyu, 'pivot_') ===0){
    				unset($dataUpload[$keyu]);
    				$pivots[$keyu] = $valueu;
    			}
    		}
    		$arrWhere = array();
    		foreach ($arrPK as $key => $value) {
    			array_push($arrWhere, array('key'=>$value['name'],'compare'=>'=','value'=>$dataUpload[$value['name']]));
    			unset($dataUpload[$value['name']]);
    		}
    		if(isset($dataUpload['update_time'])){
    			$dataUpload['update_time'] = time();
    		}
    		$resultHook = $this->hooks->call_hook(['tech5s_before_update_1',"table"=>$table,"dataUpload"=>$dataUpload,"arrWhere"=>$arrWhere]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
    		if($this->Admindao->updateData($dataUpload,$table,$arrWhere))
    		{
    			$this->_updatePivot($pivots,$postid,$table);
    			if(@$dataUpload['slug'] ){
    				$lastId = $postid;
    				if($checkExistLink){
    					$data['controller']=$post['tech5s_controller'];
    					$data['link']= $ext.$extraUrl;
    					$data['note']=@$dataUpload['name']?$dataUpload['name']:"";
    					$data['update_time'] = time();
    					$resultHook = $this->hooks->call_hook(['tech5s_after_in_update_before_update_routes',"table"=>$table,"dataUpload"=>$dataUpload,"arrWhere"=>$arrWhere,"data"=>$data]);
						if(!is_bool($resultHook)&& is_array($resultHook)){
							extract($resultHook);
						}
    					$this->Admindao->updateData($data,'nuy_routes',
    						array(
    							array('key'=>'table','compare'=>'=','value'=>"'".$table."'"),
    							array('key'=>'tag_id','compare'=>'=','value'=>"'".$lastId."'")
    						)
    					);
    				}
    				else{
    					$data= array();
    					$data['controller']=$post['tech5s_controller'];
    					$data['link']= $ext.$extraUrl;
    					$data['note']=@$dataUpload['name']?$dataUpload['name']:"";
    					$data['table']=$table;
    					$data['tag_id']=$lastId;
    					$data['update_time'] = time();
    					$data['create_time'] = time();
    					$resultHook = $this->hooks->call_hook(['tech5s_after_in_update_before_insert_routes',"table"=>$table,"dataUpload"=>$dataUpload,"arrWhere"=>$arrWhere,"data"=>$data]);
						if(!is_bool($resultHook)&& is_array($resultHook)){
							extract($resultHook);
						}
    					$this->Admindao->insertData($data,'nuy_routes');
    				}
    				$this->createSitemap(false);
    			}
    			$resultHook = $this->hooks->call_hook(['tech5s_after_update_1',"table"=>$table,"dataUpload"=>$dataUpload,"arrWhere"=>$arrWhere]);
				if(!is_bool($resultHook)){
					extract($resultHook);
				}
				echo echoJSON(SUCCESS,alang("UPDATE_SUCCESS"));
    		}
    		else{
    			echo echoJSON(ERROR,alang("UPDATE_FAIL"));
    		}
    	}
    	else{
    		echo echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
    	}
		// }
    }
    private function _updatePivot($pivots,$id,$table){
    	$resultHook = $this->hooks->call_hook(['tech5s_before_function_update_pivot',"pivots"=>$pivots,"id"=>$id,"table"=>$table]);
		if(!is_bool($resultHook) && is_array($resultHook)){
			extract($resultHook);
		}
    	foreach ($pivots as $key => $pivot) {
    		$pivot = json_decode($pivot,true);
    		$pivot = @$pivot?$pivot:[];
    		if(!array_key_exists("currentTable", $pivot)||!array_key_exists("tableParent", $pivot) || !array_key_exists("data", $pivot)) continue;
    		$realKey = str_replace("pivot_", "", $key);
    		$currentTable = $pivot["currentTable"];
    		$tableParent = $pivot["tableParent"];
    		$data = $pivot["data"];
    		$this->db->where("field_map",$realKey);
    		$this->db->where("sub_table",$table);
    		$this->db->where("parent_table",$tableParent);
    		$this->db->where("sub_id",$id);
    		$this->db->delete("pivots");
    		$ins = [];
    		$data = array_unique($data);
    		foreach ($data as $kdata => $item) {
    			$tmp = [];
    			$tmp["field_map"] = $realKey;
    			$tmp["sub_table"] = $table;
    			$tmp["parent_table"] = $tableParent;
    			$tmp["sub_id"] = $id;
    			$tmp["parent_id"] = $item;
    			array_push($ins, $tmp);
    		}
			$this->db->insert_batch('pivots', $ins); 
    	}
    	
    }
    function do_insert(){
    	$this->do_insert_from_var($this->uri->segment(3,""),$this->input->postf(),0);
    }
    private function cron_insert($table,$post){
    	$this->do_insert_from_var($table,$post,1);
    }
    private function do_insert_from_var($table,$post,$dontcheck=0,$needId = false){
		if($dontcheck||$this->checkPermisstionAccess($table,"insert") )
		{
			if(@$post){
				$extraUrl = $this->getConfigSite('URL_EXT','');
				$dataUpload = $post;
				$arrPK = $this->Admindao->getAllFieldInTable(
				array(
				    array("key"=>"a.id",
				        "value"=>"b.parent",
				        "compare"=>"="),
				    array('key'=>'b.type','compare'=>'=','value'=>"'PRIMARYKEY'"),
				    array('key'=>'a.name','compare'=>"='",'value'=>$table."'")
				    )
				," ord asc"
				);
				if(@$dataUpload['slug']){
					$total = 0;
					$count = sizeof($this->Admindao->getTagInNuyRountes($dataUpload['slug'],"","",$extraUrl));
					$total +=$count;
					$ext= $dataUpload['slug'];
					while($count>0){
						$ext  = $dataUpload['slug'].($count>0?"-".($total+1):"");
						$count = sizeof($this->Admindao->getTagInNuyRountes($ext,"","",$extraUrl));
						$total +=1;
					}
					$dataUpload['slug']=$ext;
				}
				foreach ($dataUpload as $keyu => $valueu) {
					if(strpos($keyu, 'tech5s_') ===0){
						unset($dataUpload[$keyu]);
					}
				}
				$pivots = [];
	    		foreach ($dataUpload as $keyu => $valueu) {
	    			if(strpos($keyu, 'pivot_') ===0){
	    				unset($dataUpload[$keyu]);
	    				$pivots[$keyu] = $valueu;
	    			}
	    		}
				foreach ($arrPK as $kp => $vp) {
					unset($dataUpload[$vp['name']]);
				}
				$resultHook = $this->hooks->call_hook(['tech5s_before_insert',"table"=>$table,"dataUpload"=>$dataUpload,"pivots"=>$pivots]);
				if(!is_bool($resultHook)){
					extract($resultHook);
				}
				$lastId = $this->Admindao->insertData($dataUpload,$table);
				if($lastId>0)
				{
					$this->_updatePivot($pivots,$lastId,$table);
					if(@$dataUpload['slug']){
						$data= array();
						$data['controller']=$post['tech5s_controller'];
						$data['link']= $ext.$extraUrl;
						$data['note']=@$dataUpload['name']?$dataUpload['name']:"";
						$data['table']=$table;
						$data['tag_id']=$lastId;
						$data['create_time'] = time();
						$data['update_time'] = time();
						$resultHook = $this->hooks->call_hook(['tech5s_after_insert_success_before_insert_routes',"lastId"=>$lastId,"table"=>$table,"dataUpload"=>$dataUpload,'data'=>$data]);
						if(!is_bool($resultHook)&& is_array($resultHook)){
							extract($resultHook);
						}
						$this->Admindao->insertData($data,'nuy_routes');
						$this->createSitemap(false);
					}
					$resultHook = $this->hooks->call_hook(['tech5s_after_insert_success',"lastId"=>$lastId,"table"=>$table,"dataUpload"=>$dataUpload]);
					if(!is_bool($resultHook)){
						extract($resultHook);
					}
					if($needId){
						echo $lastId;
						return;
					}
					echo echoJSON(SUCCESS,alang("INSERT_SUCCESS"));
				}
				else{
					if(!$needId){
						echo echoJSON(ERROR,alang("INSERT_FAIL"));
					}
					$resultHook = $this->hooks->call_hook(['tech5s_after_insert_fail',"lastId"=>$lastId,"table"=>$table,"dataUpload"=>$dataUpload]);
					if(!is_bool($resultHook)){
						extract($resultHook);
					}
				}
				$this->insertHistory('Thêm mới nội dung '.$table.((@$post && @$post['name']) ? " : ".$post['name']:""));
			}
			else{
				if(!$needId){
					echo echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
				}
			}
		}
	}
    function insertNuyTable(){
    	die("Tính năng này bị hủy bỏ, vui lòng sử dụng qua Plugin!");
    	if(isAdminServer()){
    		$post = $this->input->postf();
    		if(@$post){
    			$result= $this->Admindao->insertTableToSystem($post);
    			$result= $result>0?"Thêm thành công":"Xảy ra lỗi, thử lại sau";
    			$data['extra']=$result;
    			$data['content']="fnc/nuytableview";
    			$data['lsttable']=$this->Admindao->selectAllTableCanInsert();
    			$this->load->view('template',$data);
    		}
    	}
    }
    function deleteInRoutes($table,$id){
    	$resultHook = $this->hooks->call_hook(['tech5s_delete_routes',"table"=>$table,"id"=>$id]);
		if(!is_bool($resultHook)){
			extract($resultHook);
		}
    	$arrTable = $this->Admindao->getAllFieldInTable(
    		array(
    			array("key"=>"a.id",
    				"value"=>"b.parent",
    				"compare"=>"="),
    			array('key'=>" (b.name ='slug' or b.name='id')",'compare'=>'','value'=>''),
    			array('key'=>'a.name','compare'=>"='",'value'=>$table."'")
    		)
    	);
    	if(sizeof($arrTable)==2) {
    		$awr =array(array('tag_id'=>$id),array('table'=>$table));
    		$ret = $this->Admindao->deleteData('nuy_routes',$awr);
    	}
    }
    function delete(){
    	$table = $this->uri->segment(3,"");
    	$table= addslashes($table);
    	if($this->checkPermisstionAccess($table,"delete"))
    	{
    		if(@$this->input->postf()){
    			$post = $this->input->postf();
    			if(@$post['where']){
    				$datawhere = json_decode($post['where'],true);
    				$resultHook = $this->hooks->call_hook(['tech5s_before_delete',"table"=>$table,"datawhere"=>$datawhere]);
					if(!is_bool($resultHook)){
						extract($resultHook);
					}
    				$this->deleteInRoutes($table,@$datawhere[0]['id']?(int)$datawhere[0]['id']:0);
					$this->createSitemap(false);
					$result = $this->Admindao->deleteData($table,$datawhere);
					$resultHook = $this->hooks->call_hook(['tech5s_after_delete',"table"=>$table,"datawhere"=>$datawhere,"result"=>$result]);
					if(!is_bool($resultHook)){
						extract($resultHook);
					}
					if($result){
						echoJSON(SUCCESS,alang("DELETE_SUCCESS"));
					}
					else{
						echoJSON(ERROR,alang("DELETE_FAIL"));
					}
				}
				else{
					echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
				}
				$this->insertHistory('Xóa nội dung '.$table.((@$post && @$post['name']) ? " : ".$post['name']:""));
			}
			else{
				echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
			}
		}
	}
	function updateOneField(){
		$table = $this->uri->segment(3,"");
		$table= addslashes($table);
		if($this->checkPermisstionAccess($table,"edit")){
			$post = $this->input->postf();
			if(@$post){
				if(@$post['where']){
					$datawhere = json_decode($post['where'],true);
					$data[$post['name']] = $post['newValue'];
					$resultHook = $this->hooks->call_hook(['tech5s_before_quickupdate',"table"=>$table,"datawhere"=>$datawhere,"data"=>$data]);
					if(!is_bool($resultHook)){
						extract($resultHook);
					}
					$result = $this->Admindao->updateOneField($data,$table,$datawhere);
					$resultHook = $this->hooks->call_hook(['tech5s_after_quickupdate',"table"=>$table,"datawhere"=>$datawhere,"data"=>$data,"result"=>$result]);
					if(!is_bool($resultHook)){
						extract($resultHook);
					}
					if($result){
						echoJSON(SUCCESS,alang("UPDATE_SUCCESS"));
					}
					else{
						echoJSON(ERROR,alang("UPDATE_FAIL"));
					}
				}
				else{
					echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
				}
				$this->insertHistory('Cập nhật nhanh '.$table.((@$post && @$post['name']) ? " : ".$post['name']:"")."-".$post['newValue']."-".$post['where']);
			}
			else{
				echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
			}
		}
	}
	function getRole(){
		$post = $this->input->postf();
		if(@$post && isset($post['groupuser'])){
			$groupuser = (int)$post['groupuser'];
			$roles =  $this->Admindao->getDataInTable("",'nuy_role', array(array('key'=>'group_user_id','compare'=>'=','value'=>$groupuser)),"","", "");
			echo json_encode($roles);
		}
		else{
			echoJSON(ERROR,alang("UPDATE_FAIL"));
		}
	}
	function editRobot(){
		$this->testLoginAdmin();
		$fileNameRobot = "robots.txt";
		$post = $this->input->postf();
		if(@$post && @$post['contentdata']){
			$resultHook = $this->hooks->call_hook(['tech5s_write_robot',"post"=>$post,"fileNameRobot"=>$fileNameRobot]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			$myfile = fopen($fileNameRobot, "w+") or die("Unable to open file!");
			$contentdata = $post['contentdata'];
			fwrite($myfile, $contentdata);
			fclose($myfile);
			echoJSON(SUCCESS,alang("UPDATED"));
			$this->insertHistory('Cập nhật robots.txt ');
		}
		else{
			$resultHook = $this->hooks->call_hook(['tech5s_read_robot',"fileNameRobot"=>$fileNameRobot]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			$myfile = fopen($fileNameRobot, "a+") or die("Unable to open file!");
			$size = filesize($fileNameRobot);
			$size = $size>0?$size:1;
			$contentdata =  fread($myfile,$size);
			fclose($myfile);
			$data['content'] = 'other/robot';
			$data['contentdata'] = $contentdata;
			$this->load->view('template',$data);
		}
	}
	function editHtaccess(){
		$this->testLoginAdmin();
		$fileHtaccess = ".htaccess";
		$post = $this->input->postf();
		if(@$post && @$post['contentdata']){
			$resultHook = $this->hooks->call_hook(['tech5s_write_htaccess',"post"=>$post,"fileHtaccess"=>$fileHtaccess]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			$myfile = fopen($fileHtaccess, "w+") or die("Unable to open file!");
			$contentdata = $post['contentdata'];
			fwrite($myfile, $contentdata);
			fclose($myfile);
			echoJSON(SUCCESS,alang("UPDATED"));
			$this->insertHistory('Cập nhật htaccess ');
		}
		else{
			$resultHook = $this->hooks->call_hook(['tech5s_read_htaccess',"fileHtaccess"=>$fileHtaccess]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			$myfile = fopen($fileHtaccess, "a+") or die("Unable to open file!");
			$size = filesize($fileHtaccess);
			$size = $size>0?$size:1;
			$contentdata =  fread($myfile,$size);
			fclose($myfile);
			$data['content'] = 'other/htaccess';
			$data['contentdata'] = $contentdata;
			$this->load->view('template',$data);
		}
	}
	function viewSitemap(){
		$this->testLoginAdmin();
		$resultHook = $this->hooks->call_hook(['tech5s_view_sitemap']);
		if(!is_bool($resultHook)&&is_array($resultHook)){
			extract($resultHook);
		}
		$myfile = fopen("sitemap.xml", "a+") or die("Unable to open file!");
		$size = filesize("sitemap.xml");
		$size = $size>0?$size:1;
		$contentdata =  fread($myfile,$size);
		fclose($myfile);
		$data['content'] = 'other/sitemap';
		$data['contentdata'] = $contentdata;
		$this->load->view('template',$data);
	}
	function createSitemap($def = true){
		$this->testLoginAdmin();
		$continue = true;
		$resultHook = $this->hooks->call_hook(['tech5s_pre_create_sitemap','def'=>$def,'continue'=>$continue]);
		if(!is_bool($resultHook)){
			extract($resultHook);
		}
		if(!$continue) return;
		$routes = $this->Admindao->getDataInTable("",'nuy_routes', "","","", "");
		$resultHook = $this->hooks->call_hook(['tech5s_create_sitemap',"routes"=>$routes]);
		if(!is_bool($resultHook)){
			extract($resultHook);
		}
		$sitemap = new Sitemap(base_url());   
		$sitemap->setPath('');
		$sitemap->setFilename('sitemap');
		foreach ($routes as $post) {
		    $sitemap->addItem($post['link'], '0.6', 'weekly',(int)$post['create_time'] );
		}
		$sitemap->createSitemapIndex(base_url(), 'Today');
		if($def){
			echoJSON(SUCCESS,"Khỏi tạo thành công!");
			$this->insertHistory('Cập nhật Sitemap');			
		}
	}
	function quickpost(){
		$table = $this->uri->segment(3,"");
		$table= addslashes($table);
		if($this->checkPermisstionAccess($table,"insert")){
			if(@$_FILES){
				echo $this->uploadImage('file');
			}
			else{
				echo "";
			}
		}
	}
	function doQuickPost(){
		$table = $this->uri->segment(3,"");
		$table= addslashes($table);
		if($this->checkPermisstionAccess($table,"insert")){
			$post = $this->input->postf();
			if(@$post && !isNull($table)){
				$arr = json_decode($post['data'],true);
				$extraUrl = $this->getConfigSite('URL_EXT','');
				$arrPK = $this->Admindao->getAllFieldInTable(
					array(
					    array("key"=>"a.id",
					        "value"=>"b.parent",
					        "compare"=>"="),
					    array('key'=>'b.type','compare'=>'=','value'=>"'PRIMARYKEY'"),
					    array('key'=>'a.name','compare'=>"='",'value'=>$table."'")
					    )
					," ord asc"
					);
				foreach ($arr as $key => $value) {
					$dataUpload = $value;
					if(@$dataUpload['slug']){
						$arRoutes = $this->Admindao->getTagInNuyRountes($dataUpload['slug'],"","",$extraUrl);
						$count = sizeof($arRoutes);
						$ext  = $dataUpload['slug'].($count>0?"-".($count+1):"");
						$dataUpload['slug']=$ext;
					}
					foreach ($dataUpload as $keyu => $valueu) {
						if(strpos($keyu, 'tech5s_') ===0){
							unset($dataUpload[$keyu]);
						}
					}
					foreach ($arrPK as $kp => $vp) {
						unset($dataUpload[$vp['name']]);
					}
					$lastId = $this->Admindao->insertData($dataUpload,$table);
					if($lastId>0)
					{
						if(@$dataUpload['slug']){
							$data= array();
							$data['controller']=$value['tech5s_controller'];
							$data['link']= $ext.$extraUrl;
							$data['note']=@$dataUpload['name']?$dataUpload['name']:"";
							$data['table']=$table;
							$data['tag_id']=$lastId;
							$data['create_time'] = time();
							$data['update_time'] = time();
							$this->Admindao->insertData($data,'nuy_routes');
						}
					}
				}
				echoJSON(SUCCESS,"Đã thực hiện!");
				$this->insertHistory('Đăng nhanh'.$table." - ".count($arr)." bản ghi.");
			}
			else{
				echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
			}
		}
	}
	function forgetPass(){
    	$post = $this->input->post();
    	if(@$post){
    		if(@$post['username'] && @$post['email']){
    			$username = addslashes($post['username']);
    			$email = addslashes($post['email']);
    			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    				$this->session->set_flashdata('error', 'Email không hợp lệ');
			    	redirect(base_url('Techsystem/forgetPass'));
			    }
    			$arr_user = $this->Dindex->getDataDetail(array(
			        'table'=>'nuy_user',
			        'where'=>array(array('key'=>'username','compare'=>'=','value'=>'"'.$username.'"'), array('key'=>'email','compare'=>'=','value'=>'"'.$email.'"')),
			        'limit'=>'0,1'
			    ));
			    if(count($arr_user) > 0){
			    	$resultHook = $this->hooks->call_hook(['tech5s_before_forget_password',"arr_user"=>$arr_user,"username"=>$username,"email"=>$email]);
					if(!is_bool($resultHook)){
						extract($resultHook);
					}
			    	$new_password_root = rand_string(10);
			    	$new_password_encode = $this->bcrypt->hash_password($new_password_root);
			    	if(sendMail($email, 'Yêu cầu cấp lại mật khẩu truy cập quản trị website: '.base_url(), 'Mật khẩu truy cập mới của bạn là: '.$new_password_root)){
			    		$this->Dindex->updateDataFull('nuy_user',array('password' => $new_password_encode),array('username'=>$username, 'email' => $email));
			    		$resultHook = $this->hooks->call_hook(['tech5s_after_forget_password',"new_password_root"=>$new_password_root,"username"=>$username,"email"=>$email]);
						if(!is_bool($resultHook)){
							extract($resultHook);
						}
			    		$this->session->set_flashdata('success_change_pass', 'Một email chứa mật khẩu mới đã được gửi về '.$email.'. Vui lòng vào hòm thư(kiểm tra cả mục spam) của bạn để lấy mật khẩu mới.');
			    		redirect(base_url('Techsystem/login'));
			    	}
			    	else{
			    		$this->session->set_flashdata('error', 'Email gửi hoặc email nhận không hợp lệ.');
			    		redirect(base_url('Techsystem/forgetPass'));
			    	}
			    }
			    else{
			    	$this->session->set_flashdata('error', 'Không tồn tại tài khoản với email này trong hệ thống');
			    	redirect(base_url('Techsystem/forgetPass'));
			    }
    		}
    		else{
    			$this->session->set_flashdata('error', 'Vui lòng điền đẩy đủ thông tin!');
    			redirect(base_url('Techsystem/forgetPass'));
    		}
    	}
    	$this->load->view('forget');
    }
	function media1(){
		if($this->checkPermisstionAccess('medias','view')){
			$data['content'] = 'other/media';
			$this->load->view('template',$data);
		}
	}
	function getPasswordEncrypt(){
		$this->testLoginAdmin();
		$post = $this->input->postf();
		if(@$post && isset($post['pass'])){
			$pass = $this->bcrypt->hash_password($post['pass']);
			echoJSON(SUCCESS,$pass);
		}
		else{
			echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
		}
	}
	function readFile(){
		$post = $this->input->postf();
		if($this->checkPermisstionAccess('editcode','view')){
			if(@$post){
				$path = FCPATH.$post['filename'];
				if(strpos($path, "./")!==FALSE || strpos($path, "../")!==FALSE){
					echoJSON(ERROR,"Lỗi đọc file!");
					return;
				}
				if(is_file($path)){
					$myfile = fopen($path, "r") or die("Unable to open file!");
					$size = filesize($path);
					$size = $size>0?$size:1;
					$contentdata =  fread($myfile,$size);
					fclose($myfile);
					$obj = new stdClass();
					$obj->message = "Đã đọc file";
					$obj->content = $contentdata;
					$obj->code=SUCCESS;
					$this->session->set_userdata('tmpfileedit',$path);
					echo json_encode($obj);
				}
				else{
					echoJSON(ERROR,"Lỗi đọc file!");
				}
			}
			else{
				echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
			}
		}
		else{
		}
	}
	function updateFileCode(){
		$tmp = $this->session->userdata('tmpfileedit');
		$post = $_POST;
		if($this->checkPermisstionAccess('editcode','edit')){
		if(@$tmp){
			$myfile = fopen($tmp, "w+") or die("Unable to open file!");
			if(@$post && @$post['contentdata']){
				$contentdata = $post['contentdata'];
				fwrite($myfile, $contentdata);
				fclose($myfile);
				echoJSON(SUCCESS,alang("UPDATED"));
				$this->insertHistory('Chỉnh sửa file code '.$tmp);
			}
			else{
				echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
			}
		}
		else{
			echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
		}
	}
	}
	function getAllDir($path_uploads){
		$lstFolders = scandir($path_uploads,1);
		$arrFolder = array();
		foreach ($lstFolders as $key => $value) {
			if($value=='.'||$value=='..') continue;
			if(is_dir($path_uploads.$value)){
				$obj = new stdClass();
				$obj->name = $value;
				$obj->childs = $this->getAllDir($path_uploads.$value."/");
				array_push($arrFolder, $obj);
			}
		}
		return $arrFolder;
	}
    private function save_image($inPath,$outPath)
    { 
    	$opts=array(
    		"ssl"=>array(
    			"verify_peer"=>false,
    			"verify_peer_name"=>false,
    		),
    	);  
    	$in = fopen($inPath, 'r', false, stream_context_create($opts));
    	$out=   fopen($outPath, "wb");
    	while ($chunk = fread($in,8192))
    	{
    		fwrite($out, $chunk, 8192);
    	}
    	fclose($in);
    	fclose($out);
    }
    function deleteFileCode(){
    	$post = $this->input->postf();
    	if(@$post && isset($post['name'])){
    		if($post['name']=='styles.min.css'||$post['name']=='scripts.min.js'){
    			if(file_exists("theme/frontend/".$post['name'])){
    				unlink(FCPATH."theme/frontend/".$post['name']);
    				$this->insertHistory('Xóa file code '.$post['name']);
    			}
    		}
    		echoJSON(SUCCESS,alang("UPDATED"));
    	}
    	else{
    		echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
    	}
    }
    function historyAccess(){
    	$this->testLoginAdmin();
    	$table = "nuy_history";
    	$resultHook = $this->hooks->call_hook(['tech5s_history_access',"table"=>$table]);
		if(!is_bool($resultHook)){
			extract($resultHook);
		}
    	$offset = $this->uri->segment(3,"0");
    	$rpp = 20;
    	$config['base_url']=base_url()."Techsystem/historyAccess/";
    	$config['per_page']=$rpp;
    	$config['total_rows']=$this->Admindao->getNumDataInTable("",$table,"");
    	$config['uri_segment']=3;
    	$this->pagination->initialize($config);
    	$data['lstData']=$this->Admindao->getDataInTable("",$table,"",$rpp,$offset);
    	$data['total_rows']= $config['total_rows'];
    	$data['content'] = 'nuy/viewhistory';
    	$this->load->view('template',$data);
    }
    function insertHistory($msg){
    	$dataInsert['note'] = $msg;
    	$dataInsert['create_time'] = time();
    	$user = getAdminUser();
    	$dataInsert['name'] = @$user['user']['username']?$user['user']['username']:"unknow";
    	$dataInsert['ip']=$this->input->ip_address();
    	$table = "nuy_history";
    	$resultHook = $this->hooks->call_hook(['tech5s_insert_history_access',"table"=>$table,"dataInsert"=>$dataInsert]);
		if(!is_bool($resultHook)){
			extract($resultHook);
		}
    	$this->Admindao->insertData($dataInsert,$table);
    }
    function preview(){
    	$post= $this->input->postf();
    	$table = addslashes($post['table']);
    	$arrData= array(0=>$post);
    	$arrTable = $this->Dindex->getData('nuy_table',array('map_table'=>$table),0,1);
    	if(sizeof($arrTable)<=0) return;
    	if(sizeof($arrData)>0){
    		if(array_key_exists('count', $arrData[0])){
    			$this->Dindex->updateData($post['table'],array('count'=>'count+1'),array('id'=>$arrData[0]['id']));	
    		}
    		$data['dataitem']=sizeof($arrData)>0?$arrData[0]:"";
    		$itemTable = $arrTable[0];
    		$data['datatable']=$itemTable;
				// $data['content'] = "view/pro";
    		$this->load->view('pro/view',$data);
    	}
    }
    function deleteSuperCache($slug = false){
    	$path = "application/cache/super/";
    	$resultHook = $this->hooks->call_hook(['tech5s_delete_super_cache',"path"=>$path,"slug"=>$slug]);
		if(!is_bool($resultHook)){
			extract($resultHook);
		}
    	if($slug){
    		if(file_exists($path.md5($slug))){
				unlink($path.md5($slug));
			}
			$caches = scandir($path);
			foreach ($caches as $key => $value) {
	    		if($value != '.' && $value != '..' && strlen($value) > 32){
	    			if(file_exists($path.$value)){
	    				unlink($path.$value);
	    			}
	    		}
	    	}
    	}
    	else{
    		$caches = scandir($path);
	    	foreach ($caches as $key => $value) {
	    		if($value != '.' && $value != '..'){
	    			if(file_exists($path.$value)){
	    				unlink($path.$value);
	    			}
	    		}
	    	}
    	}
    	$file = md5('/');
    	if(file_exists($path.$file)){
			unlink($path.$file);
		}
    }
    function deleteCache(){
    	$post = $this->input->postf();
    	if(@$post){
    		$resultHook = $this->hooks->call_hook(['tech5s_delete_cache',"post"=>$post]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			$this->db->cache_delete_all();
			$this->cache->clean();
			if($this->cache->memcached->is_supported()){
				$this->cache->memcached->clean();
			}
			array_map('unlink', glob(FCPATH."application/cache/_cache_*"));
    		if(file_exists("application/cache/website_setting")){
    			unlink(FCPATH."application/cache/website_setting");
    			$this->insertHistory('Xóa file cache '."application/cache/website_setting");
    		}
    		if(file_exists("application/cache/website_language")){
    			unlink(FCPATH."application/cache/website_language");
    			$this->insertHistory('Xóa file cache '."application/cache/website_language");
    		}
    		if(file_exists("application/cache/website_admin_language")){
    			unlink(FCPATH."application/cache/website_admin_language");
    			$this->insertHistory('Xóa file cache '."application/cache/website_admin_language");
    		}
    		echoJSON(SUCCESS,alang("UPDATED"));
    	}
    	else{
    		echoJSON(MISSING_PARAM,alang("MISSING_PARAM"));
    	}
    }
    function configcron(){
    	$data['content'] = 'cron';
    	$this->load->view('index',$data);
    }
    private function getDetailOneItem($arrLinks,$jsonItem,$countitem,$cron,$basehtml){
    	$object = $jsonItem->object;
    	$jsonContent = $object->content;
    	$jsonRemove = $jsonContent->remove;
    	$currentCount =0;
    	$ret = array();
    	while ($currentCount<$countitem) {
    		foreach ($arrLinks as $link) {
    			if($currentCount >=$countitem){
    				return $ret;	
    			}
    			$html = file_get_html($link);
    			$name = trim($html->find($object->name,0)->innertext);
    			$arr = $this->Admindao->getDataInTable("*",$jsonItem->table,array(array('key'=>"from_cron",'compare'=>'=','value'=>"1"),array('key'=>"title_cron",'compare'=>'=','value'=>strtolower(trim($name)))), 1,0, " order by id desc ");
    			if(count($arr)>0){
    				return $ret;
    			}
    			$short_content ="";
    			if(!isNull($object->short_content)){
    				$short_content = $html->find($object->short_content,0);
    				if($short_content!=null){
    					$short_content = $short_content->innertext;
    				}
    			}
    			$content = $html->find($jsonContent->main,0);
    			foreach ($jsonRemove as $rmv) {
    				$itemRmvs = $content -> find($rmv);
    				foreach ($itemRmvs as $itemRmv) {
    					if(@$itemRmv && $itemRmv ->outertext!=''){
    						$itemRmv->outertext='';	
    					}
    				}
    			}
    			if(isNull($content)){
    				echo "Lỗi lấy nội dung tin tức!";
    				return;
    			}
    			else{
    				$content = $content ->innertext;	
    			}
    			echo $name."<br>";
    			$currentCount++;
    			$tmp = array('name'=>$name,'short_content'=>$short_content,'content'=>$content);
    			array_push($ret, $tmp);
    		}
    		$jsonPagi = $jsonItem->pagination;
    		$pagi = $basehtml->find($jsonPagi->main,0);
    		$paginext = $pagi->find($jsonPagi->active,0);
    		if($paginext!=null && (strtolower(trim($paginext->tag))=="a"|| strtolower(trim($paginext->tag))=="li"|| strtolower(trim($paginext->tag))=="span")){
    			$parent = $paginext->parent();
    			if($parent !=null && strtolower(trim($parent->tag))=="li"){
    				$paginext = $parent->next_sibling()->find('a');
    			}
    			else{
    				$paginext =$paginext->next_sibling();
    			}
    		}
    		else{
    			$paginext = $pagi->find($jsonPagi->next,0);
    			if(strtolower(trim($paginext->tag))!="a")
    			{
    				$paginext= $paginext->find('a');
    			}
    		}
    		if($paginext==null || !$paginext->hasAttribute('href')){
    			$currentCount += $countitem;
    		}
    		else{
    			$hrefnext = $paginext->getAttribute('href');
    			$pos = strpos($link, 'http');
    			$hrefnext = $pos===FALSE?$cron['base_url'].$hrefnext:$hrefnext;
    			$html = file_get_html($hrefnext);
    			$basehtml = $html;
    			$arrLinks = $this->getListLink($hrefnext,$cron['base_url'],$jsonItem,$html);
    		}
    	}
    	return $ret;
    }
    private function getListLink($link,$base,$jsonItem,$html = null){
    	if($html==null){
    		$html = file_get_html($link);
    	}
    	$lists = $html->find($jsonItem->list); 
    	$arrLinks = array();
    	foreach ($lists as $item) {
    		$link = $item->find($jsonItem->link,0)->getAttribute('href');
    		$pos = strpos($link, 'http');
    		$link = $pos===FALSE?$base.$link:$link;
    		array_push($arrLinks, $link);
    	}
    	return $arrLinks;
    }
    private function loopDataCron($cron,$data,$countitem){
    	$ret = array();
    	foreach ($data as $key => $jsonItem) {
    		$table = $jsonItem->table;
    		$html = file_get_html($cron['link']);
    		$arrLinks = $this->getListLink($cron['link'],$cron['base_url'],$jsonItem,$html);
    		$tmp = $this->getDetailOneItem($arrLinks,$jsonItem,$countitem,$cron,$html);
    		array_push($ret, $tmp);
    	}
    	return $ret;
    }
    function runCron($countitem=1){
    	if(@$_SERVER['HTTP_HOST']){
    		echo "This function is stopping...";
    		return;
    	}
    	$enable = $this->Admindao->getDataInTable("","nuy_config", array(array('key'=>"name",'compare'=>'=','value'=>"ENABLE_CRON")),"","", "");
    	if((count($enable)>0 && $enable[0]['value']==0) || count($enable)==0){
    		echo "CRON is disable in Admin panel!<br>";
    		return;
    	}
    	$isrunning = $this->Admindao->getDataInTable("","nuy_config", array(array('key'=>"name",'compare'=>'=','value'=>"IS_CRON_RUNNING")),"","", "");
    	if((count($isrunning)>0 && $isrunning[0]['value']==1) || count($isrunning)==0){
    		echo "CRON is running! Please wait!<br>";
    		return;
    	}
    	$ret = $this->Admindao->updateData(array('value'=>'1'),'nuy_config',array(array('key'=>'name','compare'=>'=','value'=>"'IS_CRON_RUNNING'")));
    	$numberrecord = $this->Admindao->getDataInTable("","nuy_config", array(array('key'=>"name",'compare'=>'=','value'=>"NUMBER_RECORD_CRON")),"","", "");
    	$countitem = count($numberrecord)>0?$numberrecord[0]['value']:5;
    	echo "CRON is starting...";
    	$listCrons = $this->Admindao->getDataInTable("","crontabs", array(array('key'=>"act",'compare'=>'=','value'=>"1")),"","", "");
    	foreach ($listCrons as $kc => $cron) {
    		if($cron['parent']==0) continue;
    		$data = json_decode($cron['xpath']);
    		if(json_last_error()==JSON_ERROR_NONE){
    			$tmp =  $this->loopDataCron($cron,$data->data,$countitem);
    			$default =  json_decode($cron['default_data'],true);
    			foreach ($tmp as $tmplv1) {
    				foreach ($tmplv1 as $tmplv2) {
    					$tmp['from_name_cron'] = $cron['link'];
    					$tmp = array_merge($tmplv2,$default['default']);
    					$this->testCron($cron['base_url'],$tmp);
    				}
    			}
    		}
    		else{
    			echo "Some configs is wrong...<br>";
    		}
    	}
    	echo "Finishing... CRON<br>";
    	$this->Admindao->updateData(array('value'=>'0'),'nuy_config',array(array('key'=>'name','compare'=>'=','value'=>"'IS_CRON_RUNNING'")));
    }
    private function testCron($base,$lv3){
    	$lv3['slug'] = replaceURL($lv3['name']);
    	$lv3['from_cron'] = 1;
    	$lv3['title_cron'] =strtolower(trim($lv3['name']));
    	$lv3['s_title'] = $lv3['name'];
    	$lv3['create_time'] =time();
    	$lv3['update_time'] = time();
    	$lv3['tech5s_controller'] = 'news/view.php';
    	$content = $this->getImageFromContent($base,$lv3['content']);
    	$lv3['content'] = $content['content'];
    	$lv3['img'] = $content['img'];
    	$this->cron_insert('news',$lv3);	
    }
    private function getImageFromContent($base,$contentdata){
    	$html = str_get_html($contentdata);
    	$imgs = $html->find('img');
    	$i =0;
    	$imgThumb = "";
    	foreach ($imgs as $key => $value) {
    		$file =  $value->getAttribute('src');
    		if(isNull($file))continue;
    		$ext = substr($file, strrpos($file, '.'));
    		$name = microtime();
    		$name = str_replace(" ", "", $name);
    		$name = str_replace(".", "", $name);
    		$file = strpos($file, 'http')===FALSE?$base.$file:$file;
    		$file= str_replace(" ", "%20", $file);
    		$this->save_image($file,'uploads/'.$name.$ext);
    		if($i==0 && file_exists('uploads/'.$name.$ext)){
    			$heightImage = $this->Admindao->getConfigSite('height_image',200);
    			$widthImage = $this->Admindao->getConfigSite('width_image',200);
    			$this->load->library("image_lib");
    			$config['image_library'] = 'gd2';
    			$config['source_image'] = 'uploads/'.$name.$ext;
    			$config['create_thumb'] = false;
    			$config['new_image'] = 'uploads/'."thumbs/".$name.$ext;
    			if($heightImage<=0){
    				$config['maintain_ratio'] = TRUE;
    				$config['width'] = $widthImage;
    			}
    			else if($widthImage<=0){
    				$config['maintain_ratio'] = TRUE;
    				$config['height']   = $heightImage;	
    			}
    			else{
    				$config['maintain_ratio'] = FALSE;
    				$config['width'] = $widthImage;
    				$config['height']   = $heightImage;	
    			}
    			$this->image_lib->initialize($config);
    			$this->image_lib->resize();
    			$imgThumb = $config['source_image'];
    		}
    		$i++;
    		$value->setAttribute('src','uploads/'.$name.$ext);
    		$value->setAttribute('rel','uploads/'.$name.$ext);
    	}
    	$as = $html->find('a');
    	foreach ($as as $key => $a) {
    		$a->setAttribute('href','');
    	}
    	return array('content'=>$html->outertext,'img'=>$imgThumb);
    }
    function setOrderingFile(){
    	$order = $this->uri->segment(3,"filedown");
    	$this->session->set_userdata('ORDERING_FILE',$order);
    	redirect('Techsystem/mediaManager');
    }
    public static function sortByTime($a,$b)
    {
    	$c1= strtotime($a->created_time);
    	$c2= strtotime($b->created_time);
    	return $c2 -$c1;
    }
    public function checkPhoneNumber($input_lines){
    	$input_lines = preg_replace('/\s+/', '', $input_lines);
    	preg_match_all("/((09|012|016)\d{8}|(088|089)\d{7})/", $input_lines, $output_array);
    	return $output_array[0];
    }
    public function synccommentsfb(){
    }
    function export(){
    	
    }
    public function ajx_select2_search(){
    	$this->testLoginAdmin();
    	$post = $this->input->postf();
    	$resultHook = $this->hooks->call_hook(['tech5s_select2_search',"post"=>$post]);
		if(!is_bool($resultHook)){
			extract($resultHook);
		}
    	if(isset($post["source"])){
    		if($post["source"]=="static"){
				// Không làm gì cả
    		}
    		else if($post["source"]=="database"){
    			$values = json_decode($post["settings"],true);
    			$input = array_key_exists('select', $values) ?$values['select']:"";
    			$table = array_key_exists('table', $values) ?$values['table']:"";
    			$where = array(array("key"=>" name ","compare"=>" like ","value"=>"%".addslashes($post['q'])."%"));
    			$arr = $this->Admindao->getDataInTable($input,$table, $where,"","", "order by name asc");
    			$obj = new stdClass();
    			$obj->items = $arr;
    			echo json_encode($obj);
    		}
    	}
    }
    public function ajx_select2_multi_search(){
    	$this->testLoginAdmin();
    	$post = $this->input->postf();
    	$resultHook = $this->hooks->call_hook(['tech5s_select2_multi_search',"post"=>$post]);
		if(!is_bool($resultHook)){
			extract($resultHook);
		}
    	if(isset($post["source"])){
    		if($post["source"]=="static"){
				// Không làm gì cả
    		}
    		else if($post["source"]=="database"){
    			$values = json_decode($post["settings"],true);
    			$input = array_key_exists('select', $values) ?$values['select']:"";
    			$table = array_key_exists('table', $values) ?$values['table']:"";
    			$where = array(array("key"=>" name ","compare"=>" like ","value"=>"%".addslashes($post['q'])."%"));
    			$arr = $this->Admindao->getDataInTable($input,$table, $where,"","", "order by name asc");
    			$obj = new stdClass();
    			$obj->items = $arr;
    			echo json_encode($obj);
    		}
    	}
    }
	function insertTag(){
		$this->testLoginAdmin();
		$post = $this->input->postf();
		if(@$post && isset($post['name']) && $post['name'] != ''){
			$data['name'] = $post['name'];
			$data['content'] = $post['name'];
			$data['link'] = replaceURL($post['name']);
			$data['create_time'] = time();
			echo $this->do_insert_from_var($this->uri->segment(3,""),$data,1,true);	
		}
	}
	//Hàm mở rộng tính năng cho quản trị
	function extra(){
		$this->testLoginAdmin();
		$get = $this->input->get();
		$param = isset($get["action"])?$get["action"]:"";
		if($param=="") return;
		if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $param)) {
			return ;
	    } 
		$param = base64_decode($param);
		parse_str($param, $actions); 
		$table = isset($actions["table"])?$actions["table"]:"";
		$act = isset($actions["action"])?$actions["action"]:"";
		$code = isset($actions["code"])?$actions["code"]:"";
		if($act == "" || $table == "") return;
		if($this->checkPermisstionAccess($table,$act))
    	{
    		$resultHook = $this->hooks->call_hook(['tech5s_extra_function',"table"=>$table,"act"=>$act,"code"=>$code]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
    	}
	}
	
	public function changeLanguage(){
		$this->testLoginAdmin();
		$lang = $this->uri->segment(3,"");
		setAdminLanguage($lang);
		$get = $this->input->get();
		if(array_key_exists("return", $get)){
			redirect($get["return"]);
		}
        else{
        	redirect('Techsystem/index');
        }
	}
}
?>