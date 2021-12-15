	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<?php echo __('note',$field); ?>
		</div>
		<?php $value = isset($dataitem)? $dataitem[$field['name']]:''; ?>
		<div class="col-md-10 col-xs-12">
			<textarea class="hidden" name="<?php echo $field['name'] ?>"><?php echo $value;  ?></textarea>
			<?php $jsons = json_decode($value,true);$jsons = @$jsons?$jsons:[];

			 ?>
			 
			<div class="textv2-<?php echo $field['name'] ?>">
				<?php foreach($jsons as $key =>$json){  ?>
						

				<?php 
					$linkimg=array_key_exists("img", $json)?$json['img']:'';
					$img = file_exists($linkimg)?$linkimg: 'theme/admin/images/noimage.png';  
			 		
			 	 ?>
				<div class="item">
					<div class="row">
						<div class="col-md-3">
						<label>Icon</label>
						</div>
						<div class="col-md-9">
						<input onchange="" type="hidden" value='<?php echo $img; ?>' class="img-<?php echo $field['name'];?>" id="img-<?php echo $field['name'];echo $key?>">
						<img style="width:100px;" src="<?php echo $img; ?>" alt="" >
						<div class="btnadmin">
				    		<a href="<?php echo base_url() ?>Techsystem/Media/media?istiny=img-<?php echo $field['name'];echo $key?>" class="btn iframe-btn" type="button">Browse File ...</a>
				    	</div>
				    	<div class="btnadmin">
				    		<a href="#" class="btn btndelete" type="button"><?php echo alang("DELETE") ?></a>
				    	</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
						<label>Tên</label>
						</div>
						<div class="col-md-9">
						<input class="name-<?php echo $field['name'] ?>" value='<?php echo(isset($json['name'])) ?$json['name'] :'' ?>'>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
						<label>Nội dung </label>
						</div>
						<div class="col-md-9">
						<textarea data-height="100" class=" content-<?php echo $field['name'] ?>"><?php echo(isset($json['content'])) ?$json['content'] :''  ?></textarea>
						</div>
					</div>
					<div class="text-center">
					<button type="button" class="delete">Xóa</button>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="text-center" style="width: 80%;">
				<button type="button" class="add-<?php echo $field['name'] ?>"><?php echo alang("ADD") ?></button>
			</div>
		</div>
	</div>
	<style type="text/css">
		.textv2-<?php echo $field['name'] ?>{
		    border: 1px solid #00923f;
			width: 80%;
			margin: 10px 0px;
		}
		.textv2-<?php echo $field['name'] ?> .item{
			margin: 10px;
    border: 1px solid #ccc;
		}
		.textv2-<?php echo $field['name'] ?> .item input, .textv2-<?php echo $field['name'] ?> .item textarea{
			width: 100% !important; 
		}
	</style>
	<script type="text/javascript">
		$(function() {
			loadEditor();
			function loadEditor(){
				if($('.small_editor').length > 0){
					$('.small_editor').each(function(){
						var _this =$(this);
						var dtHeight =_this.attr('data-height');
						tinymce.init({
					        selector:'.small_editor',
					        height: dtHeight,
					        theme: 'modern',
					        paste_data_images: true,
					        plugins: 'lists textcolor',
					        toolbar1: 'formatselect  bold italic strikethrough forecolor  | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
					        image_advtab: true,	
					        branding:false,
					       
					        setup: function (editor) {
					            editor.on('change', function () {
					                editor.save();
					                calculate<?php echo $field['name'] ?>();
					            });
					        }
					    });
					})
				}
			}
			function calculate<?php echo $field['name'] ?>(){
				var textv2 = $(".textv2-<?php echo $field['name'] ?> .item");
				var ret = [];
				for (var i = 0; i < textv2.length; i++) {
					var tmp = {};
					var textv22 =$(textv2[i]) ;
					tmp.img=textv22.find('.img-<?php echo $field['name'] ?>').val();
					tmp.content = textv22.find('.content-<?php echo $field['name'] ?>').val();
					tmp.name = textv22.find('.name-<?php echo $field['name'] ?>').val();
					ret.push(tmp);
					
				}
				
				
				$('textarea[name=<?php echo $field['name'] ?>]').val(JSON.stringify(ret));
			}
			$(document).on('change','.img-<?php echo $field['name'];?>',function(e){
				e.preventDefault();
				var json = JSON.parse($(this).val());
				if(json.path!=undefined && json.file_name !=undefined){
					$(this).val(json.path+json.file_name);
				}
				calculate<?php echo $field['name'] ?>();
			})
			$(document).on('input', '.content-<?php echo $field['name'] ?>,.name-<?php echo $field['name'] ?>', function(event) {
				event.preventDefault();
				calculate<?php echo $field['name'] ?>();
			});
			$(document).on('click', '.textv2-<?php echo $field['name'] ?> .item .delete', function(event) {
				event.preventDefault();
				$(this).parents('.item').remove();
				calculate<?php echo $field['name'] ?>();
			});
			$(document).on('click', '.textv2-<?php echo $field['name'] ?> .btndelete', function(event) {
				event.preventDefault();
				$(this).parent().parent().children('img').attr('src','theme/admin/images/noimage.png');
				$(this).parent().parent().children('input').val('theme/admin/images/noimage.png');
				calculate<?php echo $field['name'] ?>();
				
			});
			
			$(".add-<?php echo $field['name'] ?>").click(function(event) {
				event.preventDefault();
				var name = Math.random().toString(36).substring(7);
				var str = '<div class="item">'
					+'<div class="row">'
						+'<div class="col-md-3">'
						+'<label>Icon</label>'
						+'</div>'
						+'<div class="col-md-9">'
						+'<input onchange="" type="hidden" value="" class="img-<?php echo $field['name'];?>" id="'+name+'">'
						+'<img style="width:100px;" src="theme/admin/images/noimage.png" alt="" >'
						+'<div class="btnadmin">'
				    		+'<a href="<?php echo base_url() ?>Techsystem/Media/media?istiny='+name+'" class="btn iframe-btn" type="button">Browse File ...</a>'
				    	+'</div>'
				    	+'<div class="btnadmin">'
				    		+'<a href="#" class="btn btnchange" type="button">Sửa thông tin cơ bản</a>'
				    	+'</div>'
				    	+'<div class="btnadmin">'
				    		+'<a href="#" class="btn btndelete" type="button">Xóa ảnh</a>'
				    	+'</div>'
						+'</div>'
					+'</div>'
					+'<div class="row">'
						+'<div class="col-md-3">'
						+'<label>Tên</label>'
						+'</div>'
						+'<div class="col-md-9">'
						+'<input class="name-<?php echo $field['name'] ?>"></input>'
						+'</div>'
					+'</div>'
					+'<div class="row">'
						+'<div class="col-md-3">'
						+'<label>Nội dung</label>'
						+'</div>'
						+'<div class="col-md-9">'
						+'<textarea data-height="100" class="content-<?php echo $field['name'] ?>" value=""></textarea>'
						+'</div>'
					+'</div>'
					+'<div class="text-center">'
					+'<button type="button" class="delete">Xóa</button>'
					+'</div>'
				+'</div>';
				$('.textv2-<?php echo $field['name'] ?>').append(str);
				loadEditor();
			});
		});
	</script>