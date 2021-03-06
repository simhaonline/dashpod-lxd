<?php

if (!isset($_SESSION)) {
  session_start();
}

//Set exec time limit to 60 seconds
set_time_limit(60);

$remote = filter_var(urldecode($_POST['remote']), FILTER_SANITIZE_STRING);
$name = filter_var(urldecode($_POST['name']), FILTER_SANITIZE_STRING);
$yaml = urldecode($_POST['yaml']);
$time = date('d-m-y-H-i-s',time()); //to add some uniqueness to filename 
$filepath = "/tmp/dashpod-profile-" . $time . ".yaml";
$inventorypath = "/tmp/dashpod-ansible-" . $time . ".yaml";
 

//Construct ansible host file data
$inventorydata = $name . " ansible_connection=lxd ansible_host=" . $remote . ":" . $name;

#Create a file to write the YAML data to
$yamlfile = fopen($filepath, "w") or die("Unable to open file!");
fwrite($yamlfile,$yaml);
fclose($yamlfile);

//Create an ansible inventory file
$inventoryfile = fopen($inventorypath, "w") or die("Unable to open file!");
fwrite($inventoryfile,$inventorydata);
fclose($inventoryfile);


#Run ansible playbook
exec("sudo ansible-playbook -i $inventorypath $filepath --limit '$name' 2>&1", $output, $return);

#Remove temp files
unlink($filepath);
unlink($inventorypath);



$counter = 0;
foreach ($output as $line){
  if ($couter++ < 1)
    continue;
  $_SESSION['alert'] .= htmlentities($line) . "\n";
}
  header("Location: ".$_SERVER['HTTP_REFERER']);
  exit;

?>