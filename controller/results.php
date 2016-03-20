<?php

function verify_controller_preconditions() {
  return has_voted();
}

function run_controller() {
  $vote_num = get_user_vote(get_user_id());
  $vote = !is_null($vote_num)
    ? $GLOBALS['config']['options'][$vote_num - 1]['name']
    : null;
  $results = get_results();
  $results_min = array();
  $total = 0;
  foreach ($results as &$row) {
    $results_min[] = array($row['name'], $row['votes']);
    $total += $row['votes'];
  }
  render('results',
    array(
      'screen_name' => get_screen_name(),
      'vote' => $vote,
      'total_votes' => $total,
      'results' => $results,
      'results_min' => $results_min
    ));
}
