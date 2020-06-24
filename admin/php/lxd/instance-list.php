<?php

//start session
if (!isset($_SESSION)) {
  session_start();
}

$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);

#Get the JSON data
$results = exec("sudo lxc list '$remote': --format json 2>&1", $output, $return);

#Decode the JSON data
$items = json_decode($results, true);

#Build the table's data
echo "<thead>";
echo "<tr>";
echo "<th style='width:75px'></th>";
echo "<th>Name</th>";
echo "<th>Memory</th>";
echo "<th>IPv4 Addresses</th>";
echo "<th>IPv6 Addresses</th>";
echo "<th>Image</th>";
echo "<th style='width:75px'></th>";
echo "</tr>";
echo "</thead>";

echo "<tbody>";

foreach ($items as $item) {
  $created = $item['created_at'];
  $name = $item['name'];
  $description = $item['description'];
  $image = $item['config']['image.description'];
  $status = $item['state']['status']; //ex Running
  $memory = number_format($item['state']['memory']['usage'] / 1048576, 2);
  $networks = $item['state']['network']; //array of network interfaces

  if ($name == "")
    continue;

  echo "<tr>";
  
  if ($status == "Running")
    echo "<td> <i class='fas fa-cube fa-2x' style='color:#4e73df'></i> </td>";
  else
    echo "<td> <i class='fas fa-cube fa-2x' style='color:#DDDDDD'></i> </td>";
  
  echo "<td>";
    echo "<a href='./instance.html?name=" . $name . "&remote=" . $remote . "'>";
      echo htmlentities($name);
    echo "</a>";
    echo "<br />";
    echo htmlentities($description);  
  echo "</td>";

  if ($memory > 0)
    echo "<td>" . htmlentities($memory) . " MB </td>";
  else
    echo "<td> </td>";
  
  echo "<td>";
  foreach ($networks as $key=>$network){
    $interfaces = $network['addresses'];
    $interface_name = $key;
    foreach ($interfaces as $interface){
      if ($interface['family'] == "inet" && $interface['address'] != "127.0.0.1"){
        echo htmlentities($interface['address']) . " (" . htmlentities($interface_name) . ")";
        echo "<br />";
      }
    }
  } 
  echo "</td>";

  echo "<td>";
    foreach ($networks as $key=>$network){
      $interfaces = $network['addresses'];
      $interface_name = $key;
      foreach ($interfaces as $interface){
        if ($interface['family'] == "inet6" && $interface['address'] != "::1" && substr($interface['address'],0,4) != "fe80"){
          echo htmlentities($interface['address']) . " (" . htmlentities($interface_name) . ")";
          echo "<br />";
        }
      }
    } 
  echo "</td>";
  
  echo "<td>" . htmlentities($image) . "</td>";


  echo "<td>";
    echo '<div class="dropdown no-arrow">';
    echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
    echo '</a>';
    echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
    echo '<div class="dropdown-header">Options:</div>';

    if ($status == "Running")
      echo '<a class="dropdown-item" href="./php/lxd/instance-stop.php?name='. $name . '&remote=' . $remote . '">Stop</a>';
    else
      echo '<a class="dropdown-item" href="./php/lxd/instance-start.php?name='. $name . '&remote=' . $remote . '">Start</a>';

    echo '</div>';
    echo '</div>';
  echo "</td>";

  echo "</tr>";
}

echo "</tbody>";


?>
