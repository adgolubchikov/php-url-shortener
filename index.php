<?php
include 'core.php';

if (isset($_GET['redir']))
{
	//Redirecting to the full link and incrementing clicks number
    increment(substr($_GET['redir'], 0, 4));
}
else
{
	//Link manager
	session_start();
	if ((isset($_SESSION['auth'])) && ($_SESSION['auth']==1))
	{		
		//Beginning of link manager
		?>
<!DOCTYPE html>
<html>
<head>
    <title>Сокращатор ссылок</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/validate.js/0.12.0/validate.min.js"></script>

    <link rel="stylesheet" href="style.css" />
</head>
<body onload="redraw()" onresize="redraw()">
    <script type="text/javascript" src="script.js"></script>
    
	<!-- Form that removing the link -->
	<form action="delete.php" id="deletelink" method="post">
	    <input type="text" value="0" name="id" id="id_to_delete" />
	</form>
	
    <center>
    <table id="tableform" align="center">
		<tr>
        <td align="left">
	    <form action="add.php" id="form" method="post" onsubmit="check();return false">
            Link: <input style="display: inline" class="mdl-textfield__input" type="text" id="link" name="link" />&nbsp;
            <input class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" type="submit" value="Add" /> 
        </form> 
        </td>
        <td align="right">
            <div id="exitbutton"><a href="unauth.php"><button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">Log out</button></a></div>
        </td>
        </tr>
    </table>

    <table class="mdl-data-table mdl-js-data-table" id="tabledata">
		<?php
		if ((isset($_SESSION['justadded'])) && ($_SESSION['justadded']==1))
		{
			$_SESSION['justadded']=0;
			echo '<tr>Link <span id="justaddedlink">'.$_SESSION['justaddedlink'].'</span> shortened to <span id="justaddedshortlink">'.$_SESSION['justaddedshortlink'].'</span>. <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" id="justaddedbutton" onclick="copylink()">Copy</button> <span id="copied"></span></tr>';
		}
		
		?>
	    <tr>
		    <th class="mdl-data-table__cell--non-numeric">Original URL</th>
		    <th>Created</th>
		    <th>Short URL</th>
		    <th>Clicks</th>
		    <th>DEL</th>
	    </tr>
<?php
show();

?>

    </table>
    </center>
    <br /><br />
</body>
</html>
<?php
    //End of link manager
	}
	else
	{
		//Beginning of authorization page
		?>
<!DOCTYPE html>
<html>
<head>
    <title>Authorization</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
</head>
<body>
	<script type="text/javascript">
	function id(e){return document.getElementById(e);}
	function check()
	{
		if (id("login").value=="")
		{
			id("login").focus();
			return false;
		}
		if (id("password").value=="")
		{
			id("password").focus();
			return false;
		}
	id("form").submit();
	}
	
	</script>
<center>
<form action="auth.php" id="form" method="post" onsubmit="check();return false">
Username: <input type="text" id="login" name="login" /><br />
Password: <input type="password" id="password" name="password" /><br />
<input class="mdl-button mdl-js-button mdl-button--raised" type="submit" value="Log in" /></form>
</center>
</body>
</html>
<?php

    //End of authorization page
	}
	
}
?>
