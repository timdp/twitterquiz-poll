<?php

function verify_controller_preconditions() {
  return !is_signed_in();
}

function run_controller() {
  $token = get_supplied_twitter_access_token();
  if (!is_null($token)) {
    save_signin($token);
    after_signin();
  } else {
    $url = get_twitter_authorization_url();
    redirect($url);
  }
}
