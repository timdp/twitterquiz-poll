<?php

function run_controller() {
  if (is_signed_in()) {
    after_signin();
  } else {
    render('index');
  }
}
