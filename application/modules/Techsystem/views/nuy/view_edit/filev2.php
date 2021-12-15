<div class="row margin0">
	<div class="col-md-2 col-xs-12">
		<?php echo __('note',$field) ?>
	</div>
	<div class="col-md-10 col-xs-12">
		<div style="" class="switch">
			<input onchange="changeShow<?php echo $field['name'] ?>(this)" type="hidden" value='<?php echo isset($dataitem)? $dataitem[$field['name']]:'' ?>' name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>">
			<?php 
			$tmp = isset($dataitem) ? $dataitem[$field['name']]:"";
			$tmp = json_decode($tmp,true);
			$img = isset($tmp) && is_array($tmp) ?$tmp["path"].$tmp["file_name"]:'';  
			?>
			<input oninput="changeHide<?php echo $field['name'] ?>(this)" style="width:300px;" value="<?php echo $img ?>" type="text" alt="" class="<?php echo $field['name'] ?>">
			<div class="btnadmin">
	    		<a href="<?php echo base_url() ?>Techsystem/Media/media?istiny=<?php echo $field['name'] ?>" class="btn iframe-btn" type="button">Browse File ...</a>
	    	</div>
	  	</div>
	</div>
</div>
<script type="text/javascript">
	function changeShow<?php echo $field['name'] ?>(_this){
		var val = $(_this).val();
		try{
			var val = JSON.parse(val);
			$(_this).next(val.path+val.file_name);
		}
		catch{}
	}
	function changeHide<?php echo $field['name'] ?>(_this){
		var val = $(_this).val();
		try{
			if(val.trim().length==0){
				$(_this).prev().val("");
			}
		}
		catch{}
	}
</script>