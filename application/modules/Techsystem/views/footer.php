<div style="color: Gray; text-align: center;">   <?php $copyright =  @$team39?$team39->version:"Made by Team 39 - UET" ?>
	<?php 
	$resultHook = $this->hooks->call_hook(['tech5s_admin_copyright',"copyright"=>$copyright]);
    if(!is_bool($resultHook)){
         extract($resultHook);
     }
 	?>
 	<?php echo $copyright; ?>
</div>