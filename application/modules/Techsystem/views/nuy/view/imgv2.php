<td data-title="<?php echo __('note',$currentvalue) ?>">
<?php 
				$tmp = isset($currentitem) ? $currentitem[$currentvalue['name']]:"";
				$tmp = json_decode($tmp,true);
				$img = isset($tmp) && is_array($tmp) ?$tmp["path"].$tmp["file_name"]:'theme/admin/images/noimage.png';  
				?>
				<img style="max-width:100px; max-height: 100px; display: block; margin: auto;" src="<?php echo $img ?>" alt="" class="">
</td>