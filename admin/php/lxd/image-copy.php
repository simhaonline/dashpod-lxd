<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$image = filter_var(urldecode($_GET['image']), FILTER_SANITIZE_STRING);

//remove special characters 
$remote  = preg_replace('/[^a-zA-Z0-9\.\_\-]/s','-',$remote);
$image  = preg_replace('/[^a-zA-Z0-9\.\_\-\/]/s','-',$image);

exec("sudo lxc image copy images:'$image' '$remote': 2>&1", $output, $return);

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
