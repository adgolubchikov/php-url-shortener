<?php
//Unauth and back to main page

session_start();
$_SESSION['auth']=0;
header("Location: /");

?>
