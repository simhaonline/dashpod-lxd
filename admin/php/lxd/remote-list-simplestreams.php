<?php

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);

$results = shell_exec("sudo lxc remote list --format json");

#Decode JSON data
$items = json_decode($results, true);

echo "<thead>";
echo "<tr>";
echo "<th style='width:75px'></th>";
echo "<th>Name</th>";
echo "<th>Address</th>";
echo "<th>Protocol</th>";
echo "<th>Public</th>";
echo "<th style='width:75px'></th>";
echo "</tr>";
echo "</thead>";

echo "<tbody>";


foreach ($items as $item=>$value) {
  $name = $item;
  $addr = $value['Addr'];
  $protocol = $value['Protocol'];
  $public = ($value['Public'])?"true":"false";

  $domain = $value['Domain']; //currently empty
  $project = $value['Project']; //currently empty
  $static = $value['Static']; //boolean

  if ($name == "" || $protocol != "simplestreams")
  continue; 

  echo "<tr>";

  echo '<td> <a href="repository.html?remote=' . $remote_url . '&project=' . $project_url . '&repo=' . $name . '"> <i class="fas fa-archive fa-2x" style="color:#4e73df"></i> </a> </td>';
  echo '<td> <a href="repository.html?remote=' . $remote_url . '&project=' . $project_url . '&repo=' . $name . '">' . htmlentities($name) . '</a> </td>'; 
  echo "<td>" . htmlentities($addr) . "</td>";
  echo "<td>" . htmlentities($protocol) . "</td>";
  echo "<td>" . htmlentities($public) . "</td>";
  echo "<td></td>"; 

/*
  echo "<td>";
  echo '<div class="dropdown no-arrow">';
  echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
  echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
  echo '</a>';
  echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
  echo '<div class="dropdown-header">Options:</div>';

  echo '<a class="dropdown-item" href="./php/lxd/remote-remove.php?name='. $name . '">Delete</a>';

  echo '</div>';
  echo '</div>';
  echo "</td>";
*/


  echo "</tr>";

}

echo "</tbody>";

?>
