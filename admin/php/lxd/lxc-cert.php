<?php

$results = shell_exec("cat /var/dashpod/data/lxc/client.crt");

echo htmlentities($results);

?>
