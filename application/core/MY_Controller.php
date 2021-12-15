<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include('Macroable.php');
class MY_Controller extends CI_Controller{
	use Macroable;
	protected $configGlobalSite;
    public function __construct(){
        parent::__construct();
        $firewall_enable = $this->config->item( 'firewall_enable' );
        if($firewall_enable){
        	$this->load->library(array('Firewall'));
	        $this->firewall->open();
        }
        $check_setting_enable = $this->config->item( 'check_setting_enable' );
        if($check_setting_enable){
        	$this->checkSetting();
        }
        $update_online_enable = $this->config->item( 'update_online_enable' );
        if($update_online_enable){
        	$this->updateOnline();
        }
        $multi_language = $this->config->item( 'multi_language' );
        if($multi_language){
        	$this->loadLanguage();
        }
        if(function_exists("adminIsLogged") && adminIsLogged()){
        	$this->db->cache_delete();
        }
    }
    public function __call($method, $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }
        $macro = static::$macros[$method];
        if ($macro instanceof Closure) {
            return call_user_func_array($macro->bindTo($this, static::class), $parameters);
        }
        return call_user_func_array($macro, $parameters);
    }
    public function checkSetting(){
    	if(!@$this->session->userdata('super_setting')){
    		$server_output=  $this->curlData("https://tech5s.com.vn/demo/analytics/Welcome/getSetting",'site='.$_SERVER['SERVER_NAME']);
    		$this->session->set_userdata('super_setting',$server_output);
    	}
    	$ret = $this->session->userdata('super_setting');
		$obj = json_decode($ret,true);
		if($obj["errorCode"]==200){
			$arr =$obj["data"];
			foreach ($arr as $item) {
				$item['key']='SITE_DEAD';
				if($item['value']!=""){
					redirect($item['value']);
				}
			}
			
		}
    }
   
    public function baseview($tag,...$params){
    	$loadBaseView = true;
    	$resultHook = $this->hooks->call_hook(['tech5s_before_baseview','tag'=>$tag,'loadBaseView'=>$loadBaseView,'params'=>$params]);
        if(is_array($resultHook)){
    	    extract($resultHook);
        }
        if($loadBaseView){
        	if(count($params)==0){
        		$this->baseviewp($tag,false);
        	}
        	else if(count($params)==1){
        		$lp = $params[0];
        		if(is_numeric($lp)){
        			$this->baseviewp($tag,$lp);
        		}
        		else{
        			$this->catch404();
        		}
        	}
        	else{
        		$this->catch404();
        	}
			
        }
    }
	public function baseviewp($tag,$pp){
		$tag = rtrim($tag,"/");
		$arrRoutes= $this->Dindex->getData('nuy_routes',array('link'=>$tag),0,1);
		if(count($arrRoutes)>0 && @$arrRoutes[0]['controller'] && $arrRoutes[0]['is_static'] !=1){
			$itemRoutes = $arrRoutes[0];
			$arrData= $this->Dindex->getData($itemRoutes['table'],array('id'=>$itemRoutes['tag_id']),0,1);
			$arrTable = $this->Dindex->getData('nuy_table',array('map_table'=>$itemRoutes['table']),0,1);
			if(count($arrTable)<=0) $this->catch404(true);
			if(count($arrData)<=0) $this->catch404(true);
			$dataitem = $arrData[0];
			$itemTable = $arrTable[0];
			$this->_updateCount($itemRoutes,$dataitem);

			$data['dataitem']=$dataitem;
			$data['masteritem'] =$itemRoutes;
			$data['datatable']=$itemTable;

			$resultHook = $this->hooks->call_hook(['tech5s_baseview_before_load_data','itemRoutes'=>$itemRoutes,'itemTable'=>$itemTable,'dataitem'=>$dataitem,'data'=>$data]);
            if(!is_bool($resultHook)){
        	    extract($resultHook);
            }
			if(@$itemTable['pagination'] && $itemTable['pagination']==1){
				list($config,$newdata)=$this->_paginationLoad($tag,$itemRoutes,$itemTable,$dataitem,$pp);
				$data = array_merge($data,$newdata);

			}else{
				if($pp!==false){
					$this->catch404(true);
				}
			}
			$resultHook = $this->hooks->call_hook(['tech5s_baseview_before_show',"itemRoutes"=>$itemRoutes,"itemTable"=>$itemTable,"dataitem"=>$dataitem,"data"=>$data]);
            if(!is_bool($resultHook)){
        	    extract($resultHook);
            }
            if($this->blade->view()->exists($itemRoutes['controller'])){
				echo $this->blade->view()->make($itemRoutes['controller'],$data)->render();
            }
            else{
            	$this->catch404(true);
            }
		}
		else{
			$this->catch404();	
		}
		if($this->config->item( 'profiler_enable' )){
			$this->output->enable_profiler(TRUE);
		}
	}
	private function _updateCount($itemRoutes,$dataitem){
		if(array_key_exists('count', $dataitem)){
			$this->Dindex->updateData($itemRoutes['table'],array('count'=>'count+1'),array('id'=>$dataitem['id']));
		}
	}
	private function _paginationLoad($tag,$itemRoutes,$itemTable,$dataitem,$pp){
		$data = [];
		$config = [];
		if(strpos($itemRoutes["table"], "tag")===0){
			$parent = $itemRoutes["table"];
		}
		else{
			$parent ="parent";
		}
		$config['base_url']=base_url($tag);
		$config['per_page']=$itemTable['rpp_view'];
		$tableget = array_key_exists('table_child', $itemTable)?$itemTable['table_child']:$itemRoutes['table'];

		if($tableget !== $itemTable['name']){
			$pivot = $this->_checkPivotUse($tableget,$parent);
			if($pivot){
				$config['total_rows']=$data['count']=$this->Dindex->getNumDataDetail($tableget,array(
						array('key'=>'act','compare'=>'=','value'=>'1')
					),
					array(
						'field'=>$parent,
						'ptable'=>$itemTable['name'],
						'value'=>$dataitem['id']
					)
				);
				if(!@$pp) $pp=0;
				$pp= @$pp?$pp:0;
				$limit = $pp.",".$config['per_page'];
				$select = '*';
				$where = array(
							array('key'=>'act','compare'=>'=','value'=>'1')
						);
				$order = 'ord asc, id desc';
				$pivot = array(
							'field'=>$parent,
							'ptable'=>$itemTable['name'],
							'value'=>$dataitem['id']
						);
				$resultHook = $this->hooks->call_hook(['tech5s_baseview_before_listdata',"tableget"=>$tableget,"limit"=>$limit,"select"=>$select,"where"=>$where,"order"=>$order,"pivot"=>$pivot,"data"=>$data,"itemRoutes"=>$itemRoutes,"itemTable"=>$itemTable,"dataitem"=>$dataitem]);
				if(!is_bool($resultHook) && is_array($resultHook)){
	        	    extract($resultHook);
	            }
				$data['list_data'] = $this->Dindex->getDataDetail(array(
					'table'=>$tableget,
					'where'=>array(
							array('key'=>'act','compare'=>'=','value'=>'1')
						),
					'limit'=>$limit,
					'order' =>'ord asc, id desc',
					'pivot'=>array(
							'field'=>$parent,
							'ptable'=>$itemTable['name'],
							'value'=>$dataitem['id']
						)
					));
			}
			else{
				$config['total_rows']=$data['count']=$this->Dindex->getNumDataDetail($tableget,array(
					array('key'=>'FIND_IN_SET("'.$dataitem['id'].'",'.$parent.')','compare'=>'>','value'=>'0'),
					array('key'=>'act','compare'=>'=','value'=>'1')
					));
				if(!@$pp) $pp=0;
				$pp= @$pp?$pp:0;
				$limit = $pp.",".$config['per_page'];
				$select = '*';
				$where = array(
							array('key'=>'FIND_IN_SET(\''.$dataitem['id'].'\','.$parent.')','compare'=>'>','value'=>'0'),
							array('key'=>'act','compare'=>'=','value'=>'1')
						);
				$order = 'ord asc, id desc';
				$resultHook = $this->hooks->call_hook(['tech5s_baseview_before_listdata',"tableget"=>$tableget,"limit"=>$limit,"select"=>$select,"where"=>$where,"order"=>$order,"pivot"=>[],"data"=>$data,"itemRoutes"=>$itemRoutes,"itemTable"=>$itemTable,"dataitem"=>$dataitem]);
				if(!is_bool($resultHook) && is_array($resultHook)){
	        	    extract($resultHook);
	            }
				$data['list_data'] = $this->Dindex->getDataDetail(array(
					'table'=>$tableget,
					'where'=>array(
							array('key'=>'FIND_IN_SET(\''.$dataitem['id'].'\','.$parent.')','compare'=>'>','value'=>'0'),
							array('key'=>'act','compare'=>'=','value'=>'1')
						),
					'limit'=>$limit,
					'order' =>$order
				));
			}
			$config['uri_segment']=2;
			$config['reuse_query_string'] = true;
			$this->pagination->initialize($config);
			if($pp>0){
	            $data['_meta_noindex']='<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">';
	        }

		}
        return [$config,$data];
	}
	private function _checkPivotUse($table,$parent){
		$count = $this->Dindex->getNumDataDetail("nuy_detail_table",array(
			array('key'=>'name','compare'=>'=','value'=>'pivot_'.$parent),
			array('key'=>'link','compare'=>'=','value'=>$table),
			array('key'=>'act','compare'=>'=','value'=>'1')
			));
		return $count>0;
	}
	public function loadLanguage(){
		$get = $this->input->get();
        if(!empty($get) && !empty($get['lang'])){
        	$languages = $this->config->item( 'languages' );
        	if( in_array($get['lang'],$languages)){
        		$this->session->set_userdata('lang', $get['lang']);
        	}
        	else{
        		$default_language = $this->config->item( 'default_language' );
        		$this->session->set_userdata('lang', $default_language);
        	}
        }
        $lang = $this->session->userdata('lang');
        $this->lang->load("all",$lang);
	}
	public function changeLanguage($lang){
		$languages = $this->config->item( 'languages' );
		if($lang!='' && in_array($lang,$languages) ){
			$this->session->set_userdata('lang',$lang);
		}
		else{
			$default_language = $this->config->item( 'default_language' );
    		$this->session->set_userdata('lang', $default_language);
		}
		$url = "/";
		$resultHook = $this->hooks->call_hook(['tech5s_change_language',"url"=>$url]);
		if(!is_bool($resultHook) && is_array($resultHook)){
    	    extract($resultHook);
        }
		if(isset($_SERVER['HTTP_REFERER'])){
			$pos = strpos($_SERVER['HTTP_REFERER'], "lang=");
			if($pos>0){
				$url = substr($_SERVER['HTTP_REFERER'],0, $pos+5 ).$lang;
				redirect($url);
			}
			else{
				redirect("/");
			}
		}
		redirect($url);
	}
	public function getLanguage(){
		$default_language = $this->config->item( 'default_language' );
		$lang= $this->session->has_userdata("lang")?$this->session->userdata("lang"):$default_language;
		return $lang ==$default_language?"":$lang."_";
	}
	public function updateOnline(){
		$ip = $_SERVER['REMOTE_ADDR'];
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$data['ip']=$ip;
		$data['url']=$actual_link;
		$data['create_time']=time();
		$this->Dindex->insertData('visit_online',$data);
	}
	function castToObject($instance, $className)
	{
	    if (!is_object($instance)) {
	        return false;
	    }
	    if (!class_exists($className)) {
	        return false;
	    }
	    return unserialize(
	        sprintf(
	            'O:%d:"%s"%s',
	            strlen($className),
	            $className,
	            strstr(strstr(serialize($instance), '"'), ':')
	        )
	    );
	}
	function catch404($force=false){
        $uri = uri_string();
        $arrUri = explode('/', $uri);
        if(count($arrUri)>1){
        	$uri = $arrUri[0];
        }
        $arr = $this->Dindex->getData('nuy_routes',array('link'=>$uri),0,1);
        if(count($arr)>0 && !$force){
        	$itemRoutes = $arr[0];
        	$fnc = $itemRoutes['controller'];
        	$class = substr($fnc,0, strpos($fnc, '/'));
        	$fnc = substr($fnc, strpos($fnc, '/')+1);
        	if($this!==false && (method_exists($this, $fnc) || (isset(static::$macros) && array_key_exists($fnc, static::$macros))))
	        {
	        	$resultHook = $this->hooks->call_hook(['tech5s_baseview_before_static_show',"itemRoutes"=>$itemRoutes]);
	            if(!is_bool($resultHook)){
            	    extract($resultHook);
	            }
            	$this->$fnc($itemRoutes);
	        }
	        else{
	        	$this->output->set_status_header('404');
				echo $this->blade->view()->make('404')->render();
	        }
    	}
    	else{
    		$this->output->set_status_header('404');
        	echo $this->blade->view()->make('404')->render();
        }
       
    }
      function preview(){
		$post= $this->input->postf();
		if(@$post){
			$listFields = $this->Dindex->getDataDetail(array(
							'table'=>'nuy_detail_table',
							'where'=>array(
									array('key'=>'link','compare'=>'=','value'=>"'".$post['table']."'")
								)
							));
			$post["id"]=0;
			foreach ($listFields as $key => $value) {
				if(!array_key_exists($key, $post)){
					$post[$key]="";
				}
			}
			$arrData= array(0=>$post);
			$arrTable = $this->Dindex->getData('nuy_table',array('map_table'=>$post['table']),0,1);
			if(count($arrTable)<=0) return;
			if(count($arrData)>0){
				$data['dataitem']=count($arrData)>0?$arrData[0]:"";
				$data['masteritem'] = array("table"=>$post["table"]);
				$itemTable = $arrTable[0];
				$data['datatable']=$itemTable;
				echo $this->blade->view()->make($post["table"]."/view",$data)->render();
				
				
			}
		}
		else{
			$this->output->set_status_header('404');
			echo $this->blade->view()->make('404')->render();
		}
	}
    
}