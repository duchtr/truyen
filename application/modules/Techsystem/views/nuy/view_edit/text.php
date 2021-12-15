<?php /* <!-- {
    "target": "this|name",
    "event": "keyup",
    "function": "count|slug"
} 
--> */ ?>
<?php 
$defaultData = (@$field['default_data'] && !isNull($field['default_data']))? json_decode($field['default_data']):"";
if(!isNull($defaultData)){
	$dataData = $defaultData->data;
	if($dataData->source =='php'){
		$valueData = eval("return ".str_replace("$", "\$", $dataData->value) );		
	}
}
 ?>
	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<?php echo __('note',$field); ?>
		</div>
		<div class="col-md-10 col-xs-12">
			<input type="text" name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>" value="<?php echo isset($dataitem)?$dataitem[$field['name']]:(@$valueData?$valueData:"") ?>">
		</div>
	</div>
	<?php 
	$codeIU = ($type=='edit'?1:($type=='insert'?2:4));
	$referer = (@$field['referer'] && !isNull($field['referer']))? json_decode($field['referer']):"";
	if(is_array($referer) ){
		foreach ($referer as $key => $value) {
			$totalCode = property_exists($value, 'when')?$value->when:7;
			if($codeIU && ($totalCode&$codeIU)){
			?>
			<script type="text/javascript">
			function preview(){
					try{
				      tinyMCE.triggerSave();
				    }
				    catch(ex){
				      
				    }
					var form = $('form[name=addform]').clone();
					$('body').append(form);
					form.prop('action','Vindex/preview');
					form.prop('target',"_blank");
					form.append('<input type="hidden" name="table" value="<?php echo $table['name'];?>"/>');
					form.submit();
					form.remove();
			}
			$(function() {
				<?php if ($value->function =="preview" && $type=='insert'){ ?>
					
					var element = $('input[name=<?php echo $field['name'] ?>]');
					if(element.parent().find('.preview').length <=0){
						element.parent().append('<a onclick="preview();return false;" class="preview"><i class="icon-eye-open"></i>Xem trước bài viết</a>');
					}
					
				<?php } ?> 
				$('body').on('<?php echo $value->event ?>', "input[name=<?php echo $field['name'] ?>]", function(event) {
					<?php if($value->target=='this'){
						?>
						var element = $("input[name=<?php echo $field['name'] ?>]");
						<?php if($value->function == "count"){ ?>
							if(element.parent().find('.count').length <=0){
								element.parent().append('<span class="count"></span>');
							}
							element.parent().find('.count').text(element.val().length+' chars');
						<?php } else if($value->function == "slug"){ ?>
							element.val(locDau(element.val()));
						<?php } ?>
					<?php }
					else{ ?>
							var element = $("input[name=<?php echo $value->target ?>]");
							<?php if($value->function == "count"){ ?>
								if(element.parent().find('.count').length <=0){
									element.parent().append('<span class="count"></span>');
								}
								element.parent().find('.count').text(element.val().length+' chars');
							<?php } else if($value->function == "slug"){ ?>
								element.val(locDau($(this).val()));
							<?php } ?> 
					<?php }
					?>
				});
				
			});
			</script>
			<?php 
		}
		}
	}
	
	?>