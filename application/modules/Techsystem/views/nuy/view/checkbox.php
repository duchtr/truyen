<td data-title="<?php echo __('note',$currentvalue) ?>">

<?php $value =  $currentitem[$currentvalue['name']];



 ?>



	<div style="margin-left: 10px;" class="switch">

    	<input name="<?php echo $currentvalue['name'] ?>" <?php echo 'data-primary=\''.json_encode($primarykey).'\'' ?> <?php echo $value==1?'checked':'' ?> id="cmn-toggle-<?php echo $currentvalue['name']."-".$currentitem['id'] ?>" class="cmn-toggle cmn-toggle-round <?php echo $currentvalue['editable']==1?'editable':'' ?>" style="  flex: inherit;" type="checkbox">

    	<label for="cmn-toggle-<?php echo $currentvalue['name']."-".$currentitem['id'] ?>"></label>

  	</div>

</td>