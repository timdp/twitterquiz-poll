<?php

function load_configuration() {
  $base = dirname(__FILE__) . '/../config';
  $GLOBALS['config'] = _read_json("$base/site.json");
  $GLOBALS['config']['options'] = _read_json("$base/teams.json");
  $GLOBALS['config']['twitter'] = _read_json("$base/auth/twitter.json");
}

function _read_json($file) {
  if (!file_exists($file)) {
    throw new Exception("File not found: $file");
  }
  return json_decode(file_get_contents($file), true);
}
