<?php


if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$name = escapeshellarg(filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$name_url = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);

#Get the JSON data
$results = exec("sudo lxc list $remote:$name --project $project --format json 2>&1", $output, $return);

#Decode the JSON data
$items = json_decode($results, true);


foreach ($items as $item) {

  $instance_name = $item['name'];
  //Check to make sure name is not empty, also the lxc list command works more like a instance search string, limit to exact match
  if ($instance_name != $name_url)
    continue;

  echo '<thead>';
  echo '  <tr>';
  echo "    <th style='width:75px'></th>";
  echo '    <th>Snapshot Name</th>';
  echo '    <th>Stateful/Stateless</th>';
  echo '    <th>Size</th>';
  echo '    <th>Created At</th>';
  echo "    <th style='width:75px'></th>";
  echo '  </tr>';
  echo '</thead>';
  echo '<tbody>';


  $snapshots = $item['snapshots']; //array of snapshots, repeats instance ['config'], ['name'], etc 
  foreach ($snapshots as $key=>$snapshot) {
    $snapshot_name = $snapshot['name'];
    $stateful = $snapshot['stateful'];
    $snapshot_size = number_format($snapshot['size']/1024/1024,2);
    $created_at = $snapshot['created_at'];

    echo "<tr>";
    echo "<td> <i class='fas fa-clone fa-2x' style='color:#4e73df'></i> </td>";
    echo "<td><strong>" . htmlentities($snapshot_name) . "</strong></td>";
    if ($stateful)
      echo "<td>stateful</td>";
    else
    echo "<td>stateless</td>";

    echo "<td>" . htmlentities($snapshot_size) . " MB</td>";
    echo "<td>" . htmlentities($created_at) . "</td>";

    echo "<td>";
    echo '<div class="dropdown no-arrow">';
    echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
    echo '</a>';
    echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
    echo '<div class="dropdown-header">Options:</div>';
    echo '<a class="dropdown-item" href="./php/lxd/instance-snapshot-restore.php?name='. $name_url . '&snapshot=' . $snapshot_name . '&remote=' . $remote_url .'&project=' . $project_url .  '">Restore</a>';
    echo '<a class="dropdown-item" href="./php/lxd/instance-snapshot-delete.php?name='. $name_url . '&snapshot=' . $snapshot_name . '&remote=' . $remote_url .'&project=' . $project_url .  '">Delete</a>';
    echo '</div>';
    echo '</div>';
    echo "</td>";

    echo "<tr>";
  }

  echo "</tbody>";
  
}


?>
