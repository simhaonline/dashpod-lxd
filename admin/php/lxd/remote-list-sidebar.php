<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);

$results = exec("sudo lxc remote list --format json 2>&1", $output, $return);


if ($return == 0 ) {

  echo '<li class="nav-item">';
  echo '<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRemotesSidebar" aria-expanded="true" aria-controls="collapseRemotesSidebar">';
  echo '<i class="fas fa-fw fa-server"></i>';
  echo '<span style="display:inline-flex;width:80%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis"> Host: '. htmlentities($remote_url) . '</span>';
  echo '</a>';
  echo '<div id="collapseRemotesSidebar" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">';
  echo '<div class="bg-white py-2 collapse-inner rounded">';
  
  echo '<a class="collapse-item" href="index.html?remote=' . $remote_url . '&project=' . $project_url . '">Manage hosts</a>';
  echo '<hr style="color:#3a3b45;background-color:#3a3b45;width:80%;">';

  $items = json_decode($results, true);

  foreach ($items as $item=>$value) {
    $name = $item;
    $protocol = $value['Protocol'];
  
    if ($name == "" || $protocol != "lxd")
    continue; 
    
    if ($name == $remote_url)
      echo '<a style="display:inline-flex;width:80%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis" class="collapse-item active" href="overview.html?remote=' . $name . '&project=' . $project_url . '">' . htmlentities($name) . '</a>';
    else
      echo '<a style="display:inline-flex;width:80%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis" class="collapse-item" href="overview.html?remote=' . $name . '&project=default">' . htmlentities($name) . '</a>';
  }

  echo '</div>';
  echo '</div>';
  echo '</li>';

}


?>