<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$name = escapeshellarg(filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING));
$project = escapeshellarg(filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING));

#Get the YAML data
$results = shell_exec("sudo lxc config show $remote:$name --project $project --expanded"); //returns yaml format

echo "<pre>";
print_r($results);
echo "</pre>";

?>