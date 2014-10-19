<?php

function load_configuration() {
  $GLOBALS['config'] = null;
  $file = dirname(__FILE__) . '/../config.json';
  if (file_exists($file)) {
    $GLOBALS['config'] = json_decode(file_get_contents($file), true);
  }
  if (!$GLOBALS['config']) {
    throw new Exception('config.json not found');
  }
}
