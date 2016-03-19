<?php

function get_user_vote($user_id) {
  $res = query(
    'SELECT "vote"
      FROM "votes"
      WHERE "user_id" = $1',
    intval($user_id)
  );
  $row = pg_fetch_row($res);
  return ($row === false) ? null : intval($row[0]);
}

function save_user_vote($user_id, $screen_name, $vote) {
  query(
    'INSERT INTO "votes"
      ("user_id", "screen_name", "vote")
      VALUES ($1, $2, $3)',
    $user_id, $screen_name, $vote
  );
}

function get_results() {
  $res = query(
    'SELECT "vote", COUNT(*), EXTRACT(EPOCH FROM MIN("when"))
      FROM "votes"
      GROUP BY "vote"'
  );
  $cache = array();
  while ($row = pg_fetch_row($res)) {
    $cache[array_shift($row)] = array_map('intval', $row);
  }
  $now = time();
  $results = array();
  foreach ($GLOBALS['config']['options'] as $idx => &$team) {
    $info = $team;
    if (array_key_exists($idx + 1, $cache)) {
      $info['votes'] = $cache[$idx + 1][0];
      $info['earliest_vote'] = $cache[$idx + 1][1];
    } else {
      $info['votes'] = 0;
      $info['earliest_vote'] = $now;
    }
    $results[] = $info;
  }
  usort($results, function (&$a, &$b) {
    return ($a['votes'] != $b['votes']) ?
      $b['votes'] - $a['votes'] :
      $a['earliest_vote'] - $b['earliest_vote'];
  });
  return $results;
}

function get_votes() {
  $res = query(
    'SELECT "user_id", "screen_name", "vote", "when"
      FROM "votes"
      ORDER BY "when" DESC'
  );
  $tz = new DateTimeZone($GLOBALS['config']['timezone']);
  $votes = array();
  while ($row = pg_fetch_assoc($res)) {
    $row['vote'] = $GLOBALS['config']['options'][$row['vote'] - 1]['name'];
    $row['when'] = new DateTime($row['when']);
    $row['when']->setTimezone($tz);
    $row['when'] = $row['when']->format('Y-m-d H:i:s');
    $votes[] = $row;
  }
  return $votes;
}

function query() {
  _connect_to_database();
  $args = func_get_args();
  $sql = array_shift($args);
  $res = count($args) ? pg_query_params($sql, $args) : pg_query($sql);
  if ($res === false) {
    throw new Exception('Query failed: ' . pg_last_error());
  }
  return $res;
}

function _connect_to_database() {
  if (array_key_exists('dbh', $GLOBALS)) {
    return;
  }
  if ($_ENV['OPENSHIFT_POSTGRESQL_DB_URL']) {
    $data['host'] = $_ENV['OPENSHIFT_POSTGRESQL_DB_HOST'];
    $data['port'] = $_ENV['OPENSHIFT_POSTGRESQL_DB_PORT'];
    $data['user'] = $_ENV['OPENSHIFT_POSTGRESQL_DB_USERNAME'];
    $data['password'] = $_ENV['OPENSHIFT_POSTGRESQL_DB_PASSWORD'];
    $data['dbname'] = 'twitterquiz';
    // $data['sslmode'] = 'require';
  } else {
    $comp = parse_url($_ENV['DATABASE_URL']);
    $data['host'] = $comp['host'];
    $data['port'] = $comp['port'];
    $data['user'] = $comp['user'];
    $data['password'] = $comp['pass'];
    $data['dbname'] = substr($comp['path'], 1);
    $data['sslmode'] = 'require';
  }
  $str = implode(' ', array_map(function($key) use (&$data) {
    return "$key={$data[$key]}";
  }, array_keys($data)));
  $res = pg_connect($str);
  if ($res === false) {
    throw new Exception('Database connection failed');
  }
  $GLOBALS['dbh'] = $res;
}
