<div style="color: Gray; text-align: center;">   <?php $copyright =  @$tech5s?$tech5s->version:"Tech 5s" ?>
	<?php 
	$resultHook = $this->hooks->call_hook(['tech5s_admin_copyright',"copyright"=>$copyright]);
    if(!is_bool($resultHook)){
         extract($resultHook);
     }
 	?>
 	<?php echo $copyright; ?>
</div>