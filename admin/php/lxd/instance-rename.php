<?php

if (!isset($_SESSION)) {
  session_start();
}

//Set exec time limit to 60 seconds
set_time_limit(60);

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$name = escapeshellarg(filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING));
$rename = escapeshellarg(filter_var(urldecode($_GET['rename']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);
$rename_url = filter_var(urldecode($_GET['rename']), FILTER_SANITIZE_STRING);


//rename instance
exec("sudo lxc move $remote:$name $remote:$rename --project $project 2>&1", $output, $return);

if ($return == 0) {
  header("Location: ../../instance.html?remote=" . $remote_url . "&project=" . $project_url . "&name=" . $rename_url );
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