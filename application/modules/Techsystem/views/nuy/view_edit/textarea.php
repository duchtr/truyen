<!-- {
    "target": "this|name",
    "event": "keyup",
    "function": "count|slug"
} -->
	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<?php echo __('note',$field); ?>
		</div>
		<div class="col-md-10 col-xs-12">
			<textarea name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>" style="visibility:visible !important" rows="4"><?php echo isset($dataitem)?$dataitem[$field['name']]:"" ?></textarea>
		</div>
	</div>
	<?php 
	$referer = (@$field['referer'] && !isNull($field['referer']))? json_decode($field['referer']):"";
	if(is_array($referer)){
		foreach ($referer as $key => $value) {
			?>
			<script type="text/javascript">
			$(function() {
				$('body').on('<?php echo $value->event ?>', "textarea[name=<?php echo $field['name'] ?>]", function(event) {
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
	?>