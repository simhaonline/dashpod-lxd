<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = filter_var(urldecode($_POST['remote']), FILTER_SANITIZE_STRING);
$name = filter_var(urldecode($_POST['name']), FILTER_SANITIZE_STRING);
$yaml = urldecode($_POST['yaml']);
$time = date('d-m-y-H-i-s',time()); //to add some uniqueness to filename 
$filepath = "/tmp/dashpod-profile-" . $time . ".yaml";


//For safety, replace anything other than alphanumeric, "_", "-", or space with ""
$name  = preg_replace('/[^a-zA-Z0-9_ -]/s','',$name);


#Create a file to write the YAML data to
$yamlfile = fopen($filepath, "w") or die("Unable to open file!");
fwrite($yamlfile,$yaml);
fclose($yamlfile);

#Create an empty network first
$create = exec("sudo lxc network create '$remote':'$name' 2>&1", $output, $return);

if ($return == 0) {
  #Apply YAML configuration to network
  $edit = exec("sudo lxc network edit '$remote':'$name' < $filepath 2>&1", $output, $return);
}

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