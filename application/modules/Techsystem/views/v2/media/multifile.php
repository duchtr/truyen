<?php 
foreach ($infos as $key => $info) {
	$this->load->view("v2/media/file",array("info"=>$info));
}

 ?>