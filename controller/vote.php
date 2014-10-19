<?php

function verify_controller_preconditions() {
  return is_signed_in() && !has_voted();
}

function run_controller() {
  $vote = _get_vote();
  if ($vote) {
    save_user_vote(get_user_id(), get_screen_name(), $vote);
    redirect('results');
  } else {
    render('vote',
      array(
        'screen_name' => get_screen_name(),
        'options' => $GLOBALS['config']['options']
      ));
  }
}

function _get_vote() {
  if ($_SERVER['REQUEST_METHOD'] == 'POST'
      && array_key_exists('vote', $_POST)) {
    $vote = intval($_POST['vote']);
    if ($vote > 0 && $vote <= count($GLOBALS['config']['options'])) {
      return $vote;
    }
  }
  return null;
}
