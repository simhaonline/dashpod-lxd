<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$name = escapeshellarg(filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING));
$target_remote = escapeshellarg(filter_var(urldecode($_GET['target_remote']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$target_project = escapeshellarg(filter_var(urldecode($_GET['target_project']), FILTER_SANITIZE_STRING));

$target_remote_url = filter_var(urldecode($_GET['target_remote']), FILTER_SANITIZE_STRING);
$target_project_url = filter_var(urldecode($_GET['target_project']), FILTER_SANITIZE_STRING);
$name_url = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);

//Check to see if target_project already exists on target_host
$results = shell_exec("sudo lxc project list $target_remote: --format json");
$items = json_decode($results, true);
$project_exists = false;
foreach ($items as $item) {
  if ($item['name'] == $target_project_url)
    $project_exists = true;
}

if ($project_exists != true){
  //Create project
  exec("sudo lxc project create $target_remote:$target_project 2>&1", $output, $return);
  if ($return == 0) {
    sleep(1); //give it some time to create storage volume, recieved error once saying it did not yet exist with moving.
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
}



//Move instance to remote host
//Execute command in the background
exec("sudo lxc move $remote:$name $target_remote:$name --project $project --target-project $target_project > /tmp/dashpod_error_log 2>&1 &", $output, $return);


if ($return == 0) {
  sleep(1);
  if (file_exists("/tmp/dashpod_error_log")){
    $output = explode("\n", file_get_contents('/tmp/dashpod_error_log'));
    unlink("/tmp/dashpod_error_log");
    $_SESSION['alert'] = htmlentities($output[1]);
    header("Location: ../../instances.html?remote=" . $target_remote_url . "&project=" . $target_project_url);
    exit;
  }
  else {
    $_SESSION['alert'] = "An error may have occured preventing the instance move";
    header("Location: ../../instances.html?remote=" . $target_remote_url . "&project=" . $target_project_url);
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