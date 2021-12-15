<?php
function checkCountCart(){
	$ci = & get_instance();
	return $ci->cart->contents() != null ? true : false;
}
?>