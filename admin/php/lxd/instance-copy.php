<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$name = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);
$copy = filter_var(urldecode($_GET['copy']), FILTER_SANITIZE_STRING);


exec("sudo lxc copy '$remote':'$name' '$remote':'$copy' 2>&1", $output, $return);

if ($return == 0) {
  header("Location: ../../overview.html?remote=" . $remote);
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
