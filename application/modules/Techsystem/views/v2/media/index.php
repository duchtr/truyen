<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<meta charset="utf-8">
	<base href="<?php echo base_url(); ?>">
	<link href="theme/admin/v2/media/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
	<link href="theme/admin/v2/media/css/font-awesome.css" type="text/css" rel="stylesheet" />
	<link href="theme/admin/v2/media/fancybox/jquery.fancybox.css" type="text/css" rel="stylesheet" />
	<link href="theme/admin/v2/media/fancybox/helpers/jquery.fancybox-thumbs.css" type="text/css" rel="stylesheet" />
	<link href="theme/admin/v2/media/scrollbar/jquery.mCustomScrollbar.css" type="text/css" rel="stylesheet" />
	<script src="theme/admin/v2/media/js/jquery-2.2.1.min.js"></script>
	<script src="theme/admin/v2/media/js/bootstrap.min.js"></script>
	<script src="theme/admin/v2/media/fancybox/jquery.fancybox.pack.js"></script>
	<script src="theme/admin/v2/media/fancybox/helpers/jquery.fancybox-thumbs.js"></script>
	<script src="theme/admin/v2/media/js/isotope.pkgd.min.js"></script>
	<script src="theme/admin/v2/media/js/bootbox.min.js"></script>
	<script src="theme/admin/v2/media/js/jquery.endless-scroll.js"></script>
	<script src="theme/admin/v2/media/js/jquery.lazyload.js"></script>
	<script src="theme/admin/v2/media/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
	<link href="theme/admin/v2/media/css/style.css" type="text/css" rel="stylesheet" />
	
	
	<script src="theme/admin/v2/media/js/dropzone.js"></script>
	<script type="text/javascript">
      var globalBaseUrl= "<?php echo base_url().ADMIN; ?>";
      var _baseurl= "<?php echo base_url(); ?>";
      var csrftokenname= '<?php echo $this->security->get_csrf_token_name(); ?>';
      var csrftokenvalue= '<?php echo $this->security->get_csrf_hash(); ?>';
      var extimgs= <?php echo json_encode($this->config->item('ext_img')); ?>;
      var extvideos = <?php echo json_encode($this->config->item('ext_video')); ?>;
      var extfiles = <?php echo json_encode($this->config->item('ext_file')); ?>;
      var extmusic = <?php echo json_encode($this->config->item('ext_music')); ?>;
      var extmisc = <?php echo json_encode($this->config->item('ext_misc')); ?>;
      var SUCCESS = 200;
      var globalobj ={};
      globalobj[csrftokenname] = csrftokenvalue;
      $.ajaxSetup({
      	data: globalobj
      });
      var globalObjectFile = undefined;
      var MEDIA_PER_PAGE= <?php echo MEDIA_PER_PAGE; ?>;
      var is_trash_view = <?php echo isset($trash) && $trash == 1?'1':'0' ?>;
      </script>
      <script src="theme/admin/v2/media/js/media.js"></script>
      <script src="theme/admin/v2/media/js/script.js"></script>
</head>
<body >
		<div id="media-manage" class="media-manage ">
			<div class="hover-upload">
				<div class="content-upload">
					<img src="theme/admin/v2/media/images/upload.png" alt="">
					<h3>Thả file tại đây để upload</h3>
				</div>
			</div>
			<div class="loading">
				<div class="cssload-loader-inner">
					<div class="cssload-cssload-loader-line-wrap-wrap">
						<div class="cssload-loader-line-wrap"></div>
					</div>
					<div class="cssload-cssload-loader-line-wrap-wrap">
						<div class="cssload-loader-line-wrap"></div>
					</div>
					<div class="cssload-cssload-loader-line-wrap-wrap">
						<div class="cssload-loader-line-wrap"></div>
					</div>
					<div class="cssload-cssload-loader-line-wrap-wrap">
						<div class="cssload-loader-line-wrap"></div>
					</div>
					<div class="cssload-cssload-loader-line-wrap-wrap">
						<div class="cssload-loader-line-wrap"></div>
					</div>
				</div>
			</div>
			<div class="media-bar">
				<div class="row mar-10">
					<div class="col-xs-6 col-xs-push-3  pad-10">
						<form class="media-bar-s">
							<input type="text" value="<?php echo isset($keyword)?$keyword:'' ?>">
							<button type="button"><i class="fa fa-search"></i></button>
						</form>
						<div class="menu-filter">
							<button type="button" data-filter=".file,.fold" data-toggle="tooltip" title="Lọc file"><i class="fa fa-file"></i></button>
							<button type="button" data-filter=".image" data-toggle="tooltip" title="Lọc ảnh"><i class="fa fa-picture-o"></i></button>
							<button type="button" data-filter=".video" data-toggle="tooltip" title="Lọc video"><i class="fa fa-film"></i></button>
							<button type="button" data-filter=".compress" data-toggle="tooltip" title="Lọc file nén"><i class="fa fa-inbox"></i></button>
							<button type="button" data-filter=".audio" data-toggle="tooltip" title="Lọc file âm thanh"><i class="fa fa-music"></i></button>
						</div>
					</div>
					<div class="col-xs-3 col-xs-pull-6 col-xs-6 pad-10">
						<?php 
						    $resultHook = $this->hooks->call_hook(['tech5s_media_top_button']);
						    if(!is_bool($resultHook) && is_array($resultHook)){
						       extract($resultHook);
						   }
					   	?>
						<button type="button" data-toggle="modal" data-target="#upload-modal" data-toggle="tooltip" title="Upload file"><i class="fa fa-upload"></i><span class="hidden-md">Upload</span></button>
						<button type="button" data-toggle="modal" data-target="#folder-modal" data-toggle="tooltip" title="Thêm folder mới"><i class="fa fa-folder-open"></i><span class="hidden-md">New folder</span></button>
						<?php if(isset($trash) && $trash==1): ?>
							<a class="trashview" href="<?php echo ADMIN ?>/Media/media?<?php echo isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:''; ?>"><i class="fa fa-home"></i>&nbsp;<span class="hidden-md">Gốc</span></a>
						<?php else: ?>
							<a class="trashview" href="<?php echo ADMIN ?>/Media/trash?<?php echo isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:''; ?>"><i class="fa fa-trash"></i>&nbsp;<span class="hidden-md">Thùng rác</span></a>
						<?php endif; ?>
						
						
						<?php $this->load->view("v2/media/modalupload") ?>
						<?php $this->load->view("v2/media/newfolder") ?>
					</div>
					<div class="col-xs-3 pad-10">
						<?php if(isset($trash) && $trash == 1): ?>
						<?php else: ?>
						<div class="menu-btn-r">
							<button type="button" onclick="MediaManager.deleteMultiFile()" data-toggle="tooltip" title="Xóa"><i class="fa fa-trash"></i><span class="hidden-md">Delete</span></button>
							<button type="button" onclick="MediaManager.applyChooseImage()" data-toggle="tooltip" title="Thực hiện"><i class="fa fa-check-square-o"></i><span class="hidden-md">Apply</span></button>
						</div>
						<?php endif; ?>
					</div>	
				</div>
			</div>
			<div class="media-bar-b">
				<div class="row">
					<div class="col-sm-9">
						<div class="media-breadcrumb">
							<ul>
								<?php if(!isset($trash) || $trash == 0): ?>
								<?php $prs = getParameter();  ?>
								<li><a href="<?php echo current_url() ?>?<?php echo array_key_exists("istiny", $prs)?'istiny='.$prs['istiny']:'' ?>" title=""><i class="fa fa-home"></i></a></li>
								<?php 
								if(array_key_exists("folder", $prs)){
									$folder =urldecode($prs["folder"]);
									$folders = explode(",", $folder);
									$retfolders = array();
									foreach ($folders as $key => $folder) {
										$f = $this->Mediadao->getSingleMedia($folder);
										if(count($f)>0){
											array_push($retfolders,$f[0]["id"]);
											$prs["folder"] = implode(",", $retfolders);
											echo '<li><a href="'.buildParameter($prs).'" title="">'.$f[0]["name"].'</a></li>';	
										}
										
									}
								}
								 ?>
								<?php endif; ?>
								
								<li><?php  echo @$nums["file"]?$nums["file"]:0 ?> file(s) - <?php echo $nums["folder"]?$nums["folder"]:0 ?> folder(s)</li>
							</ul>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="media-view">
							<button type="button" id="view" data-view="list" data-toggle="tooltip" title="Hiển thị"><i></i></button>
							<div class="btn-group" data-toggle="tooltip" title="Sắp xếp">
							  	<button type="button" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							    	<i class="fa fa-signal"></i><span class="caret"></span>
							  	</button>
							  	<ul class="dropdown-menu media-sort">
							    	<li><a data-sort="titleAsc" href="#">Tên file <i class="fa fa-long-arrow-up"></i></a></li>
							    	<li><a data-sort="titleDesc" href="#">Tên file <i class="fa fa-long-arrow-down"></i></a></li>
							    	<li><a data-sort="dateAsc" href="#">Ngày tạo <i class="fa fa-long-arrow-up"></i></a></li>
							    	<li><a data-sort="dateDesc" href="#">Ngày tạo <i class="fa fa-long-arrow-down"></i></a></li>
							    	<li><a data-sort="sizeAsc" href="#">Dung lượng <i class="fa fa-long-arrow-up"></i></a></li>
							    	<li><a data-sort="sizeDesc" href="#">Dung lượng <i class="fa fa-long-arrow-down"></i></a></li>
							  	</ul>
							</div>
							<button type="button" id="refresh" data-toggle="tooltip" title="Refresh"><i class="fa fa-refresh"></i></button>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="listselected">
			<div class="media-content container-fluid pad-2" style="overflow:hidden">
				<div class="row mar-2">
					<?php $this->load->view("v2/media/media-manager",array("list_data"=>$list_data)); ?>
				</div>
				
			</div>
			<div style="    display: none;text-align: center;background: #00923f;color: #fff;" class="loading-info"><img src="theme/admin/v2/media/images/8.gif" alt=""></div>
		</div>
		<div class="list-notify">
			
			
		</div>
		
	
	<ul class='custom-menu'>
      </ul>
    <div id="snackbar">Some text some message..</div>
    <img src="" class="hidden" alt="" id="aviary-image">
	<div id="info-modal" class="modal fade" role="dialog">
	  	<div class="modal-dialog" style="width:75%">
	    	<div class="modal-content">
	    	<form onsubmit="MediaManager.saveDetailFile(this);return false;" action="<?php echo ADMIN ?>/Media/saveDetailFile" method="post">
		      	<div class="modal-header">
		       		<button type="button" class="close" data-dismiss="modal">&times;</button>
		        	<h4 class="modal-title">Thông tin tệp tin</h4>
		      	</div>
		      	<div class="modal-body text-center">
		      		
		      	</div>
		      	<div class="modal-footer">
		        	<button type="submit" >Save & close</button>
		      	</div>
		      	</form>
	    	</div>
	  	</div>
	</div>
	<div id="advance-modal" class="modal fade" role="dialog">
	  	<div class="modal-dialog" style="width:75%">
	    	<div class="modal-content">
	    	<form onsubmit="MediaManager.writeMetadata(this);return false;" action="<?php echo ADMIN ?>/Media/writeMetadata" method="post">
		      	<div class="modal-header">
		       		<button type="button" class="close" data-dismiss="modal">&times;</button>
		        	<h4 class="modal-title">Thông tin Metadata Ảnh</h4>
		      	</div>
		      	<div class="modal-body text-center">
		      		
		      	</div>
		      	<div class="modal-footer">
		        	<button type="submit" >Save & close</button>
		      	</div>
		      	</form>
	    	</div>
	  	</div>
	</div>
	<?php 
    $resultHook = $this->hooks->call_hook(['tech5s_media_before_body']);
    if(!is_bool($resultHook) && is_array($resultHook)){
       extract($resultHook);
   }
   ?>
</body>
</html>