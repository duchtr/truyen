<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use WebPConvert\WebPConvert;
class Vindex extends MY_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('array', 'url', 'form','hp','cookie', 'cart_helper'));      
        $this->load->library(array('pagination','bcrypt'));
        $this->load->helper('captcha');
        
    }
    public function index()
    {
        $loadBaseView = true;
        $resultHook = $this->hooks->call_hook(['tech5s_before_baseview','tag'=>'/','loadBaseView'=>$loadBaseView,'params'=>[]]);
        if(is_array($resultHook)){
            extract($resultHook);
        }
        if($loadBaseView){
            echo $this->blade->view()->make('main')->render();    
        }
	if($this->config->item( 'profiler_enable' )){
		$this->output->enable_profiler(TRUE);
	}
	
    }
    function baseAllItem($item,$table,$perpage) {        
        $pp = $this->uri->segment(2,0);
        $config['base_url']=base_url('').$item['link'];
        $config['per_page']=$perpage;
        $where = array(array('key'=>'act','compare'=>'=','value'=>1));
        $config['total_rows']=$this->Dindex->getNumDataDetail($table, $where);
        $limit = $pp.",".$config['per_page'];
        $data['list_data'] = $this->Dindex->getDataDetail(array(
            'table'=>$table,
            'limit'=>$limit,
            'where' =>$where,
            'order'=>'ord asc, id desc'
        ));

        $config['uri_segment']=2;
        $this->pagination->initialize($config);
        $lang = $this->getLanguage();
        $data['dataitem']['s_title']= $item[$lang.'title_seo'];
        $data['dataitem']['s_des']= $item[$lang.'des_seo'];
        $data['dataitem']['s_key'] = $item[$lang.'key_seo'];
        $data['stt'] = $pp;
        if(!@$_POST){
            echo $this->blade->view()->make('all'.$table.".all".$table,$data)->render();    
        }
    }
}