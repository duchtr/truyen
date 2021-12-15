<?php 
	$gt = (isset($datasearch) && array_key_exists("search_".$value['name'], $datasearch))?$datasearch["search_".$value['name']] : "";
	 ?>
<div class="col-md-4 col-xs-12 itemsearch">
	<span><?php echo __('note',$value) ?>: </span>
	<div style="margin-left: 10px;" class="switch">
		<input type="hidden" name="<?php echo 'search_'.$value['name'] ?>" >
    	<input <?php echo $gt==1?'checked':'' ?> onclick="clickkick<?php echo "search_".$value['name'] ?>(this)" id="search-cmn-toggle-<?php echo $value['name'].((@$is_dialog && $is_dialog==1)?"-searchable":"") ?>" class="cmn-toggle cmn-toggle-round" style="  flex: inherit;" type="checkbox">
    	<label for="search-cmn-toggle-<?php echo $value['name'].((@$is_dialog && $is_dialog==1)?"-searchable":"") ?>"></label>
  	</div>
	
</div>
	<script type="text/javascript">
	$(function() {
		<?php $idcheckbox = $value['name'].((@$is_dialog && $is_dialog==1)?'-searchable':''); ?>
		$('input[name=<?php echo "search_".$value['name'] ?>]').val($("#search-cmn-toggle-<?php echo $idcheckbox ?>").is(':checked')?"1":"0")
	});
	function clickkick<?php echo "search_".$value['name'] ?>(_this){
		$('input[name=<?php echo "search_".$value['name'] ?>]').val($(_this).is(':checked')?"1":"0");
		
	}
	</script>