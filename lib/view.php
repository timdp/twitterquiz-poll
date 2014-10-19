<?php

function render($view, $params = array()) {
  $loader = new Twig_Loader_Filesystem(dirname(__FILE__) . '/../view');
  $twig = new Twig_Environment($loader,
    array(
      'cache' => dirname(__FILE__) . '/../cache',
      'auto_reload' => true
    )
  );
  $params['site_url'] = $GLOBALS['config']['site_url'];
  $params['site_title'] = $GLOBALS['config']['site_title'];
  echo $twig->render($view . '.html', $params);
}
