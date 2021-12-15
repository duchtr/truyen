<div class="row margin0">
	<div class="col-md-2 col-xs-12">
		<?php echo __('note',$field) ?>
	</div>
	<div class="col-md-10 col-xs-12">
		<textarea class="editor" name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>" style="visibility:visible !important" rows="4"><?php echo isset($dataitem)? $dataitem[$field['name']]:'' ?></textarea>
	</div>
</div>
