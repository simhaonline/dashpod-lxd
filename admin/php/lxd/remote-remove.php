<?php

if (!isset($_SESSION)) {
  session_start();
}

$name = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);

//remove special characters 
$name  = preg_replace('/[^a-zA-Z0-9\.\_\-]/s','-',$name);

exec("sudo lxc remote remove '$name' 2>&1", $output, $return);

if ($return == 0) {
  $db = new SQLite3('/var/dashpod/data/sqlite/dashpod.sqlite');
  $db->exec("DELETE FROM lxd_remotes WHERE name = '$name'");
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