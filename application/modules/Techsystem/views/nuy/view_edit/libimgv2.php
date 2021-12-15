	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<?php echo __('note',$field); ?>
		</div>
		<div class="col-md-10 col-xs-12 boxlistimg">
			
			<input onchange="" type="hidden" data-name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>_single">
			<div class="libimgv2 libimg libimgv2-<?php echo $field['name'] ?>" data-type="libimgv2">
				<?php $listimg = isset($dataitem)? $dataitem[$field['name']]:'' ;
				if(!isNull($listimg)){
					$listimg = json_decode($listimg,true);
					if(is_array($listimg)){
						foreach ($listimg as $key => $value) {
							if(is_string($value)){
								$val = json_decode($value,true);
								$val = isset($val)?$val:array("path"=>"","file_name"=>"");	
							}else{
								$val = $value;
							}
							
							echo '<div class="boximgv2 boximg"><i onclick="var ax=$(this).parent().parent();$(this).parent().remove();changeListImageV2(ax,\''.$field['name'].'\');" class="icon-remove-circle"></i><img';
							echo " data-file='".json_encode($val)."' " ;
							echo 'style="height:85px" src="'.$val["path"].$val["file_name"].'"/>';
							echo '<i onclick="$(this).parent().moveDown();changeListImageV2($(this).parent().parent(),\''.$field['name'].'\');" class="icon-arrow-right" style="position:absolute;right:-15px;top:50%;    color: #810D0D;font-size: 30px;transform: translateY(-50%);"></i>';
							echo '<i onclick="$(this).parent().moveUp();changeListImageV2($(this).parent().parent(),\''.$field['name'].'\');" class="icon-arrow-left" style="position:absolute;left:-15px;top:50%;    color: #810D0D;font-size: 30px;transform: translateY(-50%);"></i>';
							echo '</div>';	
						}
					}
				}
				
				?>
			</div>
			<input type="hidden" name="<?php echo $field['name'] ?>" value ='<?php echo htmlspecialchars(isset($dataitem)? $dataitem[$field['name']]:'') ?>'>
			<div class="btnadmin">
		    	<a href="<?php echo base_url() ?>Techsystem/Media/media?istiny=<?php echo $field['name'] ?>_single" class="btn iframe-btn" type="button">Browse File ...</a>
		    </div>
		  	
		</div>
	</div>
	<script type="text/javascript">
		$(function() {
			$(".libimgv2-<?php echo $field['name'] ?>").on('click', '.boximgv2 img', function(event) {
				event.preventDefault();
				var _this = this;
				var file = $(_this).attr("data-file");
				file = JSON.parse(file);
				var dialog = bootbox.dialog({
					title:"Chỉnh sửa thông tin ảnh",
					onEscape:true,
				    message: '<div class="row">'
							+'<div class="col-md-6 col-xs-12 form-group">'
							+'	<label for="">Title</label>'
							+'	<input name="title" type="text" class="form-control" value="'+file.title+'" placeholder="Title">'
							+'	</div>'
							+'	<div class="col-md-6 col-xs-12 form-group">'
							+'		<label for="">Caption</label>'
							+'		<input name="caption" type="text" class="form-control" value="'+file.caption+'" placeholder="Caption">'
							+'	</div>'
							+'	<div class="col-md-6 col-xs-12 form-group">'
							+'		<label for="">Alt</label>'
							+'		<input name="alt" type="text" class="form-control" value="'+file.alt+'" placeholder="Alt">'
							+'	</div>'
							+'	<div class="col-md-6 col-xs-12 form-group">'
							+'		<label for="">Description</label>'
							+'		<input name="description" type="text" class="form-control" value="'+file.description+'" placeholder="Description">'
							+'	</div>'
							+'</div>',
					buttons: {
				        confirm: {
				            label: 'Lưu',
				            className: 'btn-success',
				            callback: function() {
  								file.title = dialog.find("input[name=title]").val();
  								file.caption = dialog.find("input[name=caption]").val();
  								file.alt = dialog.find("input[name=alt]").val();
  								file.description = dialog.find("input[name=description]").val();
  								$(_this).attr("data-file",JSON.stringify(file));
  								changeListImageV2(".libimgv2-<?php echo $field['name'] ?>","<?php echo $field['name'] ?>");
  							}
				        },
				        cancel: {
				            label: 'Thoát',
				            callback:function(){
				            	dialog.modal("hide");
				            }
				        }
				    },
				});
				dialog.modal("show");
			});
		});
	</script>