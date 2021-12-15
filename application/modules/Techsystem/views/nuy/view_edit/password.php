	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<?php echo __('note',$field); ?>
		</div>
		<div class="col-md-10 col-xs-12">
			<input type="text" name="<?php echo $field['name'] ?>" value="<?php echo isset($dataitem)?$dataitem[$field['name']]:(@$valueData?$valueData:"") ?>">
		</div>
	</div>
	<script type="text/javascript">
	$(function() {
		$('input[name=<?php echo $field['name'] ?>]').change(function(event) {
			var val = $(this).val();
			if(val.indexOf('$2a$')==0 && val.length ==60){
				/*Đây là mật khẩu*/
			}
			else{
				
				$.ajax({
					url: 'Techsystem/getPasswordEncrypt',
					type: 'POST',
					data: {pass:val},
					global:false
				})
				.done(function(e) {
					console.log(e);
					try{
						var json = $.parseJSON(e);
						$('input[name=<?php echo $field['name'] ?>]').val(json.message);	
					}
					catch(ex){
					}
					
				})
				.fail(function(e) {
					console.log(e);
				})
				.always(function() {
					console.log("complete");
				});
				
			}
		});
	});
	</script>