<?php
$hook['tech5s_vindex_init'][] = array( 
  'class'    => 'Rating',
  'function' => 'initVindex',
  'filename' => 'Rating.php',
  'filepath' => 'plugins/rating',
);
$hook['tech5s_before_footer'][] = array( 
  'class'    => 'Rating',
  'function' => 'insertScript',
  'filename' => 'Rating.php',
  'filepath' => 'plugins/rating',
);
