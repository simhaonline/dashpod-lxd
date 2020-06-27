<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);

//remove special characters 
$remote  = preg_replace('/[^a-zA-Z0-9\.\_\-]/s','-',$remote);

#Get the JSON data
$results = exec("sudo lxc image list '$remote': --format json 2>&1", $output, $return);

#Decode JSON data
$items = json_decode($results, true);


foreach ($items as $item) {
  $description = $item['properties']['description'];
  $fingerprint = $item['fingerprint'];

  if ($description == "")
    continue;

  echo '<option value="' . $fingerprint . '">' . htmlentities($description) . '</option>';
} 


?>