<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_POST['remote']), FILTER_SANITIZE_STRING));
$name = escapeshellarg(filter_var(urldecode($_POST['name']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_POST['project']), FILTER_SANITIZE_STRING));
$yaml = urldecode($_POST['yaml']);
$time = date('d-m-y-H-i-s',time()); //to add some uniqueness to filename 
$filepath = "/tmp/dashpod-profile-" . $time . ".yaml";


#Create a file to write the YAML data to
$yamlfile = fopen($filepath, "w") or die("Unable to open file!");
fwrite($yamlfile,$yaml);
fclose($yamlfile);

#Apply YAML configuration to profile
$edit = exec("sudo lxc storage edit $remote:$name --project $project < $filepath 2>&1", $output, $return);

#Remove temp file
unlink($filepath);

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