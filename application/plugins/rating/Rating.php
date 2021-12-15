<?php
class Rating extends IPlugin{
	protected $CI;
	protected $linkContact = "rating";
	public function __construct(){
		parent::__construct();
		$this->CI = &get_instance();
	}
	public function install(){
		$this->addRoutes("Vindex/rating",$this->linkContact);
		$this->publishFile("theme/js/script.js");
	}
	public function uninstall(){
		$this->removeRoutes($this->linkContact);
		$this->removeFile();
	}
	
	public function initVindex(){
		$vindex = &get_instance();
		$page = $this;
		$vindex::macro("rating", function($itemRoutes) use($page){
			$page->rating($itemRoutes);
		});
	}
	function rating(){
		if(@$_GET){
			$get= $this->CI->input->get();
			$sessRating = $this->CI->session->userdata('_rating');
			$sessRating = isset($sessRating)?$sessRating:array();
			if(count($sessRating) > 0){
				foreach ($sessRating as $key => $value) {
					if($value['id'] == $get['id'] && $value['table'] == $get['table']){
						echoJSON(100,$this->CI->getLanguage()==""?"Bạn đã bình chọn, xin cảm ơn!":"Thanks vote");
						return;
					}
				}
			}
			$arr = $this->CI->Dindex->getDataDetail(array(
				'table'=>$get['table'],
				'limit'=>'0,1',
				'where' =>array(array('key'=>'id','compare'=>'=','value'=>$get['id']))
			));
			$lastScore=0;
			$code=100;
			$message ="Đánh giá không thành công!";
			$r = array();
			if(count($arr)>0){
				$rate = $arr['0']['score'];
				$r = json_decode($rate,true);$r = (array)$r;
				$val = "s-".$get['val'];
				if(array_key_exists($val, $r))
				{
					$r[$val] +=1;    
				}
				else{
					$r[$val] =1;
				}
				$score =0;
				$total= 0;
				foreach ($r as $key => $value) {
					$score+=$value* str_replace("s-", "", $key);
					$total += $value;
				}
				$lastScore = $score/($total>0?$total:1);
				$code=200;
				$message ="Đánh giá thành công!";
			}
			$obj = new stdClass();
			$obj->score= $lastScore;
			$obj->total= $total;
			$obj->code = $code;
			$obj->message = $message;
			$obj->sosao = $get['val'];
			$json =  json_encode($obj);
			$data['score'] = json_encode((object)$r);
			$this->CI->Dindex->updateDataFull($get['table'],$data,array('id'=>$get['id']));
			array_push($sessRating, array('id'=>$get['id'], 'table'=>$get['table']));
			$this->CI->session->set_userdata('_rating',$sessRating);
			$sosao=array('id'=>$get['id'],'sosao'=>$get['val']);
			$this->CI->session->set_userdata('sosao',$sosao);
			echo $json;
		}
	}
	public function insertScript(){
		return '<script defer type="text/javascript" src="'.$this->urlFile("theme/js/script.js").'"></script>';
	}
}