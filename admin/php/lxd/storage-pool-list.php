<?php

if (!isset($_SESSION)) {
  session_start();
}
  
$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);

#Get the JSON data
$results = exec("sudo lxc storage list $remote: --project $project --format json 2>&1", $output, $return);

if ($return == 0 ) {

  $items = json_decode($results, true);

  #Build the table's data
  echo "<thead>";
  echo "<tr>";
  echo "<th style='width:75px'></th>";
  echo "<th>Name</th>";
  echo "<th>Driver </th>";
  echo "<th>Source</th>";
  echo "<th>Size</th>";
  echo "<th style='width:75px'></th>";
  echo "</tr>";
  echo "</thead>";

  echo "<tbody>";

  foreach ($items as $item) {
    $name = $item['name'];
    $driver = $item['driver']; //array
    $source = $item['config']['source'];
    $size = $item['config']['size'];


    if ($name == "")
      continue;

    echo "<tr>";
    
    echo "<td> <i class='fas fa-hdd fa-2x' style='color:#4e73df'></i> </td>";
    
    echo "<td>" . htmlentities($name) . "</td>";
    echo "<td>" . htmlentities($driver) . "</td>";
    echo "<td>" . htmlentities($source) . "</td>";
    echo "<td>" . htmlentities($size) . "</td>";


    echo "<td>";
      echo '<div class="dropdown no-arrow">';
      echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
      echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
      echo '</a>';
      echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
      echo '<div class="dropdown-header">Options:</div>';

      echo '<a class="dropdown-item" href="storage-pool-edit.html?name='. $name . '&remote=' . $remote_url .'&project=' . $project_url .  '">Edit</a>';
      echo '<a class="dropdown-item" href="./php/lxd/storage-pool-delete.php?name='. $name . '&remote=' . $remote_url .'&project=' . $project_url .  '">Delete</a>';

      echo '</div>';
      echo '</div>';
    echo "</td>";

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