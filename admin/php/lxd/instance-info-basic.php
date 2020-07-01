<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$name = escapeshellarg(filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$name_url = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);

#Get the JSON data
$results = exec("sudo lxc list $remote:$name --project $project --format json 2>&1", $output, $return);

#Decode the JSON data
$items = json_decode($results, true);

foreach ($items as $item) {
 
  $name = $item['name'];
  //Check to make sure name is not empty, also the lxc list command works more like a instance search string, limit to exact match
  if ($name != $name_url)
    continue;
  
  $created = $item['created_at'];
  $description = $item['description'];
  $image = $item['config']['image.description'];
  $status = $item['status']; //ex Running
  $memory = number_format($item['state']['memory']['usage'] / 1048576, 2);
  $networks = $item['state']['network']; //array of network interfaces
  $profiles = $item['profiles'];
  $devices = $item['expanded_devices'];




    echo '<div class="col-12 text-right">';

    if ($status != "Running"){
     
      echo '<a href="./php/lxd/instance-start.php?name=' . $name . '&remote=' . $remote_url . '&project=' . $project_url . '">';
        echo "Start";
      echo '</a>';
    }
    else {
      echo '<a  href="./php/lxd/instance-stop.php?name=' . $name . '&remote=' . $remote_url . '&project=' . $project_url . '">';
        echo "Stop";
      echo '</a>';
    }

    echo " | ";
    echo '<a href="#" data-toggle="modal" data-target="#newSnapshotModal">';
    echo 'Create Snapshot';
    echo '</a>';

    if ($status == "Stopped"){
      echo " | ";
      echo '<a href="#" data-toggle="modal" data-target="#copyInstanceModal">';
      echo 'Copy/Clone';
      echo '</a>';

      echo " | ";
      echo '<a href="./php/lxd/instance-delete.php?name='. $name . '&remote=' . $remote_url . '&project=' . $project_url . '">';
      echo 'Delete';
      echo '</a>';
    }

    if ($status == "Running"){
      echo " | ";
      echo '<a href="#" data-toggle="modal" data-target="#playbookInstanceModal">';
        echo "Playbook";
      echo '</a>';
    }

echo "<br>";
  echo '</div>';



echo '<div class="row">';
echo '<div class="col-6">';

  echo "<h3>" . htmlentities($name) . "</h3>";
  echo "Description: " . htmlentities($description) . "<br />";
  echo "Status: " . htmlentities($status) . "<br />";
  echo "Image: " . htmlentities($image) . "<br />";
  echo "Memory: " . htmlentities($memory) . " MB<br />";

  echo "Profiles: ";
  $i = 0;
  foreach ($profiles as $profile) {
    if ($i > 0)
      echo ", ";
    echo htmlentities($profile);
    $i++;
  }
  echo "<br />";

  echo "Devices: ";
  $i = 0;
  foreach ($devices as $device_name=>$device_keys){
    if ($i > 0)
      echo ", ";
    echo htmlentities($device_name);
    foreach ($device_keys as $key=>$value){
      if ($key == "type")
        echo "(" . htmlentities($value) . ")";
      else
        continue;
    }
    $i++;
  }
  echo "<br />";


  echo '</div>';



  echo '<div class="col-6" style="margin-top: 2.5rem;">';
    if ($status == "Running"){  
      echo "IPv4 Addresses: <br />";
      foreach ($networks as $key=>$network){
        $interfaces = $network['addresses'];
        $interface_name = $key;
        foreach ($interfaces as $interface){
          if ($interface['family'] == "inet" && $interface['address'] != "127.0.0.1"){
            echo " - " . htmlentities($interface_name) . ": " . htmlentities($interface['address']);
            echo "<br />";
          }
        }
      } 

      echo "IPv6 Addresses: <br />";
      foreach ($networks as $key=>$network){
        $interfaces = $network['addresses'];
        $interface_name = $key;
        foreach ($interfaces as $interface){
          if ($interface['family'] == "inet6" && $interface['address'] != "::1" && substr($interface['address'],0,4) != "fe80"){
            echo " - " . htmlentities($interface_name) . ": " . htmlentities($interface['address']);
            echo "<br />";
          }
        }
      } 

    }

  echo '</div>';

echo '</div>';


}

?>