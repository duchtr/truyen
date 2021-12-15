<?php
$hook['tech5s_after_login'][] = array( 
  'class'    => 'Pwa',
  'function' => 'injectMenuAdmin',
  'filename' => 'Pwa.php',
  'filepath' => 'plugins/pwa',
);
$hook['tech5s_extra_function'][] = array( 
  'class'    => 'Pwa',
  'function' => 'managerPwa',
  'filename' => 'Pwa.php',
  'filepath' => 'plugins/pwa',
);
$hook['tech5s_before_footer'][] = array( 
  'class'    => 'Pwa',
  'function' => 'insertScript',
  'filename' => 'Pwa.php',
  'filepath' => 'plugins/pwa',
);
$hook['tech5s_vindex_init'][] = array( 
  'class'    => 'Pwa',
  'function' => 'initVindex',
  'filename' => 'Pwa.php',
  'filepath' => 'plugins/pwa',
);