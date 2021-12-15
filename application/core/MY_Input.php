<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Input extends CI_Input{


    function post($index = NULL, $xss_clean = TRUE)
    {
        return parent::post($index, $xss_clean);
    }
    function postf($index = NULL, $xss_clean = FALSE)
    {
        return parent::post($index, $xss_clean);
    }

    function get($index = NULL, $xss_clean = TRUE)
    {
        return parent::get($index, $xss_clean);
    }
    function getf($index = NULL, $xss_clean = FALSE)
    {
        return parent::get($index, $xss_clean);
    }
}
?>