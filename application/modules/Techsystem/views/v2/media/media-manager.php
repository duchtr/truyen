<?php foreach ($list_data as $file) {
						$subdata["info"] = $file;
						if($file["is_file"]){
							$this->load->view("v2/media/file",$subdata);
						}
						else{
							$this->load->view("v2/media/folder",$subdata);
						}
					} ?>