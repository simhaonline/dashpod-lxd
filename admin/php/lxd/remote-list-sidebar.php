<?php

$results = shell_exec("sudo lxc remote list --format json");

#Decode JSON data
$items = json_decode($results, true);


foreach ($items as $item=>$value) {
  $name = $item;
  $protocol = $value['Protocol'];

  if ($name == "" || $protocol != "lxd")
  continue; 

  echo '<!-- Nav Item -->';
  echo '<li class="nav-item">';
  echo '<a class="nav-link" href="overview.html?remote=' . $name . '&project=default">';
  echo '<i class="fas fa-fw fa-server"></i>';
  echo '<span>' . htmlentities($name) . '</span></a>';
  echo '</li>';

}

?>