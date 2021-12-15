	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<?php echo __('note',$field) ?>
		</div>
		<div class="col-md-10 col-xs-12">
			<div style="" class="switch">
				<input onchange="" type="hidden" value='<?php echo isset($dataitem)? $dataitem[$field['name']]:'' ?>' name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>">
				<?php 
				$tmp = isset($dataitem) ? $dataitem[$field['name']]:"";
				
				$tmp = json_decode($tmp,true);
				$img = isset($tmp) && is_array($tmp) ?$tmp["path"].$tmp["file_name"]:base_url().'theme/admin/images/noimage.png';  
				?>
				<img style="width:100px;" src="<?php echo $img ?>" alt="" class="<?php echo $field['name'] ?>">
				<div class="btnadmin">
		    		<a href="<?php echo base_url() ?>Techsystem/Media/media?istiny=<?php echo $field['name'] ?>" class="btn iframe-btn" type="button">Browse File ...</a>
		    	</div>
		    	<div class="btnadmin">
		    		<a href="#" class="btn btnchange-<?php echo $field['name'] ?>" type="button">Sửa thông tin cơ bản</a>
		    	</div>
		    	<div class="btnadmin">
		    		<a href="#" class="btn btndelete-<?php echo $field['name'] ?>" type="button">Xóa ảnh</a>
		    	</div>
		  	</div>
		</div>
	</div>
	<script type="text/javascript">
		$(function() {
			$(".btnchange-<?php echo $field['name'] ?>").click(function(event) {
				event.preventDefault();
				var file = $('input[name=<?php echo $field['name'] ?>]').val();
				file = JSON.parse(file);
				var dialog = bootbox.dialog({
					title:"Chỉnh sửa thông tin ảnh",
					onEscape:true,
				    message: '<div class="row">'
							+'<div class="col-md-6 col-xs-12 form-group">'
							+'	<label for="">Title</label>'
							+'	<input name="title" type="text" class="form-control" value="'+(file.title==null?'':file.title)+'" placeholder="Title">'
							+'	</div>'
							+'	<div class="col-md-6 col-xs-12 form-group">'
							+'		<label for="">Caption</label>'
							+'		<input name="caption" type="text" class="form-control" value="'+(file.caption==null?'':file.caption)+'" placeholder="Caption">'
							+'	</div>'
							+'	<div class="col-md-6 col-xs-12 form-group">'
							+'		<label for="">Alt</label>'
							+'		<input name="alt" type="text" class="form-control" value="'+(file.alt==null?'':file.alt)+'" placeholder="Alt">'
							+'	</div>'
							+'	<div class="col-md-6 col-xs-12 form-group">'
							+'		<label for="">Description</label>'
							+'		<input name="description" type="text" class="form-control" value="'+(file.description==null?'':file.description)+'" placeholder="Description">'
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
  								$('input[name=<?php echo $field['name'] ?>]').val(JSON.stringify(file));
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
			$(".btndelete-<?php echo $field['name'] ?>").click(function(event) {
				event.preventDefault();
				$("input[name='<?php echo $field['name'] ?>']").val("");
				$("img.<?php echo $field['name'] ?>").attr("src","");
			});
		});
	</script>