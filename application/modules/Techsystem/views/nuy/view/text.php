<td data-title="<?php echo __('note',$currentvalue) ?>">
<?php $value =  $currentitem[$currentvalue['name']]; ?>
<input 
<?php echo 'data-primary=\''.json_encode($primarykey).'\'' ?>
style="width:100%" class="<?php echo $currentvalue['editable']==1?'editable':'' ?>" <?php echo $currentvalue['editable']==1?'readonly':'' ?> type="text" 
name="<?php echo $currentvalue['name'] ?>" value="<?php echo $value ?>">
</td>