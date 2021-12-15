<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/


$hook['post_controller_constructor'][] = array( 
  'class'    => 'PreProcess',
  'function' => 'generate_token',
  'filename' => 'preprocess.php',
  'filepath' => 'hooks'
);

$hook['display_override'][] = array(
  'class'    => 'PreProcess',
  'function' => 'replaceHtml',
  'filename' => 'preprocess.php',
  'filepath' => 'hooks'
);
$hook['post_controller_constructor'][] = array(
  'class'    => 'HookPlugin',
  'function' => 'loadPlugins',
  'filename' => 'hook_plugins.php',
  'filepath' => 'hooks'
);

$hook['post_controller_constructor'][] = array(
  'class'    => 'HookPlugin',
  'function' => 'loadInjectVindex',
  'filename' => 'hook_plugins.php',
  'filepath' => 'hooks'
);


