<?php
session_start();
$_SESSION = array(); // clear array
session_destroy(); // destroy session
header("location: login.html"); // redirect to login
exit;
?>

