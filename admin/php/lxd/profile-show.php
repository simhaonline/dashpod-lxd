<?php

$remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
$name = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);


#Get the JSON data
$results = shell_exec("sudo lxc profile show '$remote':'$name'");

echo $results;

?>