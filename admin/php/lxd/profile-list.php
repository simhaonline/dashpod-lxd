<?php

if (!isset($_SESSION)) {
  session_start();
}
  
$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);

#Get the JSON data
$results = exec("sudo lxc profile list '$remote': --format json 2>&1", $output, $return);

$items = json_decode($results, true);

#Build the table's data
echo "<thead>";
echo "<tr>";
echo "<th style='width:75px'></th>";
echo "<th>Name</th>";
echo "<th>Description</th>";
echo "<th>NIC Devices</th>";
echo "<th style='width:75px'></th>";
echo "</tr>";
echo "</thead>";

echo "<tbody>";

foreach ($items as $item) {
  $name = $item['name'];
  $devices = $item['devices']; //array
  $description = $item['description'];


  if ($name == "")
    continue;

  echo "<tr>";
  
  echo "<td> <i class='fas fa-address-card fa-2x' style='color:#4e73df'></i> </td>";
  
  echo "<td>" . htmlentities($name) . "</td>";
  echo "<td>" . htmlentities($description) . "</td>";

  echo "<td>";
  foreach ($devices as $key=>$device){
    $device_name = $device['name'];
    $device_type = $device['type'];

    if ($device_type == "nic"){
        echo htmlentities($device_name);
        echo "<br />";
    }
  } 
  echo "</td>";


  echo "<td>";
    echo '<div class="dropdown no-arrow">';
    echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
    echo '</a>';
    echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
    echo '<div class="dropdown-header">Options:</div>';

    echo '<a class="dropdown-item" href="profile-edit.html?name='. $name . '&remote=' . $remote . '">Edit</a>';
    echo '<a class="dropdown-item" href="./php/lxd/profile-delete.php?name='. $name . '&remote=' . $remote . '">Delete</a>';

    echo '</div>';
    echo '</div>';
  echo "</td>";

  echo "</tr>";

}

echo "</tbody>";


?>