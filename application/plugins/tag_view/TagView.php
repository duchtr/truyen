<?php
class TagView extends IPlugin{
	protected $CI;
	protected $linkTag = "tag";
    protected $linkTagPro = "tags";
	public function __construct(){
		parent::__construct();
		$this->CI = &get_instance();
	}
	public function install(){
		$this->addRoutes("Vindex/tag",$this->linkTag,[
			"title_seo"=>"tag",
			"des_seo"=>"tag",
			"en_title_seo"=>"tag",
			"en_des_seo"=>"tag",
		]);
        $this->addRoutes("Vindex/tags",$this->linkTagPro,[
            "title_seo"=>"tag",
            "des_seo"=>"tag",
            "en_title_seo"=>"tag",
            "en_des_seo"=>"tag",
        ]);
	}
	public function uninstall(){
		$this->removeRoutes($this->linkTag);
        $this->removeRoutes($this->linkTagPro);
	}
	
	public function initVindex(){
		$vindex = &get_instance();
		$page = $this;
		$vindex::macro("tag", function($itemRoutes) use($page){
			$page->tag($itemRoutes);
		});
        $vindex::macro("tags", function($itemRoutes) use($page){
            $page->tagPro($itemRoutes);
        });
	}
	public function tag($itemRoutes){
        $dataitem['s_title']= $itemRoutes['title_seo'];
        $dataitem['s_des']= $itemRoutes['des_seo'];
        $dataitem['s_key'] = $itemRoutes['key_seo'];
        $data['dataitem'] = $dataitem;
        $tag = $this->CI->uri->segment(2,"");
        $tag = urldecode ($tag);
        $arrTag = $this->CI->Dindex->getDataDetail(array(
            'table'=>'tag',
            'where'=>array(array('key'=>'link','compare'=>'=','value'=>$tag))
        ));
        $tag = count($arrTag)?$arrTag[0]['id']:0;
        if(count($arrTag)>0){
            $data['dataitem'] = $arrTag[0];
        }
        $pp = $this->CI->uri->segment(3,0);
        $table="news";
        $config['base_url']=base_url('').'tag/'.$arrTag[0]['link'];
        $config['per_page']=9;
        $config['total_rows']=$this->CI->Dindex->getNumDataDetail($table,array(array('key'=>'FIND_IN_SET('.$tag.',tag)','compare'=>'>','value'=>0)));
        $limit = $pp.",".$config['per_page'];
        $data['list_data'] = $this->CI->Dindex->getDataDetail(array(
            'table'=>$table,
            'limit'=>$limit,
            'where'=>array(array('key'=>'FIND_IN_SET('.$tag.',tag)','compare'=>'>','value'=>0)),
            'order'=>'ord asc, id desc'
        ));
        $config['uri_segment']=3;
        $this->CI->pagination->initialize($config);
        echo $this->CI->blade->view()->make('tag.view',$data)->render();
	}
    function tagPro($itemRoutes){
        $dataitem['s_title']= $itemRoutes['title_seo'];
        $dataitem['s_des']= $itemRoutes['des_seo'];
        $dataitem['s_key'] = $itemRoutes['key_seo'];
        $data['dataitem'] = $dataitem;
        $tag = $this->CI->uri->segment(2,"");
        $tag = urldecode ($tag);
        $arrTag = $this->CI->Dindex->getDataDetail(array(
            'table'=>'tag_pro',
            'where'=>array(array('key'=>'link','compare'=>'=','value'=>$tag))
        ));
        $tag = count($arrTag)?$arrTag[0]['id']:0;
        if(count($arrTag)>0){
            $data['dataitem'] = $arrTag[0];
        }
        $pp = $this->CI->uri->segment(3,0);
        $table="pro";
        $config['base_url']=base_url('').'tags/'.$arrTag[0]['link'];
        $config['per_page']=12;
        $config['total_rows']=$this->CI->Dindex->getNumDataDetail($table,array(array('key'=>'FIND_IN_SET('.$tag.',tag_pro)','compare'=>'>','value'=>0)));
        $limit = $pp.",".$config['per_page'];
        $data['list_data'] = $this->CI->Dindex->getDataDetail(array(
            'table'=>$table,
            'limit'=>$limit,
            'where'=>array(array('key'=>'FIND_IN_SET('.$tag.',tag_pro)','compare'=>'>','value'=>0)),
            'order'=>'ord asc, id desc'
        ));
        $config['uri_segment']=3;
        $this->CI->pagination->initialize($config);
        echo $this->CI->blade->view()->make('tags.view',$data)->render();
    }
}