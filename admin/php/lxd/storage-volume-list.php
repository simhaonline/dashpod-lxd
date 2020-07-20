<?php

if (!isset($_SESSION)) {
  session_start();
}
  
$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$pool = escapeshellarg(filter_var(urldecode($_GET['pool']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);
$pool_url = filter_var(urldecode($_GET['pool']), FILTER_SANITIZE_STRING);

#Get the JSON data
$results = exec("sudo lxc storage volume list $remote:$pool --project $project --format json 2>&1", $output, $return);

if ($return == 0 ) {

  $items = json_decode($results, true);

  #Build the table's data
  echo "<thead>";
  echo "<tr>";
  echo "<th style='width:75px'></th>";
  echo "<th>Name</th>";
  echo "<th>Description</th>";
  echo "<th>Type</th>";
  echo "<th>Used By</th>";
  echo "<th style='width:75px'></th>";
  echo "</tr>";
  echo "</thead>";

  echo "<tbody>";

  foreach ($items as $item) {
    $name = $item['name'];
    $description = $item['description'];
    $type = $item['type'];
    $used_by = $item['used_by']; //array
 

    if ($name == "")
      continue;

    echo "<tr>";
    
    echo "<td> <i class='fas fa-hdd fa-2x' style='color:#4e73df'></i> </td>";
    
    echo "<td>" . htmlentities($name) . "</td>";
    echo "<td>" . htmlentities($description) . "</td>";
    echo "<td>" . htmlentities($type) . "</td>";

    foreach ($used_by as $instance){
      echo "<td>" . htmlentities($instance) . "</td>";
      echo "<br />";
    }
   
/*
    echo "<td>";
      echo '<div class="dropdown no-arrow">';
      echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
      echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
      echo '</a>';
      echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
      echo '<div class="dropdown-header">Options:</div>';

      echo '<a class="dropdown-item" href="storage-volume-edit.html?name='. $name . '&remote=' . $remote_url .'&project=' . $project_url . '&pool=' . $pool_url .  '">Edit</a>';
      echo '<a class="dropdown-item" href="./php/lxd/storage-volume-delete.php?name='. $name . '&remote=' . $remote_url .'&project=' . $project_url  .'&pool=' . $pool_url .  '">Delete</a>';

      echo '</div>';
      echo '</div>';
    echo "</td>";
*/

    echo "</tr>";

  }

  echo "</tbody>";
}
else {
  if ($output == null){
    echo "There seems to be an undefined error.";
  }
  else {
    echo htmlentities($output[1]);
  }
}

?>