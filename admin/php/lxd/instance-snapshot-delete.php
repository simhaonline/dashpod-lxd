<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$name = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);
$snapshot = filter_var(urldecode($_GET['snapshot']), FILTER_SANITIZE_STRING);


exec("sudo lxc delete '$remote':'$name'/'$snapshot' -f 2>&1", $output, $return);

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
