<?php 

	$gtfrom = (isset($datasearch) && array_key_exists("search_".$value['name']."_from", $datasearch))?$datasearch["search_".$value['name']."_from"] : "";

	$gtto = (isset($datasearch) && array_key_exists("search_".$value['name']."_to", $datasearch))?$datasearch["search_".$value['name']."_to"] : "";

	

?>

<div class="col-md-4 col-xs-12 itemsearch">

	<span><?php echo __('note',$value)?>: </span>

	<input value="<?php echo !isNull($gtfrom)? date('d-m-Y H:i:s',$gtfrom):'' ?>" class="datepicker" value="" type="text" placeholder="<?php echo alang('FROM') ?> (<?php echo $value['note'] ?>)" onkeydown="">

	<input value="<?php echo !isNull($gtfrom)?$gtfrom:'' ?>" type="hidden" name="search_<?php echo $value['name']?>_from">

	<span>=></span>

	<input value="<?php echo !isNull($gtto)?date('d-m-Y H:i:s',$gtto):'' ?>" class="datepicker" value=""  type="text" placeholder="<?php echo alang('TO') ?> (<?php echo $value['note'] ?>)" onkeydown="">

	<input value="<?php echo !isNull($gtto)?$gtto:'' ?>" type="hidden" name="search_<?php echo $value['name']?>_to">

</div>