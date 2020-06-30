<?php

$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);

//remove special characters 
$remote  = preg_replace('/[^a-zA-Z0-9\.\_\-]/s','-',$remote);

//Set exec time limit to 10 seconds
set_time_limit(10);

#Get the JSON data
//$results = shell_exec("sudo lxc config trust list '$remote': --format json");
$results = exec("sudo lxc config trust list '$remote': --format json 2>&1", $output, $return);

if ($return == 0 ) {

  #Decode JSON data
  $items = json_decode($results, true);

  echo "<thead>";
  echo "<tr>";
  echo "<th style='width:75px'></th>";
  echo "<th>Name</th>";
  echo "<th>Type</th>";
  echo "<th>Fingerprint</th>";
  echo "<th style='width:75px'></th>";
  echo "</tr>";
  echo "</thead>";


  echo "<tbody>";

  foreach ($items as $item) {
    $name = $item['name'];
    $type = $item['type'];
    $certificate = $item['certificate'];
    $fingerprint = $item['fingerprint'];

    if ($name == "")
      continue;

    echo "<tr>";
    
    echo "<td> <i class='fas fa-wallet fa-2x' style='color:#4e73df'></i> </td>";
    echo "<td> <strong>" . htmlentities($name) . "</strong></td>";
    echo "<td>" . htmlentities($type) . "</td>";
    echo "<td>" . htmlentities($fingerprint) . "</td>";


    echo "<td>";
    echo '<div class="dropdown no-arrow">';
    echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
    echo '</a>';
    echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
    echo '<div class="dropdown-header">Options:</div>';

    echo '<a class="dropdown-item" href="./php/lxd/trust-remove.php?fingerprint='. $fingerprint . '&remote=' . $remote . '">Delete</a>';

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
