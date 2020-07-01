<?php

if (!isset($_SESSION)) {
  session_start();
}

//can not escape for shell arg yet, will fail the validation tests
$name = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);
$host = filter_var(urldecode($_GET['host']), FILTER_SANITIZE_STRING);
$port = filter_var(urldecode($_GET['port']), FILTER_SANITIZE_STRING);

if ($name != "")
  $valid_name = true;

if (filter_var($host, FILTER_VALIDATE_IP) || filter_var($host, FILTER_VALIDATE_DOMAIN))
 $valid_domain = true;

if (filter_var($port, FILTER_VALIDATE_INT))
  $valid_port = true;

$name = escapeshellarg($name);
$host = escapeshellarg($host);
$port = escapeshellarg($port);

if ($valid_name && $valid_domain && $valid_port)
  exec("sudo lxc remote add $name $host:$port --accept-certificate 2>&1", $output, $return);
else 
  $return = 1;


if ($return == 0) {
  $db = new SQLite3('/var/dashpod/data/sqlite/dashpod.sqlite');
  $db->exec('CREATE TABLE IF NOT EXISTS lxd_remotes (name TEXT PRIMARY KEY, host TEXT, port INTEGER, exit_status INTEGER, return_value TEXT)');
  $db->exec("INSERT INTO lxd_remotes (name, host, port) VALUES ($name, $host, $port)");

  header("Location: ".$_SERVER['HTTP_REFERER']);
  exit;
}
else {
  if ($output == null){
    $_SESSION['alert'] = "There seems to be an undefined error.";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
  }
  else {
    $_SESSION['alert'] = htmlentities($output[1]);
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
  }
}

?>