<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('ADMIN', 'Techsystem');
define('MISSING_PARAM', 110);
define('SUCCESS', 200);
define('ERROR', 100);
class Media extends CI_Controller
{
	private $table = 'medias';
	public function __construct(){
		parent::__construct();
		$this->load->helper( array('array', 'url', 'form','Techsystem/adminhp','hp','exif/exif_helper','Techsystem/auser'));		
		$this->load->model(array('Admindao','Mediadao'));
		$this->load->library(array('session','pagination','sitemap','bcrypt','simple_html_dom'));
		$this->config->load('filemanager', FALSE, TRUE);
		define('MEDIA_PER_PAGE',100);
	}
	public function test(){
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $params);
		$params['o']='o';
		$url_parts['query'] = http_build_query($params);
		echo current_url().'?'. $url_parts['query'];
	}
	public function media(){
		if(!$this->checkPermisstionAccess($this->table,'view'))
		{
			return;
		}
		$path_uploads = $this->config->item('path_uploads');
		$prs = getParameter();
		$data = array();
		$pp=0;
		$keyword = array_key_exists('keyword', $prs)?$prs['keyword']:'';
		$keyword = addslashes($keyword);
		$data['keyword'] = $keyword;
		if(array_key_exists('folder', $prs)){
			$folder =urldecode($prs['folder']);
			$folders = explode(',', $folder);
			$inputpath= $this->generatePathDir($folders);
			if(strlen($inputpath)>0 && count($folders)>0){
				$f = $this->Mediadao->getSingleMedia($folders[count($folders)-1]);
				if(count($f)>0){
					$dbpath = $f[0]['path'].$f[0]['file_name'].'/';	
				}
				else{
					$dbpath = '';
				}
				
				if(count($f)>0 && $dbpath==$inputpath){
					$this->session->set_userdata('PROCESS_FILE',array('CURRENT_PATH'=>$inputpath,'CURRENT_ID'=>$f[0]['id']));
					$nums = $this->Mediadao->countNumberElement($f[0]['id'],$keyword);
					$nums= count($nums)>0?$nums[0]:array('file'=>0,'folder'=>0);
					$config['base_url']=base_url('').'Techsystem/Media/media';
					$config['per_page']=MEDIA_PER_PAGE;
					$config['reuse_query_string'] = true;
					$config['total_rows']=count($nums)>0?$nums['file']+$nums['folder']:0;
					$pp =(int) $this->uri->segment(4,0);
					$data['nums'] = $nums;
					$data['list_data'] = $this->Mediadao->getMediaW($f[0]['id'],$pp,$config['per_page'],$keyword);
					$config['uri_segment']=4;
					$this->pagination->initialize($config);
				}
				else{
					$this->load->view('v2/media/error',array('error'=>'Error matching params or not exist folder!'));return;
				}
			}
			else{
				$this->load->view('v2/media/error',array('error'=>'ERROR IN PARAMETER!'));return;
			}
		}
		else{
			$nums = $this->Mediadao->countNumberElement(0,$keyword);
			$nums= count($nums)>0?$nums[0]:array('file'=>0,'folder'=>0);
			$data['nums'] = $nums;
			$config['base_url']=base_url('').'Techsystem/Media/media';
			$config['per_page']=MEDIA_PER_PAGE;
			$config['reuse_query_string'] = true;
			$config['total_rows']=count($nums)>0?$nums['file']+$nums['folder']:0;
			$pp =(int) $this->uri->segment(4,0);
			$this->session->set_userdata('PROCESS_FILE',array('CURRENT_PATH'=>$path_uploads,'CURRENT_ID'=>0));	
			$data['list_data'] = $this->Mediadao->getMediaW(0,$pp,$config['per_page'],$keyword);
			$config['uri_segment']=4;
			$this->pagination->initialize($config);
			
		}
		$resultHook = $this->hooks->call_hook(['tech5s_media_before_view','data'=>$data]);
		if(!is_bool($resultHook)){
			extract($resultHook);
		}
		if($pp==0){
			$this->load->view('v2/media/index',$data);	
		}
		else{
			$this->load->view('v2/media/media-manager',$data);
		}
		
	}
	public function trash(){
		$resultHook = $this->hooks->call_hook(['tech5s_media_before_view_trash']);
		if(!is_bool($resultHook)){
			extract($resultHook);
		}
		$nums = $this->Mediadao->countNumberElementTrash();
		$nums= count($nums)>0?$nums[0]:array('file'=>0,'folder'=>0);
		$data['nums'] = $nums;
		$data['trash'] = 1;
		$config['reuse_query_string'] = true;
		$data['list_data'] = $this->Mediadao->getMediaTrash();
		$this->load->view('v2/media/index',$data);	

	}
	private function generatePathDir($folders){
		$path_uploads = $this->config->item('path_uploads');
		foreach ($folders as $key => $folder) {
			$f = $this->Mediadao->getSingleMedia($folder);
			if(count($f)>0){
				$path_uploads.=$f[0]['name'].'/';	
			}
			
		}
		return $path_uploads;
	}
	private function getInfoFile($filename,$file_path){
		$extimgs = $this->config->item('ext_img');
	 	$extvideos = $this->config->item('ext_video');
		$extfiles = $this->config->item('ext_file');
		$extmusic = $this->config->item('ext_music');
		$extmisc = $this->config->item('ext_misc');
		$pathuploads = $this->config->item('path_uploads');
		$basepath = $this->config->item('base_path');
		$obj = new stdClass();
		$obj->extension = strtolower(substr(strrchr($filename,'.'),1));
		$obj->size= human_filesize(filesize($file_path));
		$obj->date = filemtime($file_path);
		  
		$obj->isfile = is_file($file_path)?1:0;
		$onlyDir =  substr($file_path,0, strrpos($file_path, '/')+1);
		$onlyDir = str_replace(FCPATH.'/', '', $onlyDir);
		$obj->dir= $onlyDir;
		$obj->path = $onlyDir.$filename;
		if($obj->isfile){
			if(in_array($obj->extension, $extimgs)){
			$imagedetails = getimagesize($file_path);
			$obj->width = $imagedetails[0];
			$obj->height= $imagedetails[1];
			    if(file_exists(FCPATH.$onlyDir.'thumbs/def/'.$filename)){
			    $obj->thumb = $basepath.$onlyDir.'thumbs/def/'.$filename;
			    }
			    else{
			    $obj->thumb = $basepath.$onlyDir.$filename;
			    }
			}
			else if(in_array($obj->extension, $extvideos)){
			    if(file_exists(FCPATH.'theme/admin/images/ico/'.$obj->extension.'.jpg')){
			    $obj ->thumb = $basepath.'theme/admin/images/ico/'.$obj->extension.'.jpg';
			    }
			    else{
			    $obj->thumb = $basepath.'theme/admin/images/file.jpg';
			    }
			}
			else if(in_array($obj->extension, $extfiles)){
			    if(file_exists(FCPATH.'theme/admin/images/ico/'.$obj->extension.'.jpg')){
			    $obj ->thumb = $basepath.'theme/admin/images/ico/'.$obj->extension.'.jpg';
			    }
			    else{
			    $obj->thumb = $basepath.'theme/admin/images/file.jpg';
			    }
			  }
			else if(in_array($obj->extension, $extmusic)){
			    if(file_exists(FCPATH.'theme/admin/images/ico/'.$obj->extension.'.jpg')){
			    $obj ->thumb = $basepath.'theme/admin/images/ico/'.$obj->extension.'.jpg';
			    }
			    else{
			    $obj->thumb = $basepath.'theme/admin/images/file.jpg';
			    }
			  }
			  else if(in_array($obj->extension, $extmisc)){
			    if(file_exists(FCPATH.'theme/admin/images/ico/'.$obj->extension.'.jpg')){
			    $obj ->thumb = $basepath.'theme/admin/images/ico/'.$obj->extension.'.jpg';
			    }
			    else{
			    $obj->thumb = $basepath.'theme/admin/images/file.jpg';
			    }
			  }
			else{
			  $obj->thumb = $basepath.'theme/admin/images/file.jpg';
			}
		}
		return $obj;
	}
	private function outputJson($arr,$code = SUCCESS){
		if(is_string($arr)){
		}
		else {
			$arr = json_encode($arr); 
		}
		echoJSON($code,$arr);
		return;
	}
	public function createDir(){
		if(!$this->checkPermisstionAccess($this->table,'insert'))
		{
			return;
		}
		$post = $this->input->postf();
		if(@$post && isset($post['folder_name'])){
			$pf = $this->session->userdata('PROCESS_FILE');
			if(!@$pf || !array_key_exists('CURRENT_PATH', $pf)){
				echoJSON(MISSING_PARAM,'Thiếu thông tin dữ liệu!');
			}
			else{
				$currentpath  = $pf['CURRENT_PATH'];
				$folder_name=replaceURL($post['folder_name']);
				$resultHook = $this->hooks->call_hook(['tech5s_media_before_create_folder','currentpath'=>$currentpath,'folder_name'=>$folder_name]);
				if(!is_bool($resultHook)){
					extract($resultHook);
				}
				if(!is_dir($currentpath.$folder_name)){
					mkdir($currentpath.$folder_name,0777,TRUE);
					$data['name']= $folder_name;
					$data['file_name']= $folder_name;
					$data['is_file']= 0;
					$data['create_time']= time();
					$data['parent']= $pf['CURRENT_ID'];
					$data['path']= $currentpath;
					$data['file_name']= $folder_name;
					$data['extra']= json_encode($this->getInfoFile($folder_name,$currentpath));
					$ret = $this->Mediadao->insertMedia($data);
					return $this->outputJson($ret.'');
				}
				else{
					return $this->outputJson('Tạo Folder thất bại, có thể do đã tồn tại',ERROR);
				}
				
			}
		}
		else{
			return $this->outputJson('Thiếu thông tin dữ liệu!',MISSING_PARAM);
		}
	}
	private function _deteleAllFolderFile($parent,$permanent= false){
		if($permanent){
			$ps=$this->Mediadao->getMediaT($parent);	
		}
		else{
			$ps=$this->Mediadao->getMediaW($parent,-1,-1);
		}
		foreach ($ps as $key => $value) {
			if($permanent){
				$this->Mediadao->deleteMedia($value['id']);
			}
			else{
				$this->Mediadao->updateMedia(['trash'=>1],$value['id']);
			}
			if($value['is_file']==0){
				$this->_deteleAllFolderFile($value['id'],$permanent);
			}
		}
	}
	private function _restoreAll($parent){
		$ps=$this->Mediadao->getMediaT($parent);
		foreach ($ps as $key => $value) {
			if($value['is_file']==1){
				$this->Mediadao->updateMedia(['trash'=>0],$value['id']);
			}
			else{
				$this->_restoreAll($value['id']);
				$this->Mediadao->updateMedia(['trash'=>0],$value['id']);
			}
		}
	}
	private function rrmdir($dir) { 
	   if (is_dir($dir)) { 
	     $objects = scandir($dir); 
	     foreach ($objects as $object) { 
	       if ($object != '.' && $object != '..') { 
	         if (is_dir($dir.'/'.$object))
	           $this->rrmdir($dir.'/'.$object);
	         else
	           unlink($dir.'/'.$object); 
	       } 
	     }
	     rmdir($dir); 
	     return true;
	   } 
	   return false;
	 }
	public function deleteFolder(){
		if(!$this->checkPermisstionAccess($this->table,'delete'))
		{
			return;
		}
		$post = $this->input->postf();
		if(@$post && isset($post['id'])){
			$id = $post['id'];
			$permanent = isset($post['permanent'])?$post['permanent']:false;
			$dir = $this->Mediadao->getSingleMedia($id);
			if(count($dir)>0){
				$dir = $dir[0];
				$resultHook = $this->hooks->call_hook(['tech5s_media_before_delete_folder','dir'=>$dir]);
				if(!is_bool($resultHook)){
					extract($resultHook);
				}
				$this->_deteleAllFolderFile($id,$permanent);

				if($permanent){
					$bl = $this->rrmdir($dir['path'].$dir['file_name']);
					if($bl){
						$this->Mediadao->deleteMedia($id);
						return $this->outputJson($id.'');
					}
					else{
						$this->Mediadao->deleteMedia($id);
						return $this->outputJson('Không thể xóa thư mục',ERROR);
					}
				}
				else{
					$this->Mediadao->updateMedia(['trash'=>1],$id);
					return $this->outputJson($id.'');
				}
			}
			return $this->outputJson('Xóa Folder Thất bại!',ERROR);
		}
		else{
			return $this->outputJson('Thiếu thông tin dữ liệu!',MISSING_PARAM);
		}
	}
	public function getInfoLasted(){
		if(!$this->checkPermisstionAccess($this->table,'view'))
		{
			return;
		}
		$post = $this->input->post();
		if(@$post && @$post['id']){
			$resultHook = $this->hooks->call_hook(['tech5s_media_get_info_lasted','post'=>$post]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			$infos = $this->Mediadao->getSingleMedia($post['id']);
			if(count($infos)>0){
				$this->load->view('v2/media/folder',array('info'=>$infos[0]));
			}
		}
	}
	public function getInfoFileLasted(){
		if(!$this->checkPermisstionAccess($this->table,'view'))
		{
			return;
		}
		$post = $this->input->post();
		if(@$post && @$post['id']){
			$resultHook = $this->hooks->call_hook(['tech5s_media_get_info_file_lasted','post'=>$post]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			$ret = array();
			if(is_array($post['id'])){
				foreach ($post['id'] as $id) {
					$infos = $this->Mediadao->getSingleMedia($id);
					if(count($infos)>0) array_push($ret,$infos[0]);
				}
			}
			else{
				$infos = $this->Mediadao->getSingleMedia($post['id']);
				if(count($infos)>0) array_push($ret,$infos[0]);
			}
			$this->load->view('v2/media/multifile',array('infos'=>$ret));
			
		}
	}
	private function getSizes($file){
		if(file_exists($file)){
			$json = $this->Admindao->getConfigSite('size_image','');
			$arr = json_decode($json,true);
			$arr = @$arr?$arr:array();
			$s = getimagesize($file);
			$w = count($s)>0 && $s[0] > 0 ?$s[0]:1;
			$h = count($s)>1?$s[1]:1;
			foreach ($arr as $k => $v) {
				if($v['width']>$w){
					unset($arr[$k]);
				}
			}

			array_push($arr,array('name'=>'def','width'=>100,'height'=>(int)($h*100/$w),'quality'=>80));
			return $arr;
		}
		return array();
	}
	private function insertImageMedia($path, $filename,$parent = 0){
		$data['name']= $filename;
		$data['file_name']= $filename;
		$data['is_file']= 1;
		$data['create_time']= time();
		$data['parent']= $parent;
		$data['path']= $path;
		$data['extra']= json_encode($this->getInfoFile($filename,$path.$filename));
		return $this->Mediadao->insertMedia($data);
	}
	private function updateImageMedia($path, $filename,$id,$parent = 0){
		$data['name']= $filename;
		$data['file_name']= $filename;
		$data['is_file']= 1;
		$data['create_time']= time();
		if($parent!=-1){
			$data['parent']= $parent;
		}
		$data['path']= $path;
		$data['extra']= json_encode($this->getInfoFile($filename,$path.$filename));
		return $this->Mediadao->updateMedia($data,$id);
	}
	private function uploadSingleFile(){
		$post = $this->input->postf();
		$this->load->config('filemanager');
		$extimgs = $this->config->item('ext_img');
	  	$extvideos = $this->config->item('ext_video');
	  	$extfiles = $this->config->item('ext_file');
	  	$extmusic = $this->config->item('ext_music');
	  	$extmisc = $this->config->item('ext_misc');
	  	$config['upload_path']=$this->config->item('path_uploads');
	  	$pf = $this->session->userdata('PROCESS_FILE');
	  	
		if(@$pf && array_key_exists('CURRENT_PATH', $pf)&& array_key_exists('CURRENT_ID', $pf)){
			$config['upload_path'] = $pf['CURRENT_PATH'];
		}
		else{
			echoJSON(MISSING_PARAM,'Không tìm thấy đường dẫn!');
			return;
		}
      	$config['allowed_types'] = implode('|',$extimgs).'|'.implode('|',$extvideos).'|'.implode('|',$extfiles).'|'.implode('|',$extmusic);
      	$config['max_size'] = $this->config->item('max_size');
      	$config['max_width']  = $this->config->item('max_width');
      	$config['max_height']  = $this->config->item('max_height');
      	$this->load->library('upload', $config);
        
      	$images = array();
      	$field = 'file';
		$files = $_FILES[$field];
		$tmpName = $files['name'];
		$tmpRealName = substr($tmpName, 0,strrpos($tmpName, '.'));
		$ext = substr($tmpName, strrpos($tmpName, '.'));
		$config['file_name'] = replaceURL($tmpRealName).$ext;
        $_FILES[$field]['name']= $files['name'];
        $_FILES[$field]['type']= $files['type'];
        $_FILES[$field]['tmp_name']= $files['tmp_name'];
        $_FILES[$field]['error']= $files['error'];
        $_FILES[$field]['size']= $files['size'];
        $this->upload->initialize($config); // load new config setting 
        if ($this->upload->do_upload($field)) { // upload file here
        	$getFileUpload = $this->upload->data();
        	$fileuploaded = $config['upload_path'].$getFileUpload['file_name'];
        	if(in_array(substr($ext,1), $extimgs)){
        		$arrSizes = $this->getSizes($fileuploaded);
        		if(count($arrSizes)>0){
        			$new_image = '';
        			foreach ($arrSizes as $size) {
        				$new_image = $this->resizeImage($config['upload_path'],$getFileUpload,$size['width'],$size['height'],$size['quality'],$size['name']);
        			}
        			//array_push($images,$new_image);
        		}
        		else{
        			//array_push($images,$fileuploaded);
        		}
        	}
        	else{
        		//array_push($images,$fileuploaded);
        	}
        	$ret = $this->insertImageMedia($config['upload_path'],$getFileUpload['file_name'],$pf['CURRENT_ID']);
        	echoJSON(SUCCESS,$ret.'');
        } else {
            echoJSON(ERROR,'Upload file không thành công!');
        }
	}
	public function uploadFile(){
		if(!$this->checkPermisstionAccess($this->table,'insert'))
		{
			return;
		}
		set_time_limit(0);
		$post = $this->input->postf();
		$this->load->config('filemanager');
		$extimgs = $this->config->item('ext_img');
	  	$extvideos = $this->config->item('ext_video');
	  	$extfiles = $this->config->item('ext_file');
	  	$extmusic = $this->config->item('ext_music');
	  	$extmisc = $this->config->item('ext_misc');
	  	$config['upload_path']=$this->config->item('path_uploads');
	  	$pf = $this->session->userdata('PROCESS_FILE');
	  	
		if(@$pf && array_key_exists('CURRENT_PATH', $pf)&& array_key_exists('CURRENT_ID', $pf)){
			$config['upload_path'] = $pf['CURRENT_PATH'];
		}
		else{
			echoJSON(MISSING_PARAM,'Không tìm thấy đường dẫn!');
			return;
		}
      	$config['allowed_types'] = implode('|',$extimgs).'|'.implode('|',$extvideos).'|'.implode('|',$extfiles).'|'.implode('|',$extmusic).'|'.implode('|',$extmisc);
      	$config['max_size'] = $this->config->item('max_size');
      	$config['max_width']  = $this->config->item('max_width');
      	$config['max_height']  = $this->config->item('max_height');
      	$this->load->library('upload', $config);
        
      	$images = array();
      	$field = 'file';
      	if(!isset( $_FILES[$field])) return;
		$files = $_FILES[$field];
		foreach ($files['name'] as $key => $image) {
			$tmpName = $files['name'][$key];
			$tmpRealName = substr($tmpName, 0,strrpos($tmpName, '.'));
			$ext = strtolower(substr($tmpName, strrpos($tmpName, '.')));
			$config['file_name'] = replaceURL($tmpRealName).$ext;
	        $_FILES[$field.'[]']['name']= $files['name'][$key];
	        $_FILES[$field.'[]']['type']= $files['type'][$key];
	        $_FILES[$field.'[]']['tmp_name']= $files['tmp_name'][$key];
	        $_FILES[$field.'[]']['error']= $files['error'][$key];
	        $_FILES[$field.'[]']['size']= $files['size'][$key];
	        $this->upload->initialize($config); // load new config setting 
	        if ($this->upload->do_upload($field.'[]')) { // upload file here
	        	$getFileUpload = $this->upload->data();
	        	$fileuploaded = $config['upload_path'].$getFileUpload['file_name'];
	        	$resultHook = $this->hooks->call_hook(['tech5s_media_after_upload','fileuploaded'=>$fileuploaded]);
				if(!is_bool($resultHook)){
					extract($resultHook);
				}
	        	if(in_array(substr($ext,1), $extimgs)){
	        		$this->convertToWebp($fileuploaded);	
	        		$arrSizes = $this->getSizes($fileuploaded);
	        		if(count($arrSizes)>0){
	        			$new_image = '';
	        			foreach ($arrSizes as $size) {
	        				$new_image = $this->resizeImage($config['upload_path'],$getFileUpload,$size['width'],$size['height'],$size['quality'],$size['name']);
	        			}
	        		}
	        		else{
	        		}
	        	}
	        	else{
	        	}
	        	$ret = $this->insertImageMedia($config['upload_path'],$getFileUpload['file_name'],$pf['CURRENT_ID']);
	        	array_push($images,$ret);
	        } else {
	        	array_push($images,-1);
	        }
		}
		echo json_encode($images);
		
	}
	private function getWebpFile($file){
		$path = pathinfo($file);
		$dirname = $path['dirname'];
		$extension = $path['extension'];
		$filename = $path['filename'];
		$destination = $dirname.'/'.$filename.'.webp';
		return $destination;
	}
	private function convertToWebp($source){
		if(!$this->config->item('webp')) return;
		$destination = $this->getWebpFile($source);
		$options = [];
		\WebPConvert\WebPConvert::convert($source, $destination, $options);
		if(file_exists($destination)){
			return $destination;
		}
		return '';
	}
	private function resizeImage($upload_path,$getFileUpload,$widthImage,$heightImage,$quality,$name){
		$filename = is_array($getFileUpload)?$getFileUpload['file_name']:$getFileUpload;
		$this->load->library('image_lib');
      $config['image_library'] = 'gd2';
      $config['source_image'] = $upload_path.$filename;
      $config['create_thumb'] = false;
      $p = $upload_path.'thumbs/'.$name.'/';
        if(!is_dir($p)){
        	mkdir($p,0777,1);
        }
      $config['new_image'] = $p.$filename;
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
    	$this->convertToWebp($config['new_image']);
    	return $config['new_image'];
	}
	private function _deleteFile($id,$permanent = false){
		if(is_numeric($id)){
			$dir = $this->Mediadao->getSingleMedia($id);
		}
		else if(is_array($id)){
			$dir = $id;
			$id = $dir["id"];
		}
		else{
			$dir = [];
		}
		if(count($dir)>0){
			if($permanent){
				$d = $dir[0];
				$sizes = $this->getSizes($d['path'].$d['file_name']);
				foreach ($sizes as $key => $value) {
					$delfile = FCPATH.$d['path'].'thumbs/'.$value['name'].'/'.$d['file_name'];
					$webpFile = $this->getWebpFile($delfile);
					if(file_exists($delfile)){
						unlink($delfile);
					}
					if(file_exists($webpFile)){
						unlink($webpFile);
					}
					
				}
				$file = FCPATH.$d['path'].$d['file_name'];
				$webpFile = $this->getWebpFile($file);
				if(file_exists($file)){
					$bl = unlink($file);
				}
				if(file_exists($webpFile)){
					$bl = unlink($webpFile);
				}
				return $this->Mediadao->deleteMedia($id);
			}
			else{
				return $this->Mediadao->updateMedia(['trash'=>1],$id);
			}
			
		}
	}
	public function deleteFile(){
		if(!$this->checkPermisstionAccess($this->table,'delete'))
		{
			return;
		}
		$post = $this->input->post();
		if(@$post && isset($post['id'])){
			$id = $post['id'];
			$permanent = isset($post['permanent'])?$post['permanent']:false;
			$resultHook = $this->hooks->call_hook(['tech5s_media_delete_file','id'=>$id]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			$bl = $this->_deleteFile($id,$permanent);
			if($bl){
				return $this->outputJson($id.'');
			}
			else{
				return $this->outputJson('Không thể xóa tệp tin',ERROR);
			}
		}
		else{
			return $this->outputJson('Thiếu thông tin dữ liệu!',MISSING_PARAM);
		}
	}
	private function _rename($old,$new){
		if(is_file($old) || is_dir($old)){
			$ret = rename($old, $new);
			return $ret;
			}
		return false;
		}	
	public function rename(){
		if(!$this->checkPermisstionAccess($this->table,'edit'))
		{
			return;
		}
		$post = $this->input->postf();
		if(@$post && isset($post['id'])&& isset($post['newname'])){
			$pf = $this->session->userdata('PROCESS_FILE');
			if(!@$pf || !array_key_exists('CURRENT_PATH', $pf)){
				echoJSON(MISSING_PARAM,'Thiếu thông tin dữ liệu!');
			}
			else{
				$currentpath  = $pf['CURRENT_PATH'];
				$id = $post['id'];
				$file = $this->Mediadao->getSingleMedia($id);
				if(count($file)>0){
					$file = $file[0];
					$resultHook = $this->hooks->call_hook('tech5s_media_rename_file',$id,$file);
					if(!is_bool($resultHook)){
						extract($resultHook);
					}
					$extra = json_decode($file['extra'],true);
					$ex = ($extra['extension']?'.'.$extra['extension']:'');
					$sizes = $this->getSizes($currentpath.$file['file_name']);
					$ret = $this->_rename(FCPATH.$currentpath.$file['file_name'],FCPATH.$currentpath.$post['newname'].$ex);
					if($ret){
						foreach ($sizes as $key => $value) {
							$old = FCPATH.$currentpath.'thumbs/'.$value['name'].'/'.$file['file_name'];
							$new = FCPATH.$currentpath.'thumbs/'.$value['name'].'/'.$post['newname'].$ex;
							$this->_rename($old,$new);
						}
						$this->updateImageMedia($currentpath, $post['newname'].$ex,$id,-1);
						echoJSON(SUCCESS,'Đã cập nhật!');
						return;
					}	
				}
				echoJSON(ERROR,'Đổi tên thất bại!');
			}
		}
		else{
			echoJSON(MISSING_PARAM,'Thiếu thông tin dữ liệu!');
		}
	}
	public function deleteAll(){
		if(!$this->checkPermisstionAccess($this->table,'delete'))
		{
			return;
		}
		$post = $this->input->post();
		if(@$post && isset($post['ids'])){
			$ids = json_decode($post['ids']);
			$ids = @$ids ?$ids:array();
			$resultHook = $this->hooks->call_hook('tech5s_media_delete_all',$ids);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			foreach ($ids as $key => $value) {
				$this->_deleteFile($value,false);
			}
			echoJSON(SUCCESS,'Đã cập nhật');
		}
		else{
			echoJSON(MISSING_PARAM,'Thiếu thông tin dữ liệu!');
		}
	}
	private function _copyFile($old,$new){
		if(file_exists($old)){
			return copy($old, $new);
		}
		return false;
	}
	public function duplicateFile(){
		if(!$this->checkPermisstionAccess($this->table,'insert'))
		{
			return;
		}
		$post = $this->input->postf();
		if(@$post && isset($post['id'])){
			$id = $post['id'];
			$pf = $this->session->userdata('PROCESS_FILE');
			if(!@$pf || !array_key_exists('CURRENT_PATH', $pf)){
				echoJSON(MISSING_PARAM,'Thiếu thông tin dữ liệu!');
			}
			else{
				$currentpath  = $pf['CURRENT_PATH'];
				$file = $this->Mediadao->getSingleMedia($id);
				if(count($file)>0){
					$file = $file[0];
					$resultHook = $this->hooks->call_hook(['tech5s_media_duplicate_file','id'=>$id,'file'=>$file]);
					if(!is_bool($resultHook)){
						extract($resultHook);
					}
					$extra = json_decode($file['extra'],true);
					$ex = ($extra['extension']?'.'.$extra['extension']:'');
					$str = substr($file['file_name'], 0,strrpos($file['file_name'], '.'));
					$newfilename= $str.'_'.time();
					$ret = $this->_copyFile($currentpath.$file['file_name'], $currentpath.$newfilename.$ex);
					$sizes = $this->getSizes($currentpath.$file['file_name']);
					foreach ($sizes as $key => $value) {
						$old = FCPATH.$currentpath.'thumbs/'.$value['name'].'/'.$file['file_name'];
						$new = FCPATH.$currentpath.'thumbs/'.$value['name'].'/'.$newfilename.$ex;
						$this->_copyFile($old,$new);
					}
					$retid = $this->insertImageMedia($currentpath, $newfilename.$ex);
					if($ret){
						echoJSON(SUCCESS,''.$retid);
						return;
					}
				}
				echoJSON(ERROR,'Duplicate File không thành công!');
			}
		}
		else{
			echoJSON(MISSING_PARAM,'Thiếu thông tin dữ liệu!');
		}
	
	}
	private function recusiveMediaFolder($parent =0 ){
		$arr = $this->Mediadao->getMediaFolder($parent);
		$str = '';
		foreach ($arr as $key => $value) {
			$str .= '<ul class="list-folders">';
			$str .= '<li><a onclick=\'$(".list-folders li a").removeClass("active");$(this).addClass("active");return false;\' dt-id="'.$value['id'].'" href="#">'.'<i class="fa fa-folder"></i>'.$value['name'].'</a>';
			$str .=$this->recusiveMediaFolder($value['id']);
			$str .= '</li>';
			$str .= '</ul>';
		}
		return $str;
	}
	public function listFolder(){
		if(!$this->checkPermisstionAccess($this->table,'view'))
		{
			return;
		}
		$str = $this->recusiveMediaFolder(0);
		$this->load->view('v2/media/choosefolder',array('folders'=>$str));
	}
	public function listFolderMove(){
		if(!$this->checkPermisstionAccess($this->table,'view'))
		{
			return;
		}
		$str = $this->recusiveMediaFolder(0);
		$this->load->view('v2/media/choosefolder',array('folders'=>$str,'type'=>'move'));
	}
	public function moveFile(){
		if(!$this->checkPermisstionAccess($this->table,'edit'))
		{
			return;
		}
		$this->copyFile(true);
	}
	public function copyFile($deleteOld = false){
		if(!$this->checkPermisstionAccess($this->table,'insert'))
		{
			return;
		}
		$post = $this->input->post();
		if(@$post && isset($post['id']) && isset($post['idfolder'])){
			$idfile = $post['id'];
			$idfolder = $post['idfolder'];
			$pf = $this->session->userdata('PROCESS_FILE');
			if(!@$pf || !array_key_exists('CURRENT_PATH', $pf)){
				echoJSON(MISSING_PARAM,'Thiếu thông tin dữ liệu!');
				return;
			}
			$file = $this->Mediadao->getSingleMedia($idfile);
			$folder = $this->Mediadao->getSingleMedia($idfolder);
			if(count($file)>0 && count($folder)>0){
				$currentpath  = $pf['CURRENT_PATH'];
				$file = $file[0];
				$folder = $folder[0];
				$resultHook = $this->hooks->call_hook(['tech5s_media_copy_file','deleteOld'=>$deleteOld,'idfile'=>$idfile,'idfolder'=>$idfolder,'file'=>$file,'folder'=>$folder]);
				if(!is_bool($resultHook)){
					extract($resultHook);
				}
				$from = $currentpath.$file['file_name'];
				$to = $folder['path'].$folder['file_name'].'/'.$file['file_name'];
				$this->_copyFile($from,$to);
				if($deleteOld){
					unlink($from);
				}
				$sizes = $this->getSizes($currentpath.$file['file_name']);
				foreach ($sizes as $key => $value) {
					$old = FCPATH.$currentpath.'thumbs/'.$value['name'].'/'.$file['file_name'];
					$newdir = FCPATH.$folder['path'].$folder['file_name'].'/'.'thumbs/'.$value['name'].'/';
					if(!is_dir($newdir)){
						mkdir($newdir,0777,TRUE);
					}
					$new = $newdir.$file['file_name'];
					$this->_copyFile($old,$new);
					if($deleteOld){
						unlink($old);
					}
				}
				if($deleteOld){
					$retid = $this->updateImageMedia($folder['path'].$folder['file_name'].'/',$file['file_name'],$idfile,$folder['id']);
					echoJSON(SUCCESS,''.$retid);
					return;
				}
				else{
					$this->insertImageMedia($folder['path'].$folder['file_name'].'/',$file['file_name']);
					echoJSON(SUCCESS,'Đã cập nhật!');
					return;
				}
				
			}
			echoJSON(ERROR,'Copy Thất bại');
		}
		else{
			echoJSON(MISSING_PARAM,'Thiếu thông tin!');
		}
	}
	private function save_image($inPath,$outPath)
	{ 
		$opts=array(
		    'ssl'=>array(
		        'verify_peer'=>false,
		        'verify_peer_name'=>false,
		    ),
		);  
		$in = fopen($inPath, 'r', false, stream_context_create($opts));
	    $out=   fopen($outPath, 'wb');
	    while ($chunk = fread($in,8192))
	    {
	        fwrite($out, $chunk, 8192);
	    }
	    fclose($in);
	    fclose($out);
	}
	public function downloadImage(){
		if(!$this->checkPermisstionAccess($this->table,'insert'))
		{
			return;
		}
		$post = $this->input->postf();
		if(@$post && isset($post['file'])&& isset($post['name'])&& isset($post['id'])){
			$pf = $this->session->userdata('PROCESS_FILE');
			if(!@$pf || !array_key_exists('CURRENT_PATH', $pf)){
				echoJSON(MISSING_PARAM,'Thiếu thông tin dữ liệu!');
			}
			else{
				$currentpath  = $pf['CURRENT_PATH'];
				$this->save_image($post['file'],FCPATH.$currentpath.$post['name']);
				if(file_exists(FCPATH.$currentpath.$post['name'])){
					$fileuploaded = $currentpath.$post['name'];
					$arrSizes = $this->getSizes($fileuploaded);
	        		if(count($arrSizes)>0){
	        			$new_image = '';
	        			foreach ($arrSizes as $size) {
	        				$new_image = $this->resizeImage($currentpath,$post['name'],$size['width'],$size['height'],$size['quality'],$size['name']);
	        			}
	        			//array_push($images,$new_image);
	        		}
	        		$ret = $this->updateImageMedia($currentpath,$post['name'],$post['id'],$pf['CURRENT_ID']);
				}
				echoJSON(SUCCESS,'Đã cập nhật!');
			}
		}
		else{
			echoJSON(MISSING_PARAM,'Thiếu thông tin dữ liệu!');
		}
	}
	public function getDetailFile(){
		if(!$this->checkPermisstionAccess($this->table,'view'))
		{
			return;
		}
		$post =$this->input->post();
		if(@$post && isset($post['id'])){
			$file = $this->Mediadao->getSingleMedia($post['id']);
			if(count($file)>0){
				$this->load->view('v2/media/modalinfo',array('file'=>$file[0]));
			}
		}
	}
	public function saveDetailFile(){
		$post = $this->input->post();
		if(@$post && isset($post['id'])){
			$data['caption'] = isset($post['caption'])?$post['caption']:'';
			$data['alt'] = isset($post['alt'])?$post['alt']:'';
			$data['title'] = isset($post['title'])?$post['title']:'';
			$data['description'] = isset($post['description'])?$post['description']:'';
			$resultHook = $this->hooks->call_hook(['tech5s_media_save_detail_file','data'=>$data,'post'=>$post]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			$ret = $this->Mediadao->updateMedia($data,$post['id']);
			if($ret){
				echoJSON(SUCCESS,'Thành công');
			}
			else{
				echoJSON(ERROR,'Thất bại');
			}
		}else{
			echoJSON(MISSING_PARAM,'Thiếu thông tin dữ liệu!');
		}
	}
	private function checkPermisstionAccess($table,$action){
		$this->testLoginAdmin();
		if(strlen($table)>0 && $this->Admindao->getExistTable($table)>0){
			$id = $this->uri->segment(4,'');
			$updateMyInfo = $table=='nuy_user' && getAdminUserId() ==$id && $id!='';
			$fromsv = isAdminServer();
			$havePermission = $this->Admindao->checkPermissionAction($table,$action) || $updateMyInfo ||$fromsv;
			$resultHook = $this->hooks->call_hook(['tech5s_check_permission_access','havePermission'=>$havePermission,'table'=>$table,'action'=>$action]);
			if(!is_bool($resultHook)){
				extract($resultHook);
			}
			if($havePermission)
			{
				return true;
			}
			else{
				$data['notify'] = 'Bạn không có quyền thực hiện tác vụ này!';
				$data['content']='other/nopermis';
				$this->load->view('template',$data);
			}
		}
		else{
			$data['notify'] = 'Thiếu thông tin dữ liệu!';
			$data['content']='other/nopermis';
			$this->load->view('template',$data);
		}
		return false;
	}
	private function testLoginAdmin(){
		if(!adminIsLogged()){
			redirect('Techsystem/login');
		}
	}
	private function readMetadataPel($path){} 
	private function writeMetadataPel($path,$ins){}
	public function getMedatata(){}
	public function writeMetadata(){}
	public function restore(){
		if(!$this->checkPermisstionAccess($this->table,'delete'))
		{
			return;
		}
		$post = $this->input->postf();
		if(@$post && isset($post['id'])){
			$id = $post['id'];
			$dir = $this->Mediadao->getSingleMedia($id);
			if(count($dir)>0){
				$dir = $dir[0];
				$resultHook = $this->hooks->call_hook(['tech5s_media_before_restore','dir'=>$dir]);
				if(!is_bool($resultHook)){
					extract($resultHook);
				}
				if($dir['is_file']==0){
					$this->_restoreAll($id);
				}
				$this->Mediadao->updateMedia(['trash'=>0],$id);
				return $this->outputJson($id.'');
			}
			return $this->outputJson('Xóa Folder Thất bại!',ERROR);
		}
		else{
			return $this->outputJson('Thiếu thông tin dữ liệu!',MISSING_PARAM);
		}
	}
}
?>