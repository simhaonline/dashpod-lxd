<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);

#Get the JSON data
$results = exec("sudo lxc operation list $remote: --project $project --format json 2>&1", $output, $return);

if ($return == 0 ) {

  #Decode JSON data
  $items = json_decode($results, true);

  #Build the table's data
  echo "<thead>";
  echo "<tr>";
  echo "<th style='width:75px'></th>";
  echo "<th>ID</th>";
  echo "<th>Class</th>";
  echo "<th>Description</th>";
  echo "<th>Status</th>";
  echo "<th>Created</th>";
  echo "<th>Cancelable</th>";
  echo "<th style='width:75px'></th>";
  echo "</tr>";
  echo "</thead>";

  echo "<tbody>";

  foreach ($items as $item) {
    $id = $item['id'];
    $class = $item['class'];
    $description = $item['description'];
    $status = $item['status'];
    $created_at = $item['created_at'];
    $may_cancel = ($item['may_cancel'])?"true":"false";

    if ($id == "")
      continue;

    echo "<tr>";
    echo '<td> <a href="./operation.html?remote=' . $remote_url . '&project=' . $project_url . '&name=' . htmlentities($id) . '"> <i class="fas fa-exchange-alt fa-2x" style="color:#4e73df"> </i> </a> </td>';

    echo '<td> <a href="./operation.html?remote=' . $remote_url . '&project=' . $project_url . '&name=' . htmlentities($id) . '">' . htmlentities($id) . '</a> </td>';
    
    echo "<td>" . htmlentities($class) . "</td>";
    echo "<td>" . htmlentities($description) . "</td>";
    echo "<td>" . htmlentities($status) . "</td>";
    echo "<td>" . htmlentities($created_at) . "</td>";
    echo "<td>" . htmlentities($may_cancel) . "</td>";

    echo "<td>";
      echo '<div class="dropdown no-arrow">';
      echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
      echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
      echo '</a>';
      echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
      echo '<div class="dropdown-header">Options:</div>';

      echo '<a class="dropdown-item" href="./php/lxd/operation-delete.php?id='. $id . '&remote=' . $remote_url . '&project=' . $project_url . '">Delete</a>';

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
