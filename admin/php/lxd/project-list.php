<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);

//Set exec time limit to 10 seconds
set_time_limit(10);

#Get the JSON data
$results = exec("sudo lxc project list $remote: --format json 2>&1", $output, $return);

if ($return == 0 ) {

  $projects_array = json_decode($results, true);

  #Build the table's data
  echo "<thead>";
  echo "<tr>";
  echo "<th style='width:75px'></th>";
  echo "<th>Name</th>";
  echo "<th>Description</th>";
  echo "<th>Featured Images</th>";
  echo "<th>Featured Profiles</th>";
  echo "<th>Features Storage Volumes</th>";
  echo "<th style='width:75px'></th>";
  echo "</tr>";
  echo "</thead>";

  echo "<tbody>";

  foreach ($projects_array as $project) {
    $name = $project['name'];
    $description = $project['description'];
    $config_features_images = $project['config']['features.images'];
    $config_features_profiles = $project['config']['features.profiles'];
    $config_features_storage_volumes = $project['config']['features.storage.volumes'];

    if ($name == "")
      continue;

    echo "<tr>";
    
    
    echo "<td> <i class='fas fa-network-wired fa-2x' style='color:#4e73df'></i> </td>";

    
    echo "<td>" . htmlentities($name) . "</td>";
    echo "<td>" . htmlentities($description) . "</td>";
    echo "<td>" . htmlentities($config_features_images) . "</td>";
    echo "<td>" . htmlentities($config_features_profiles) . "</td>";
    echo "<td>" . htmlentities($config_features_storage_volumes) . "</td>";

    echo "<td>";
    echo '<div class="dropdown no-arrow">';
    echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
    echo '</a>';
    echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
    echo '<div class="dropdown-header">Options:</div>';
    echo '<a class="dropdown-item" href="project-edit.html?name='. $name . '&remote=' . $remote_url .'&project=' . $project_url .  '">Edit</a>';
    echo '<a class="dropdown-item" href="./php/lxd/project-delete.php?name='. $name . '&remote=' . $remote_url .'&project=' . $project_url .  '">Delete</a>';

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
