<?php
//Adding data to the database and redirect to the main page

include 'core.php';
session_start();

if ((isset($_SESSION['auth'])) && ($_SESSION['auth']==1))
{
	$newid=add($_POST['link']);
	$_SESSION['justadded']=1;
	$_SESSION['justaddedlink']=$_POST['link'];
	$_SESSION['justaddedshortlink']='https://'.$domain.'/r'.$newid;
	header('Location: /');
}
else
{
	echo 'Not authorized';
}

?>
