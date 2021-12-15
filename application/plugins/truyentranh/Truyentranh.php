<?php
class Truyentranh extends IPlugin{
	protected $CI;
	protected $contact = 'lien-he';
	protected $chapter = 'chapter';
	protected $story_update = 'truyen-moi';
	protected $hover_story = 'hover-story';
	public function install(){
		$this->addRoutes("Vindex/contact",$this->contact);
		$this->addRoutes("Vindex/chapter",$this->chapter);
		$this->addRoutes("Vindex/story_update",$this->story_update);
		$this->addRoutes("Vindex/hover_story",$this->hover_story);

	}
	public function uninstall(){
		$this->removeRoutes($this->contact);
		$this->removeRoutes($this->chapter);
		$this->removeRoutes($this->story_update);
		$this->removeRoutes($this->hover_story);

	}

	public function initVindex(){
		$vindex = &get_instance();
		$page = $this;
		$vindex::macro("contact", function($itemRoutes) use($page){
			$page->contact($itemRoutes);
		});
		$vindex::macro("chapter", function($itemRoutes) use($page){
			$page->chapter($itemRoutes);
		});
		$vindex::macro("story_update", function($itemRoutes) use($page){
			$page->story_update($itemRoutes);
		});
		$vindex::macro("hover_story", function($itemRoutes) use($page){
			$page->hover_story($itemRoutes);
		});
		

	}
	function contact($item) {
		$lang = $this->CI->getLanguage();
		$data['dataitem']['s_title']= $item[$lang.'title_seo'];
		$data['dataitem']['s_des']= $item[$lang.'des_seo'];
		$data['dataitem']['s_key'] = $item[$lang.'key_seo'];
		echo $this->CI->blade->view()->make('page.contact',$data)->render();
	}
	function chapter(){
		$get = $this->CI->input->get();
		$where=array();
		$story = [];
		if (isset($get['id'])) {
			$id = (int) $get['id'];
			array_push($where, array('key'=>'parent','compare'=>'=','value'=>$id));
			
			$stories=$this->CI->Dindex->getDataDetail(array(
	            'table'=>'pro',
	            'limit'=>'0,1',
	            'where' =>[['key'=>'id','compare'=>'=','value'=>$id]]
	        ));
	        if(count($stories)>0){
	        	$story = $stories[0];
	        }
		}
		if(count($story)==0) return;
        $pp = $this->CI->uri->segment(2,0);
        $order = 'id desc';
        $config['base_url']=base_url('chapter');
        $perpage = 16;
        $table ="chapter";
        $config['per_page']=$perpage;
        $config['total_rows']=$this->CI->Dindex->getNumDataDetail($table,$where);
        $limit = $pp.",".$config['per_page'];
        $data['list_data'] = $this->CI->Dindex->getDataDetail(array(
            'table'=>$table,
            'limit'=>$limit,
            'where' =>$where,
            'order'=>$order,
        ));
        $data['story'] = $story;
        $config['uri_segment']=2;
        $config['reuse_query_string'] = true;
        $this->CI->pagination->initialize($config);
        echo $this->CI->blade->view()->make('pro.chapter_ajax', $data)->render();
	}
	function story_update(){
		$sql="SELECT max(create_time)as create_time,parent from chapter GROUP BY parent ORDER BY create_time DESC";
		$sort_chapter=$this->CI->Dindex->selectRawQuery($sql);
		$data['list_data']=[];
		foreach ($sort_chapter as $value) {
			$sql = "select * from pro where act = 1 and id = ".$value['parent']." limit 0,9";
			$dataitem = $this->CI->Dindex->selectRawQuery($sql);
			foreach ($dataitem as $value) {
			array_push($data['list_data'],$value);
				
			}
		}
		echo $this->CI->blade->view()->make('story_update_ajax', $data)->render();
	}


}