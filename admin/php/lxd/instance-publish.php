<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$name = escapeshellarg(filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING));
$description = escapeshellarg(filter_var(urldecode($_GET['description']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);

//Execute command in the background
exec("sudo lxc publish $remote:$name $remote: description=$description --project $project > /tmp/dashpod_error_log 2>&1 &", $output, $return);


if ($return == 0) {
  sleep(1);
  if (file_exists("/tmp/dashpod_error_log")){
    $output = explode("\n", file_get_contents('/tmp/dashpod_error_log'));
    unlink("/tmp/dashpod_error_log");
    $_SESSION['alert'] = htmlentities($output[1]);
    header("Location: ../../images.html?remote=" . $remote_url . "&project=" . $project_url);
    exit;
  }
  else {
    $_SESSION['alert'] = "An error may have occured preventing the published instance";
    header("Location: ../../images.html?remote=" . $remote_url . "&project=" . $project_url);
    exit;
  }
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