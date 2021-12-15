<div class="row margin0">
	<div class="col-md-2 col-xs-12">
		<?php echo __('note',$field) ?>
	</div>
	<div class="col-md-10 col-xs-12">
		<div style="" class="switch">
			<input onchange="" type="text" value="<?php echo isset($dataitem)? $dataitem[$field['name']]:'' ?>" readonly name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>">
			<div class="btnadmin">
	    		<a href="<?php echo base_url() ?>Techsystem/Media/media?istiny=<?php echo $field['name'] ?>" class="btn iframe-btn" type="button">Browse File ...</a>
	    	</div>
	  	</div>
	</div>
</div>