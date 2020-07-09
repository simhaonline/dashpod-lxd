<?php

$results = shell_exec("sudo lxc remote list --format json");

#Decode JSON data
$items = json_decode($results, true);


foreach ($items as $item=>$value) {
  $name = $item;
  $protocol = $value['Protocol'];

  if ($name == "" || $protocol != "lxd")
  continue; 

  echo '<option value="' . $name . '">' . htmlentities($name) . '</option>';

}

?>