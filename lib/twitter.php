<?php

function get_supplied_twitter_access_token() {
  if (!array_key_exists('oauth_token', $_GET)) {
    return null;
  }
  if ($_GET['oauth_token'] !== $_SESSION['oauth_request']['oauth_token']) {
    throw new Exception('Invalid OAuth token');
  }
  if (!array_key_exists('oauth_verifier', $_GET)) {
    throw new Exception('Invalid OAuth verifier');
  }
  _initialize_tmhoauth();
  $GLOBALS['tmhOAuth']->reconfigure(
    array_merge(
      $GLOBALS['tmhOAuth']->config,
      array(
        'token'  => $_SESSION['oauth_request']['oauth_token'],
        'secret' => $_SESSION['oauth_request']['oauth_token_secret']
      )
    )
  );
  $code = $GLOBALS['tmhOAuth']->user_request(
    array(
      'method' => 'POST',
      'url' => $GLOBALS['tmhOAuth']->url('oauth/access_token', ''),
      'params' => array(
        'oauth_verifier' => trim($_GET['oauth_verifier'])
      )
    )
  );
  if ($code != 200) {
    throw new Exception("Cannot obtain access token: HTTP $code");
  }
  $oauth_creds = $GLOBALS['tmhOAuth']->extract_params(
    $GLOBALS['tmhOAuth']->response['response']);
  return $oauth_creds;
}

function get_twitter_authorization_url() {
  $url = get_canonical_url('auth');
  _initialize_tmhoauth();
  $code = $GLOBALS['tmhOAuth']->apponly_request(
    array(
      'without_bearer' => true,
      'method' => 'POST',
      'url' => $GLOBALS['tmhOAuth']->url('oauth/request_token', ''),
      'params' => array(
        'oauth_callback' => $url
      )
    )
  );
  if ($code != 200) {
    throw new Exception("Cannot obtain request token: HTTP $code");
  }
  $_SESSION['oauth_request'] = $GLOBALS['tmhOAuth']->extract_params(
    $GLOBALS['tmhOAuth']->response['response']);
  if ($_SESSION['oauth_request']['oauth_callback_confirmed'] !== 'true') {
    throw new Exception('Parameter oauth_callback_confirmed must be true');
  }
  $url = $GLOBALS['tmhOAuth']->url('oauth/authorize', '')
    . '?oauth_token=' . $_SESSION['oauth_request']['oauth_token'];
  return $url;
}

function _initialize_tmhoauth() {
  if (!array_key_exists('tmhOAuth')) {
    $GLOBALS['tmhOAuth'] = new tmhOAuth($GLOBALS['config']['twitter']);
  }
}
