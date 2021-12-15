<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


class MY_Security extends CI_Security {


	protected function _do_never_allowed($str)
	{
		$str = str_replace(array_keys($this->_never_allowed_str), $this->_never_allowed_str, $str);

		foreach ($this->_never_allowed_regex as $regex)
		{
			$str = preg_replace('#'.$regex.'#is', '', $str);
		}

		return $str;
	}
	public function xss_clean($str, $is_image = FALSE) {
        // Do whatever you need here with the input ... ($str, $is_image)

        $str = parent::xss_clean($str, $is_image);

      	$str = str_replace("[removed]", "", $str);

        return $str;
    }
}
 ?>