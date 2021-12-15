<?php
$langs = $this->db->query('select ext from nuy_table where name = "configs"')->result_array();
$langs = array_filter(explode(',', count($langs) > 0 ? $langs[0]['ext'] : ['vi']));
?>
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
				<div class="item">
					<?php foreach($langs as $v){ if($v == 'vi') $v = ''; ?>
					<div class="row">
						<div class="col-md-3">
						<label>Tên(<?php echo $v == '' ? 'vn' : $v; ?>)</label>
						</div>
						<div class="col-md-9">
						<input class="name_<?php echo $v ?>-<?php echo $field['name'] ?>" value='<?php echo(isset($json['name'.($v != '' ? '_'.$v : $v)])) ?$json['name'.($v != '' ? '_'.$v : $v)] :'' ?>'>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
						<label>Nội dung(<?php echo $v == '' ? 'vn' : $v; ?>)</label>
						</div>
						<div class="col-md-9">
						<textarea data-height="100" class=" content_<?php echo $v ?>-<?php echo $field['name'] ?>"><?php echo(isset($json['content'.($v != '' ? '_'.$v : $v)])) ?$json['content'.($v != '' ? '_'.$v : $v)] :''  ?></textarea>
						</div>
					</div>
					<?php } ?>
					<div class="text-center">
					<button type="button" class="delete"><?php echo alang("DELETE") ?></button>
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
					<?php foreach($langs as $v){ if($v == 'vi') $v = ''; ?>
					tmp.content<?php echo ($v != '' ? '_'.$v : $v); ?> = textv22.find('.content_<?php echo $v; ?>-<?php echo $field['name'] ?>').val();
					tmp.name<?php echo ($v != '' ? '_'.$v : $v); ?> = textv22.find('.name_<?php echo $v; ?>-<?php echo $field['name'] ?>').val();
					<?php } ?>
					ret.push(tmp);
				}
				
				/*console.log(JSON.stringify(ret));*/
				$('textarea[name=<?php echo $field['name'] ?>]').val(JSON.stringify(ret));
			}
			<?php foreach($langs as $v){ if($v == 'vi') $v = ''; ?>
			$(document).on('input', '.name_<?php echo $v; ?>-<?php echo $field['name'] ?>', function(event) {
				event.preventDefault();
				calculate<?php echo $field['name'] ?>();
			});
			$(document).on('input', '.content_<?php echo $v; ?>-<?php echo $field['name'] ?>', function(event) {
				event.preventDefault();
				calculate<?php echo $field['name'] ?>();
			});
			<?php } ?>
			$(document).on('click', '.textv2-<?php echo $field['name'] ?> .item .delete', function(event) {
				event.preventDefault();
				$(this).parents('.item').remove();
				calculate<?php echo $field['name'] ?>();
			});
			
			$(".add-<?php echo $field['name'] ?>").click(function(event) {
				event.preventDefault();
				var name = Math.random().toString(36).substring(7);
				var str = '<div class="item">'
					<?php
					foreach($langs as $v){
						if($v == 'vi') $v = '';
					?>
					+'<div class="row">'
						+'<div class="col-md-3">'
						+'<label>Tên(<?php echo $v == '' ? 'vn' : $v; ?>)</label>'
						+'</div>'
						+'<div class="col-md-9">'
						+'<input class="name_<?php echo $v ?>-<?php echo $field['name'] ?>"></input>'
						+'</div>'
					+'</div>'
					+'<div class="row">'
						+'<div class="col-md-3">'
						+'<label>Nội dung(<?php echo $v == '' ? 'vn' : $v; ?>)</label>'
						+'</div>'
						+'<div class="col-md-9">'
						+'<textarea data-height="100" class="content_<?php echo $v ?>-<?php echo $field['name'] ?>" value=""></textarea>'
						+'</div>'
					+'</div>'
					<?php } ?>
					+'<div class="text-center">'
					+'<button type="button" class="delete">Xóa</button>'
					+'</div>'
				+'</div>';
				$('.textv2-<?php echo $field['name'] ?>').append(str);
				loadEditor();
			});
		});
	</script>