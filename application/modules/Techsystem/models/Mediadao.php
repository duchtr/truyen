<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mediadao extends CI_Model
{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->library(array('session'));
	}
	public function getMedia($offset,$number){
		$this->db->order_by('is_file asc, name asc');
		if($number>0) {
			$query = $this->db->get('medias', $number, $offset);	
		}
		else{
			$query = $this->db->get('medias');
		}
		
		return $query->result_array();
	}
	public function getMediaW($id,$offset,$number,$keyword=""){
		$this->db->where("parent",$id);
		$this->db->where("trash",0);
		if(strlen($keyword)>0){
			$this->db->like("name",$keyword,"both");
		}
		$order = 'is_file asc, create_time desc, name asc';
		$resultHook = $this->hooks->call_hook(['tech5s_media_get_media_w_order','order'=>$order]);
		if(!is_bool($resultHook) && is_array($resultHook)){
			extract($resultHook);
		}
		$this->db->order_by($order);
		if($number>0) {
			$query = $this->db->get('medias', $number, $offset);	
		}
		else{
			$query = $this->db->get('medias');
		}
		$resultHook = $this->hooks->call_hook(['tech5s_media_get_media_w','query'=>$query]);
		if(!is_bool($resultHook) && is_array($resultHook)){
			extract($resultHook);
		}
		return $query->result_array();
	}
	public function getMediaTrash(){
		$this->db->where("trash",1);
		
		$this->db->order_by('is_file asc, name asc');
		
		$query = $this->db->get('medias');
		
		
		return $query->result_array();
	}
	public function getMediaT($parent){
		$this->db->where("parent",$parent);
		$query = $this->db->get('medias');
		return $query->result_array();
	}
	public function getSingleMedia($id){
		$this->db->where("id",$id);
		$this->db->order_by('is_file asc, name asc'); 
		$query = $this->db->get('medias', 1, 0);
		return $query->result_array();
	
	}
	public function getMediaFolder($parent){
		$this->db->where("parent",$parent);
		$this->db->where("is_file",0);
		$this->db->order_by('is_file asc, name asc'); 
		$query = $this->db->get('medias');
		return $query->result_array();
	
	}
	public function deleteMedia($id){
		$this->db->trans_start();
		$this->db->where("id",$id);
		$this->db->delete("medias");
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
	public function insertMedia($data){
		$this->db->trans_start();
		$this->db->insert("medias",$data);
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
	public function updateMedia($data,$id){
		$this->db->trans_start();
		$this->db->where("id",$id);
		$this->db->update("medias",$data);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		    return $id;
		}
		else
		{
		    $this->db->trans_commit();
		    return $id;
		}
		
	}
	public function countNumberElement($parent,$keyword = ""){
		$parent = (int)$parent;
		$sql = "select sum(case when is_file =1 then 1 else 0 end) file,sum(case when is_file =0 then 1 else 0 end) folder  from medias where trash = 0 and parent = ".$parent;
		if(strlen($keyword)>0){
			$sql .=" and name like '%".$keyword."%'";
		}
   		$query=$this->db->query($sql);
   		return $query->result_array();
	}
	public function countNumberElementTrash(){
		$sql = "select sum(case when is_file =1 then 1 else 0 end) file,sum(case when is_file =0 then 1 else 0 end) folder  from medias where trash = 1";
   		$query=$this->db->query($sql);
   		return $query->result_array();
	}
}
?>