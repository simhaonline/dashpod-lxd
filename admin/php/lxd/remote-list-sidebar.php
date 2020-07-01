<?php

$results = shell_exec("sudo lxc remote list --format json");

#Decode JSON data
$items = json_decode($results, true);

$i = 0;
foreach ($items as $item=>$value) {
  $name = $item;
  $protocol = $value['Protocol'];

  if ($name == "" || $protocol != "lxd")
  continue; 


  echo '<li class="nav-item">';
  echo '<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages' . $i . '" aria-expanded="true" aria-controls="collapsePages' . $i . '">';
  echo '<i class="fas fa-fw fa-server"></i>';
  echo '<span>' . htmlentities($name) . '</span>';
  echo '</a>';
  echo '<div id="collapsePages' . $i . '" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">';
  echo '<div class="bg-white py-2 collapse-inner rounded">';
  echo '<h6 class="collapse-header">Projects:</h6>';
  
  $results = shell_exec("sudo lxc project list '$name': --format json");
  $projects = json_decode($results, true);
  foreach ($projects as $project){
    $project_name = $project['name'];
    echo '<a class="collapse-item" href="overview.html?remote=' . $name . '&project=' . $project_name . '">' . $project_name . '</a>';
  }
  
  echo '</div>';
  echo '</div>';
  echo '</li>';
$i++;

}




?>