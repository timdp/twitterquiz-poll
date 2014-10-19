<?php

function after_signin() {
  if (has_voted()) {
    redirect('results');
  } else {
    redirect('vote');
  }
}

function has_voted() {
  return is_signed_in() && !is_null(get_user_vote(get_user_id()));
}
