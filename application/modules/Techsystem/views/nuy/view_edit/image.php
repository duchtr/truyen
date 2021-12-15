<div class="row margin0">
	<div class="col-md-2 col-xs-12">
		<?php echo __('note',$field) ?>
	</div>
	<div class="col-md-10 col-xs-12">
		<div style="" class="switch">
			<input onchange="" type="hidden" value="<?php echo isset($dataitem)? $dataitem[$field['name']]:'' ?>" name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>">
			<?php $img =  isset($dataitem) ? $dataitem[$field['name']]: 'theme/admin/images/noimage.png';  ?>
			<img style="width:100px;" src="<?php echo isNull($img)?'theme/admin/images/noimage.png': $img; ?>" alt="" class="<?php echo $field['name'] ?>">
			<div class="btnadmin">
	    		<a href="<?php echo base_url() ?>Techsystem/mediaManager?istiny=<?php echo $field['name'] ?>" class="btn iframe-btn" type="button">Browse File ...</a>
	    	</div>
	  	</div>
	</div>
</div>