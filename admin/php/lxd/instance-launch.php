<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$name = escapeshellarg(filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING));
$fingerprint = escapeshellarg(filter_var(urldecode($_GET['fingerprint']), FILTER_SANITIZE_STRING));
$profile = escapeshellarg(filter_var(urldecode($_GET['profile']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);

exec("sudo lxc launch $remote:$fingerprint $remote:$name -p $profile --project $project 2>&1", $output, $return);

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
    $_SESSION['alert'] = htmlentities($output[2]); //output[1] states creating instance, [2] gives reason it failed
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
  }
}

?>