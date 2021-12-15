<?php
$hook['tech5s_before_render'][] = array( 
  'class'    => 'CompressStatic',
  'function' => 'compress',
  'filename' => 'CompressStatic.php',
  'filepath' => 'plugins/compress_static',
);