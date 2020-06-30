<?php
//Deleting link from the database and redirect to the main page
include 'core.php';
session_start();

if ((isset($_SESSION['auth'])) && ($_SESSION['auth']==1))
{
	deletelink($_POST['id']);
	header('Location: /');
}
else
{
	echo 'Not authorized';
}

?>
