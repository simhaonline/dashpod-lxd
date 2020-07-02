<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));
$remote_url = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$project_url = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);

$results = exec("sudo lxc project list $remote: --format json 2>&1", $output, $return);


if ($return == 0 ) {

  echo '<li class="nav-item">';
  echo '<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">';
  echo '<i class="fas fa-fw fa-project-diagram"></i>';
  echo '<span>Projects ('. htmlentities($project_url) . ')</span>';
  echo '</a>';
  echo '<div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">';
  echo '<div class="bg-white py-2 collapse-inner rounded">';
  
  echo '<a class="collapse-item" href="projects.html?remote=' . $remote_url . '&project=' . htmlentities($project_url) . '">Manage projects</a>';
  echo '<h6 class="collapse-header">Project select:</h6>';

  $projects_array = json_decode($results, true);

  foreach ($projects_array as $project) {
    $name = $project['name'];

    if ($name == "")
      continue;
    
    if ($name == $project_url)
      echo '<a class="collapse-item active" href="overview.html?remote=' . $remote_url . '&project=' . $name . '">' . htmlentities($name) . '</a>';
    else
      echo '<a class="collapse-item" href="overview.html?remote=' . $remote_url . '&project=' . $name . '">' . htmlentities($name) . '</a>';
  }

  echo '</div>';
  echo '</div>';
  echo '</li>';

}


?>