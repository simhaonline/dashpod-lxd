<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$fingerprint = filter_var(urldecode($_GET['fingerprint']), FILTER_SANITIZE_STRING);

//remove special characters 
$remote  = preg_replace('/[^a-zA-Z0-9\.\_\-]/s','-',$remote);
$fingerprint  = preg_replace('/[^a-zA-Z0-9]/s','-',$fingerprint);

exec("sudo lxc config trust remove '$remote': '$fingerprint' 2>&1", $output, $return);

if ($return == 0) {
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
