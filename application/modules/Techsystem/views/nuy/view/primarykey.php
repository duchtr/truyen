

<?php $value =  $currentitem[$currentvalue['name']];

if($currentvalue['view']==1){ ?>

<td data-title="<?php echo __('note',$currentvalue) ?>" class="<?php echo $currentvalue['view']==1?"hidden":"" ?>">

<input style="width:100%" class="" readonly type="text" 

name="<?php echo $currentvalue['name'] ?>" value="<?php echo $value ?>">

</td>

<?php 

}

?>

<input type="hidden" name = "<?php echo $currentvalue['name'] ?>" value="<?php echo $value ?>">





