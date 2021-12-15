<div class="row margin0">
	<div class="col-md-2 col-xs-12">
		<?php echo __('note',$field) ?>
	</div>
	<div class="col-md-10 col-xs-12">
		<div style="" class="switch">
	    	<input <?php echo (isset($dataitem) && $dataitem[$field['name']]==1)?'checked':'' ?> onclick="clickkick<?php echo $field['name'] ?>(this)"  id="cmn-toggle-<?php echo $field['name'] ?>"  class="cmn-toggle cmn-toggle-round"  type="checkbox">
	    	<label for="cmn-toggle-<?php echo $field['name'] ?>"></label>
	    	<input type="hidden" name="<?php echo $field['name'] ?>" >
	  	</div>
	</div>
</div>
<script type="text/javascript">
$(function() {
	$('input[name=<?php echo $field['name'] ?>]').val($('#cmn-toggle-<?php echo $field['name'] ?>').is(':checked')?"1":"0")
});
function clickkick<?php echo $field['name'] ?>(_this){
	$('input[name=<?php echo $field['name'] ?>]').val($(_this).is(':checked')?"1":"0");
	
}
</script>