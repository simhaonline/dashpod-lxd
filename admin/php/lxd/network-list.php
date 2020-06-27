<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);

//remove special characters 
$remote  = preg_replace('/[^a-zA-Z0-9\.\_\-]/s','-',$remote);

#Get the JSON data
$results = exec("sudo lxc network list '$remote': --format json 2>&1", $output, $return);

$networks_array = json_decode($results, true);

#Build the table's data
echo "<thead>";
echo "<tr>";
echo "<th style='width:75px'></th>";
echo "<th>Name</th>";
//echo "<th>IPs</th>";
echo "<th>Type</th>";
echo "<th>Managed</th>";
echo "<th style='width:75px'></th>";
echo "</tr>";
echo "</thead>";

echo "<tbody>";

foreach ($networks_array as $network) {
  $name = $network['name'];
  $type = $network['type'];
  $managed = $network['managed'];
  $managed_answer = $managed ? 'true' : 'false';
  $description = $network['description'];

  if ($name == "")
    continue;

  echo "<tr>";
  
  if ($managed)
    echo "<td> <i class='fas fa-network-wired fa-2x' style='color:#4e73df'></i> </td>";
  else
    echo "<td> <i class='fas fa-network-wired fa-2x' style='color:#DDDDDD'></i> </td>";
  
  echo "<td>" . htmlentities($name) . "</td>";
  echo "<td>" . htmlentities($type) . "</td>";
  echo "<td>" . htmlentities($managed_answer) . "</td>";


  if ($managed){
    echo "<td>";
    echo '<div class="dropdown no-arrow">';
    echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
    echo '</a>';
    echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
    echo '<div class="dropdown-header">Options:</div>';

    echo '<a class="dropdown-item" href="network-edit.html?name='. $name . '&remote=' . $remote . '">Edit</a>';
    echo '<a class="dropdown-item" href="./php/lxd/network-delete.php?name='. $name . '&remote=' . $remote . '">Delete</a>';

    echo '</div>';
    echo '</div>';
    echo "</td>";
  }
  else {
    echo "<td></td>";
  }

  echo "</tr>";
}

echo "</tbody>";


?>
