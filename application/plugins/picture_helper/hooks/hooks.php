<?php
$hook['tech5s_vindex_init'][] = array( 
  'class'    => 'PictureHelper',
  'function' => 'initVindex',
  'filename' => 'PictureHelper.php',
  'filepath' => 'plugins/picture_helper',
);
$hook['tech5s_before_footer'][] = array( 
  'class'    => 'PictureHelper',
  'function' => 'insertScript',
  'filename' => 'PictureHelper.php',
  'filepath' => 'plugins/picture_helper',
);
