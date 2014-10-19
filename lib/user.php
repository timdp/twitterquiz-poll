<?php

function is_signed_in() {
  return array_key_exists('oauth_info', $_SESSION);
}

function is_admin() {
  return is_signed_in() && in_array(get_user_id(), $GLOBALS['config']['admins']);
}

function get_user_id() {
  return $_SESSION['oauth_info']['user_id'];
}

function get_screen_name() {
  return $_SESSION['oauth_info']['screen_name'];
}

function save_signin($access_token) {
  $_SESSION['oauth_info'] = $access_token;
}
