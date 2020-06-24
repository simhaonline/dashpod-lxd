<?php

//start session
if (!isset($_SESSION)) {
    session_start();
}

//if session variable is not an empty string, echo it
if ($_SESSION['alert'] != "")
  echo $_SESSION['alert'];

//set session variable to empty string
$_SESSION['alert'] = "";

?>
