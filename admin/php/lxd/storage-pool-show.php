<?php

$remote = escapeshellarg(filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING));
$name = escapeshellarg(filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING));


#Get the JSON data
$results = shell_exec("sudo lxc storage show '$remote':'$name'");

echo $results;

?>