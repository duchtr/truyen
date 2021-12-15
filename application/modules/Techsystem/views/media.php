<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head >
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
          <base href="<?php echo base_url() ?>"/>
      <title><?php echo lang("ADMIN_FILE_MANAGER") ?></title>
      <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>theme/admin/static/bootstrap.css">
      <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>theme/admin/static/font-awesome.css">
      <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>theme/admin/static/fancybox/source/jquery.fancybox.css">
      <link rel="stylesheet" href="theme/admin/static/style_media.css" />
       <script type="text/javascript">
      var globalFullUrl= "<?php echo current_full_url(); ?>";
      var globalBaseUrl= "<?php echo base_url(); ?>";
      var csrftokenname= '<?php echo $this->security->get_csrf_token_name(); ?>';
      var csrftokenvalue= '<?php echo $this->security->get_csrf_hash(); ?>';
      var extimgs= <?php echo json_encode($this->config->item('ext_img')); ?>;
      var extvideos = <?php echo json_encode($this->config->item('ext_video')); ?>;
      var extfiles = <?php echo json_encode($this->config->item('ext_file')); ?>;
      var extmusic = <?php echo json_encode($this->config->item('ext_music')); ?>;
      var extmisc = <?php echo json_encode($this->config->item('ext_misc')); ?>;
      
      </script>
      <script type="text/javascript" src="<?php echo base_url(); ?>theme/admin/static/jquery.min.js"></script>
      <script type="text/javascript" src="<?php echo base_url(); ?>theme/admin/static/bootstrap.min.js"></script>
      <script type="text/javascript" src="<?php echo base_url(); ?>theme/admin/static/bootbox.min.js"></script>
      <script type="text/javascript" src="<?php echo base_url(); ?>theme/admin/static/fancybox/source/jquery.fancybox.pack.js"></script>
      <script type="text/javascript" src="<?php echo base_url(); ?>theme/admin/static/jquery.form.js"></script>
      <script type="text/javascript" src="<?php echo base_url() ?>theme/admin/static/jquery.lazyload.js"></script>
      <script type="text/javascript" src="<?php echo base_url() ?>public/tinymce/tinymce.min.js"></script>
      <script type="text/javascript" src="<?php echo base_url() ?>theme/admin/static/mainjs_media.js"></script>
   </head>
   <body >
      <div class="container-fluid">
        <div class="navbar">
          <div class="tools col-md-3 col-xs-3">
            <a data-toggle="modal" data-target="#uploadfile" href="" style="padding: 2px;" class="uploads tool" ><i class="icon-upload-alt"></i> Upload file</a> 
            <!-- <a href="" title="" class="newfile tool"><i class="icon-plus"></i><i class="icon-file"></i></a> -->
            <a href="" title="Thêm folder mới" class="newfolder tool"><i class="icon-plus"></i><i class="icon-folder-open"></i></a>
          </div>
          <div class="col-md-3 col-xs-3 views">
            <!--<a href="" class="view tool"><i class="icon-th-list"></i></a>
            <a href="" class="view tool"><i class="icon-th"></i></a>-->
          </div>
          <div class="col-md-6 col-xs-6 filters">
            <a href="" title="Lọc file" class="filter tool files"><i class="icon-file"></i></a>
            <a href="" title="Lọc ảnh" class="filter tool images"><i class="icon-picture"></i></a>
            <a href="" title="Lọc file nén" class="filter tool archive"><i class="icon-inbox"></i></a>
            <a href="" title="Lọc Video" class="filter tool videos"><i class="icon-film"></i></a>
            <a href="" title="Lọc file âm thanh" class="filter tool musics"><i class="icon-music"></i></a>
            <input type="text" name="textsearch" />
            <button class="search"><i class="icon-search "></i></button>
          </div>
        </div>
        <div class="content">
          <div class="breadcrumb aclr">
            <div class="detail-breadcrumb fl">
              <ul class="aclr fl">
                <li class="fl"><a href="Techsystem/mediaManager"><i class="icon-home"></i></a>/</li>
                <?php 
                $pf = $this->session->userdata('PROCESS_FILE');
                if(@$pf && array_key_exists('CURRENT_PATH', $pf)){
                  $path_uploads  = $pf['CURRENT_PATH'];
                }
                if(!isNull($path_uploads)){
                  $arrPaths = explode("/", $path_uploads);
                  $pathBreadcrumb = base_url()."Techsystem/mediaManager";
                  for($i=0;$i<count($arrPaths);$i++){
                    $value = $arrPaths[$i];
                    if(isNull($value)) continue;
                    if($i==1){
                      $pathBreadcrumb.="?folder=".$value;
                    }
                    else if($i>1){
                      $pathBreadcrumb.=",".$value;
                    }
                    else{
                    }
                    echo "<li class='fl'><a href='".$pathBreadcrumb."'>".$value."</a>/</li>";
                  }
                }
                 ?>
              </ul>
              <p class="info fl"><span class="countfiles"><?php echo $countFile; ?> file</span>&nbsp;-&nbsp;<span class="countfolders"><?php echo $countFolder; ?>(folders)</span></p>
            </div>
            <div class="moretools fr">
              <div class="dropdown">
                <a class="btn dropdown-toggle sorting-btn " data-toggle="dropdown" href="#">
                  <i class="icon-signal"></i>
                  <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li><a href="Techsystem/setOrderingFile/filedown">Tên file<i class="fa fa-long-arrow-down"></i></a></li>
                  <li><a href="Techsystem/setOrderingFile/fileup">Tên file<i class="fa fa-long-arrow-up"></i></a></li>
                  <li><a href="Techsystem/setOrderingFile/datedown">Ngày tạo<i class="fa fa-long-arrow-down"></i></a></li>
                  <li><a href="Techsystem/setOrderingFile/dateup">Ngày tạo<i class="fa fa-long-arrow-up"></i></a></li>
                  <li><a href="Techsystem/setOrderingFile/sizedown">Dung lượng<i class="fa fa-long-arrow-down"></i></a></li>
                  <li><a href="Techsystem/setOrderingFile/sizeup">Dung lượng<i class="fa fa-long-arrow-up"></i></a></li>
                </ul>
              </div>
              <a class="refreshnow" href="Techsystem/mediaManager">
                <i class="icon-refresh"></i>
              </a>
            </div>
          </div>
          <div class="listfiles">
            <ul class="aclr">
              <?php 
              foreach ($folders as $obj) {
               
                if(!$obj->isfile){
                  ?>
                  <li data-file='<?php echo json_encode($obj); ?>' class="col-md-2 col-sm-3 col-xs-4 fileitem">
                    <div class="boximgfile">
                      <img class="img-responsive margin0auto" src="<?php echo base_url() ?>/theme/admin/images/ico/folder.png">
                      
                        <p><?php echo $obj->name; ?></p>
                        <ul class="col-xs-12 filtercontrol">
                          <li class="col-xs-6"><i dt-name="<?php echo $obj->name; ?>"  class="icon-pencil editname"></i></li>
                          <li class="col-xs-6"><i dt-name="<?php echo $obj->name; ?>"  class="icon-trash delete"></i></li>
                        </ul>
                      </div>
                    </li>
                <?php }
                else{ ?>
                    <li data-file='<?php echo json_encode($obj); ?>' class="col-md-2 col-sm-3 col-xs-4 fileitem">
                      <div class="boximgfile">
                        <input id="<?php echo replaceURL($obj->name) ?>" class="checkbox-custom" name="cbchooseimage[]" type="checkbox">
                        <label for="<?php echo replaceURL($obj->name) ?>" class="checkbox-custom-label"></label>
                        <img id="img-<?php echo replaceURL($obj->name) ?>" class="img-responsive margin0auto lazy" dt-path="<?php echo $obj->path; ?>" src="theme/admin/images/362.GIF" data-original="<?php echo $obj->thumb ?>">
                        
                          <p><?php echo $obj->name; ?></p>
                          <ul class="col-xs-12 filtercontrol">
                            <li class="col-xs-3"><i dt-name="<?php echo $obj->name; ?>" class="icon-download download"></i></li>
                            <li class="col-xs-3"><a class="fancybox" href="<?php echo $obj->path; ?>" data-fancybox-group="gallery" title="<?php echo $obj->name ?>"><i dt-name="<?php echo $obj->name; ?>"  class="icon-eye-open preview"></i></a></li>
                            <li class="col-xs-3"><i dt-name="<?php echo $obj->name; ?>"  class="icon-pencil editname"></i></li>
                            <li class="col-xs-3"><i dt-name="<?php echo $obj->name; ?>"  class="icon-trash delete"></i></li>
                          </ul>
                      </div>
                    </li>
                <?php }
              }
              ?>
              
            </ul>
          </div>
        </div>
      </div>
      <div id="uploadfile" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="margin-top: 7%;">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Upload File</h4>
            </div>
            <div class="modal-body">
              <form action="Techsystem/uploadMultiFile" id="uploadmultifile" method="post" enctype="multipart/form-data" style="    text-align: center;">
               <p>Bạn có thể kéo thả file trực tiếp hoặc upload nhiều file (Khuyến cáo <b><</b> 10 file cùng 1 lúc)</p>
                <input name="browsefile[]" type="file" multiple class="hidden opennow"/>
<div class="alo-phone alo-green alo-show">
            <div class="alo-ph-circle"></div>
            <div class="alo-ph-circle-fill"></div>
            <div class="alo-ph-img-circle"><button name="shortbrow" type="button" class="shortbrow" onclick="$('.opennow').click();" ><i class="icon-upload-alt"></i></button></div>
        </div>
                <p class="countfile"></p>
                <input type="hidden" name="field" value="browsefile"/>
                <input name="startupload" type="submit" value="Upload file"/>
             </form>
             <div class="progressbar" style="width:0px;"><span>100%</span></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" onclick="window.location.href=globalFullUrl" data-dismiss="modal">Save & Close</button>
            </div>
          </div>
        </div>
      </div>
      <ul class='custom-menu'>
      </ul>
      <img src="" id="aviary-image" class="hidden">
      <button class="apply">Apply</button>
      <button class="deleteAll">Delete File</button>
       <div id="listfolder" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Upload File</h4>
              </div>
              <div class="modal-body">
                
                  <?php 
                  printAllDirUpload($paths,$allFolders,-1);
                  ?>
               <div class="progressbar" style="width:0px;"><span>100%</span></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" id="copyfile"  data-dismiss="modal">Copy</button>
                <button type="button" class="btn btn-default" id="movefile"  data-dismiss="modal">Move</button>
              </div>
            </div>
          </div>
      </div>
      <div id="bg-load" style="display:none;z-index: 9999; background-color: rgb(0, 0, 0); position: fixed; height: 100%; width: 100%; opacity: 0.5; top: 0; left: 0;">
         <div class="loader">
            <div class="miniloader"></div>
         </div>
      </div>
      
                      
   </body>
</html>