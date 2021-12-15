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
		<?php echo __('note',$field) ?>
	</div>
	<div class="col-md-10 col-xs-12">
		<input type="text" name="<?php echo $field['name'] ?>" value="<?php echo isset($dataitem)?$dataitem[$field['name']]:(@$valueData?$valueData:"") ?>">
	</div>
</div>
<script>
	$('input[name="<?php echo $field['name'] ?>"]').spectrum({
		color: "<?php echo isset($dataitem)?$dataitem[$field['name']]:(@$valueData?$valueData:"") ?>",
		preferredFormat: "hex",
		flat: false,
		showInput: true,
		showInitial: true,
		allowEmpty: true,
		showAlpha: true
	});
</script>
