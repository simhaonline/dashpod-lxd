<?php

//start session
if (!isset($_SESSION)) {
  session_start();
}
  
$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$name = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);

//remove special characters 
$name  = preg_replace('/[^a-zA-Z0-9\.\_\-]/s','-',$name);
$remote  = preg_replace('/[^a-zA-Z0-9\.\_\-]/s','-',$remote);

exec("sudo lxc stop '$remote':'$name' 2>&1", $output, $return);

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
