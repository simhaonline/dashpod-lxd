<?php

if (!isset($_SESSION)) {
  session_start();
}

$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$name = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);

#Get the YAML data
$results = shell_exec("sudo lxc list '$remote':'$name' --format yaml");

echo "<pre>";
print_r($results);
echo "</pre>";

?>
