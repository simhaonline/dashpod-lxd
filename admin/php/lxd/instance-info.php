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
  $devices = $item['expanded_devices'];




    echo '<div class="col-12 text-right" style="min-height:50px;">';

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
    echo 'Snapshot';
    echo '</a>';

    if ($status == "Running" && $project_url == "default"){
      echo " | ";
      echo '<a href="#" data-toggle="modal" data-target="#playbookInstanceModal">';
        echo "Playbook";
      echo '</a>';
    }

    if ($status == "Stopped"){

      echo " | ";
      echo '<a href="#" data-toggle="modal" data-target="#renameInstanceModal">';
      echo 'Rename';
      echo '</a>';

      echo " | ";
      echo '<a href="#" data-toggle="modal" data-target="#copyInstanceModal">';
      echo 'Copy';
      echo '</a>';

      echo " | ";
      echo '<a href="#" data-toggle="modal" data-target="#moveInstanceModal">';
      echo 'Move';
      echo '</a>';

      echo " | ";
      echo '<a href="#" data-toggle="modal" data-target="#publishInstanceModal">';
      echo 'Publish';
      echo '</a>';

      echo " | ";
      echo '<a href="#" data-toggle="modal" data-target="#deleteInstanceModal">';
      echo 'Delete';
      echo '</a>';
    }


echo "<br>";
  echo '</div>';



echo '<div class="row">';
echo '<div class="col-12">';

  echo "<strong>Name</strong>: " . htmlentities($name) . "<br />";
  echo "<strong>Description</strong>: " . htmlentities($description) . "<br />";
  echo "<strong>Status</strong>: " . htmlentities($status) . "<br />";
  echo "<strong>Image</strong>: " . htmlentities($image) . "<br />";
  echo "<strong>Memory</strong>: " . htmlentities($memory) . " MB<br />";
  
  echo "<strong>Disk Devices</strong>: ";
  $i = 0;
  foreach ($devices as $device_name=>$device_keys){  
    foreach ($device_keys as $key=>$value){
      if ($key == "type" && $value == "disk"){
        if ($i > 0) 
          echo ", ";
        echo htmlentities($device_name);
        $i++;
      }
    }
  }
  echo "<br />";

  echo "<strong>Network Devices</strong>: ";
  $i = 0;
  foreach ($devices as $device_name=>$device_keys){  
    foreach ($device_keys as $key=>$value){
      if ($key == "type" && $value == "nic"){
        if ($i > 0) 
          echo ", ";
        echo htmlentities($device_name);
        $i++;
      }
    }
  }
  echo "<br />";

  echo "<strong>IPv4 Addresses</strong>: ";
  $i = 0;
  if ($status == "Running"){  
    foreach ($networks as $key=>$network){
      $interfaces = $network['addresses'];
      $interface_name = $key;
      foreach ($interfaces as $interface){
        if ($interface['family'] == "inet" && $interface['address'] != "127.0.0.1"){
          if ($i > 0) 
            echo ", ";
          echo htmlentities($interface['address']);
          $i++;
        }
      }
    } 
  }
  else {
    echo "N/A";
  }
  echo "<br />";

  echo "<strong>IPv6 Addresses</strong>: ";
  $i = 0;
  if ($status == "Running"){  
    foreach ($networks as $key=>$network){
      $interfaces = $network['addresses'];
      $interface_name = $key;
      foreach ($interfaces as $interface){
        if ($interface['family'] == "inet6" && $interface['address'] != "::1" && substr($interface['address'],0,4) != "fe80"){
          if ($i > 0) 
            echo ", ";
          echo htmlentities($interface['address']);
          $i++;
        }
      }
    } 
  }
  else {
    echo "N/A";
  }
  echo "<br />";


  echo '</div>';

echo '</div>';


}

?>