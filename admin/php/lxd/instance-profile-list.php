<?php

if (!isset($_SESSION)) {
  session_start();
}
  
$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$name = escapeshellarg(filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));

$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);
$instance_url = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);


//Get list of profiles from instance
$results = exec("sudo lxc list $remote:$name --project $project --format json 2>&1", $output, $return);
$items = json_decode($results, true);
foreach ($items as $item) {
  $instance_name = $item['name'];
  //Check to make sure name is not empty, also the lxc list command works more like a instance search string, limit to exact match
  if ($instance_name != $instance_url)
    continue;
  $profiles = $item['profiles'];
}
$return = "";
$output = "";



//Get the full list of profiles
$results = exec("sudo lxc profile list $remote: --project $project --format json 2>&1", $output, $return);

if ($return == 0 ) {

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
    $profile_name = $item['name'];
    $devices = $item['devices']; //array
    $description = $item['description'];

    //Filter the list to only inlude profiles for instance
    foreach ($profiles as $profile) {
      if ($profile_name != $profile)
      continue;
      echo "<tr>";
    
      echo "<td> <i class='fas fa-address-card fa-2x' style='color:#4e73df'></i> </td>";
      
      echo "<td>" . htmlentities($profile_name) . "</td>";
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
  
        echo '<a class="dropdown-item" href="profile-edit.html?name='. $profile_name . '&remote=' . $remote_url .'&project=' . $project_url .  '">Edit</a>';
        echo '<a class="dropdown-item" href="./php/lxd/instance-profile-remove.php?name='. $instance_url . '&remote=' . $remote_url .'&project=' . $project_url . '&profile=' . $profile_name . '">Detach Profile</a>';
  
        echo '</div>';
        echo '</div>';
      echo "</td>";
  
      echo "</tr>";

    }

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