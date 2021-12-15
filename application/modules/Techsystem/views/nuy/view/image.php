<td data-title="<?php echo __('note',$currentvalue) ?>">

	<?php $value =  $currentitem[$currentvalue['name']] ;

$value = isNull($value)?'theme/admin/images/noimage.png':$value;

?>

<a class="fancybox" href="<?php echo $value ?>">

<img style="width:50px" src="<?php echo getImageThumb($value) ?>" alt="">

</a>

</td>