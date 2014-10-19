<?php

function verify_controller_preconditions() {
  return is_admin();
}

function run_controller() {
  render('votes',
    array(
      'votes' => get_votes()
    ));
}
