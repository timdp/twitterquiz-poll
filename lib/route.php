<?php

function process_request() {
  $action = ltrim($_SERVER['PATH_INFO'], '/');
  if ($action === '') {
    $action = 'index';
  }
  $valid = resolve_controller($action);
  if (!$valid || get_request_url() !== get_canonical_url($action)) {
    redirect($valid ? $action : 'index');
    return;
  }
  if (function_exists('verify_controller_preconditions')) {
    $met = verify_controller_preconditions();
    if (!$met) {
      redirect('index');
      return;
    }
  }
  run_controller();
}

function resolve_controller($action) {
  if (!preg_match('/^[a-z]+$/', $action)) {
    return false;
  }
  $file = dirname(__FILE__) . '/../controller/' . $action . '.php';
  if (!file_exists($file)) {
    return false;
  }
  require $file;
  return true;
}

function get_canonical_url($action) {
  return $GLOBALS['config']['site_url'] . '/' .
    ($action === 'index' ? '' : $action);
}

function get_request_url() {
  $https = ($_SERVER['HTTPS'] || is_cloudflare_https());
  $proto = $https ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];
  $path = $_SERVER['PATH_INFO'];
  if (empty($path)) {
    $path = '/';
  }
  return $proto . '://' . $host . $path;
}

function is_cloudflare_https() {
  if (!array_key_exists('HTTP_CF_VISITOR', $_SERVER)) {
    return false;
  }
  $info = json_decode($_SERVER['HTTP_CF_VISITOR']);
  return ($info->scheme === 'https');
}

function redirect($url) {
  if (strpos($url, '://') === false) {
    $url = get_canonical_url($url);
  }
  header('Refresh: 0; url=' . $url);
}
