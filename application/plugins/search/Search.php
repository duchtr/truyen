<?php
class Search extends IPlugin{
	protected $CI;
	protected $linkSearch = "tim-kiem";
	protected $linkSearch1 = "tim-kiem-tin-tuc";
	public function __construct(){
		parent::__construct();
		$this->CI = &get_instance();
	}
	public function install(){
		$this->addRoutes("Vindex/search",$this->linkSearch);
		$this->addRoutes("Vindex/search_new",$this->linkSearch1);
		// $this->publishFile("theme/css/style.css");
		// $this->publishFile("theme/js/script.js");
	}
	public function uninstall(){
		// $this->removeRoutes($this->linkSearch);
		// $this->removeRoutes($this->linkSearch1);
		// $this->removeFile();
	}
	
	public function initVindex(){
		$vindex = &get_instance();
		$page = $this;
		$vindex::macro("search", function($itemRoutes) use($page){
			$page->search($itemRoutes);
		});
		$vindex::macro("search_new", function($itemRoutes) use($page){
			$page->search_new($itemRoutes);
		});
	}
	public function search_new($itemRoutes){
		
		$pp=$this->CI->uri->segment(2);
	    if(!$pp) $pp=0;
	    if(@$_GET){
	        $datasearch = $this->CI->input->get();
	        $this->CI->session->set_userdata('data_search',$datasearch);
	    }
	    else{
	        $datasearch =$this->CI->session->userdata('data_search');
	    }
	    $q=@$datasearch && @$datasearch['q']?$datasearch['q']:"";
	    $q=addslashes($q);
	    $where=array();
	    if(!empty($q)){
	        array_push($where, array("key"=>"name",'compare'=>'like','value'=>"".$q.""));
	    }
	    array_push($where, array('key'=>'act','compare'=>'=','value'=>1));
	    $config['base_url']=base_url('').$itemRoutes['link'];
	    $config['per_page']=12;
	    $config['total_rows']=$this->CI->Dindex->getNumDataDetail('news',$where);
	    $limit = $pp.",".$config['per_page'];
	    $data['list_data'] = $this->CI->Dindex->getDataDetail(array(
	        'table'=>'news',
	        'where'=>$where,
	        'limit'=>$limit
	    ));

	    $config['uri_segment']=2;
	    $data['totalrow'] = $config['total_rows'];
	    $data['pages'] = $config['total_rows']/$config['per_page'];
	    
	    $this->CI->pagination->initialize($config);
	    $data['keyword']=$q;
	    $dataitem['s_title']= $itemRoutes['title_seo']."-".$q;
	    $dataitem['s_des']= $itemRoutes['des_seo'];
	    $dataitem['s_key'] = $itemRoutes['key_seo'];
	    $data['dataitem'] =$dataitem;

	    echo $this->CI->blade->view()->make('search/view_news',$data)->render();
	}
public function search($itemRoutes){
		$pp=$this->CI->uri->segment(2);
	    if(!$pp) $pp=0;
	    if(@$_GET){
	        $datasearch = $this->CI->input->get();
	        $this->CI->session->set_userdata('data_search',$datasearch);
	    }
	    else{
	        $datasearch =$this->CI->session->userdata('data_search');
	    }
	    $q=@$datasearch && @$datasearch['q']?$datasearch['q']:"";
	    $q=addslashes($q);
	    $where=array();
	    if(!empty($q)){
	        array_push($where, array("key"=>"name",'compare'=>'like','value'=>"".$q.""));
	    }
	    array_push($where, array('key'=>'act','compare'=>'=','value'=>1));
	    $config['base_url']=base_url('').$itemRoutes['link'];
	    $config['per_page']=6;
	    $config['total_rows']=$this->CI->Dindex->getNumDataDetail('pro',$where);
	    $limit = $pp.",".$config['per_page'];
	    $data['list_data'] = $this->CI->Dindex->getDataDetail(array(
	        'table'=>'pro',
	        'where'=>$where,
	        'limit'=>$limit
	    ));

	    $config['uri_segment']=2;
	    $data['totalrow'] = $config['total_rows'];
	    $data['pages'] = $config['total_rows']/$config['per_page'];
	    
	    $this->CI->pagination->initialize($config);
	    $data['keyword']=$q;
	    $dataitem['s_title']= $itemRoutes['title_seo']."-".$q;
	    $dataitem['s_des']= $itemRoutes['des_seo'];
	    $dataitem['s_key'] = $itemRoutes['key_seo'];
	    $data['dataitem'] =$dataitem;
	    echo $this->CI->blade->view()->make('search/view_pro',$data)->render();
	}

	public function insertStyle(){
		return '<link rel="stylesheet" href="'.$this->urlFile("theme/css/style.css").'">';
	}
	public function insertScript(){
		return '<script defer type="text/javascript" src="'.$this->urlFile("theme/js/script.js").'"></script>';
	}
}