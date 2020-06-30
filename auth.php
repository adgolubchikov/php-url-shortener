<?php
include 'core.php';

//Checking login and password, creating a session and redirect to the main page

if (($_POST['login']=='admin') && ($_POST['password']==$admin_pass))
{
	session_start();
	$_SESSION['auth']=1;
	header("Location: /");
}
else
{
	echo 'Incorrect login or password';
}
?>
