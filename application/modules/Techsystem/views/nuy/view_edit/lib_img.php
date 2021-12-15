	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<?php echo __('note',$field); ?>
		</div>
		<div class="col-md-10 col-xs-12 boxlistimg">
			
			<input onchange="" type="hidden" data-name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>_single">
			<div class="libimg" data-type="libimg">
				<?php $listimg = isset($dataitem)? $dataitem[$field['name']]:'' ;
				if(!isNull($listimg)){
					$listimg = json_decode($listimg);
					if(is_array($listimg)){
						foreach ($listimg as $key => $value) {
							echo '<div class="boximg"><i onclick="var ax=$(this).parent().parent();$(this).parent().remove();changeListImage(ax,\''.$field['name'].'\');" class="icon-remove-circle"></i><img style="height:85px" src="'.$value.'"/>';
							echo '<i onclick="$(this).parent().moveDown();changeListImage($(this).parent().parent(),\''.$field['name'].'\');" class="icon-arrow-right" style="position:absolute;right:-15px;top:50%;    color: #810D0D;font-size: 30px;transform: translateY(-50%);"></i>';
							echo '<i onclick="$(this).parent().moveUp();changeListImage($(this).parent().parent(),\''.$field['name'].'\');" class="icon-arrow-left" style="position:absolute;left:-15px;top:50%;    color: #810D0D;font-size: 30px;transform: translateY(-50%);"></i>';
							echo '</div>';	
						}
					}
				}
				
				?>
			</div>
			<input type="hidden" name="<?php echo $field['name'] ?>" value ="<?php echo htmlspecialchars(isset($dataitem)? $dataitem[$field['name']]:'') ?>">
			<div class="btnadmin">
		    	<a href="<?php echo base_url() ?>Techsystem/mediaManager?istiny=<?php echo $field['name'] ?>_single" class="btn iframe-btn" type="button">Browse File ...</a>
		    </div>
		  	
		</div>
	</div>