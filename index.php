<?php

error_reporting(0);

try {
  require_once dirname(__FILE__) . '/vendor/autoload.php';
  foreach (glob(dirname(__FILE__) . '/lib/*.php') as $lib) {
    require_once $lib;
  }
  session_start();
  load_configuration();
  process_request();
} catch (Exception $e) {
  render('error', array('message' => $e->getMessage()));
}
