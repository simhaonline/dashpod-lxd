<?php

//Set exec time limit to 10 seconds
set_time_limit(10);

$results = shell_exec("sudo lxc remote list --format json");

#Decode JSON data
$items = json_decode($results, true);

echo "<thead>";
echo "<tr>";
echo "<th style='width:75px'></th>";
echo "<th>Name</th>";
echo "<th>Address</th>";
echo "<th>Auth Type</th>";
echo "<th>Protocol</th>";
echo "<th>Comments</th>";
echo "<th style='width:75px'></th>";
echo "</tr>";
echo "</thead>";

echo "<tbody>";


foreach ($items as $item=>$value) {
  $name = $item;
  $addr = $value['Addr'];
  $auth_type = $value['AuthType'];
  $protocol = $value['Protocol'];
  
  $public = $value['Public']; //public images repo
  $domain = $value['Domain']; //currently empty
  $project = $value['Project']; //currently empty
  $statis = $value['Static']; //boolean

  if ($name == "" || $protocol != "lxd")
  continue; 

  echo "<tr>";

  echo '<td> <a href="overview.html?remote=' . $name . '&project=default"> <i class="fas fa-server fa-2x" style="color:#4e73df"></i> </a> </td>';
  echo '<td> <a href="overview.html?remote=' . $name . '&project=default">' . htmlentities($name) . '</a> </td>'; 
  echo "<td>" . htmlentities($addr) . "</td>";
  echo "<td>" . htmlentities($auth_type) . "</td>";
  echo "<td>" . htmlentities($protocol) . "</td>";
  echo "<td></td>"; //no comments for successful remote hosts, used for error message


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

  echo "</tr>";

}


//Display remotes that could not get loaded into the LXC Remotes option from the startup.sh script. 
$db = new SQLite3('/var/dashpod/data/sqlite/dashpod.sqlite');
$result = $db->query('SELECT * FROM lxd_remotes');

while($res = $result->fetchArray()){

  if( !isset($res['name']) || $res['exit_status'] == 0 ) 
    continue;

  echo "<tr>";
  echo "<td> <i class='fas fa-server fa-2x' style='color:#dddddd'></i> </td>";

  echo "<td>" . $res['name'] . "</td>";
  echo "<td> https://" . $res['host'] . ":" . $res['port'] . "</td>";
  echo "<td></td>";
  echo "<td></td>";
  echo "<td>" .$res['return_value'] . "</td>"; //used to display error message from startup script

  echo "<td>";
  echo '<div class="dropdown no-arrow">';
  echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
  echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
  echo '</a>';
  echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
  echo '<div class="dropdown-header">Options:</div>';
  echo '<a class="dropdown-item" href="./php/lxd/remote-remove.php?name='. $res['name'] . '&dbonly=true'  . '">Delete</a>';
  echo '</div>';
  echo '</div>';
  echo "</td>";

  echo "</tr>";

}


echo "</tbody>";

?>
