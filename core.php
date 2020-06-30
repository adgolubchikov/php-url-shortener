<?php
//Configuration
$db_server='localhost';
$db_login='login_mysql';
$db_pass='password_for_database';
$db_name='database_name';

$domain='domain.tld';
$default_url='https://golubchikov.ml/';

$admin_pass='ZE4wGoWNCXuYpfCz';

$base=20; //Links per page

function myhash($num)
{
	$result = ($num*17+1422) + (($num*106+909) % 13202) + mt_rand(0, 1000000); //Created the hash
	$byte1 = $result % 256;
	$byte2 = (($result-$byte1) % 65536)/256;
	$byte3 = (($result-$byte1-(256*$byte2)) % 16777216)/65536;
	$str = base64_encode(chr($byte1).chr($byte2).chr($byte3));
	$str = str_replace('/', '_', $str);
	$str = str_replace('+', '-', $str);
	return $str;
}

function show()
{ //Show links list
	global $db_server, $db_login, $db_pass, $db_name, $base;
    $link = mysql_connect($db_server, $db_login, $db_pass)
    or die('Не удалось соединиться: ' . mysql_error());
    mysql_select_db($db_name) or die('Не удалось выбрать базу данных');


    //Range determination
    if ((isset($_GET['page'])) && ($_GET['page']>=2))
    {
		$limit=($_GET['page']-1)*$base;
		$current_page=$_GET['page'];
	}
	else
	{
		$limit=0;
		$current_page=1;
	}

    //Finding hoow many links and pages
    $length_query=mysql_query('SELECT COUNT(1) FROM links');
    $length_array=mysql_fetch_array($length_query);
    $num=$length_array[0]; //num = total links
    $n=($num - ($num % $base))/$base; if ($num % $base!=0) { $n++; } //n = total pages
    
    
    $pages='Page: ';
    
    if ($current_page==1)
	{
		$pages.='1&nbsp;';
	}
	else
	{
		$pages.='<a href="/">1</a> ';
	}
		
    for ($i=2;$i<=$n;$i++)
    {
		if ($current_page==$i)
		{
			$pages.=$i.' ';
		}
		else
		{
			$pages.='<a href="/?page='.$i.'">'.$i.'</a> ';
		}
	}

    $query = 'SELECT * FROM links ORDER BY id DESC LIMIT '.$limit.', '.$base;
    $result = mysql_query($query) or die('MySQL error: ' . mysql_error());

    while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
    {
		//Making pretty readable time between now and link creation moment
		$original_time=date_create($line['time']);
		$now=date_create();
		$diff=date_diff($original_time, $now);
		$str='';
		if ($diff->y<>0) $str.=$diff->y.' years ';
		if ($diff->m<>0) $str.=$diff->m.' months ';
		if (($diff->d<>0) && ($diff->y==0)) $str.=$diff->d.' days ';
		if (($diff->h<>0) && ($diff->m==0) && ($diff->y==0)) $str.=$diff->h.' hours ';
		if (($diff->i<>0) && ($diff->d==0) && ($diff->m==0) && ($diff->y==0)) $str.=$diff->i.' min ';
		if (($diff->s<>0) && ($diff->h==0) && ($diff->d==0) && ($diff->m==0) && ($diff->y==0)) $str.=$diff->s.' sec ';
		$str.='ago';
		//Printing the time
        echo "<tr>";
        echo '<td style="max-width: 700px; word-wrap:break-word;">'.$line['link'].'</td>';
        echo '<td>'.$str.'</td>';
        echo '<td><a href="https://'.$domain.'/r'.$line['hash'].'" target="_blank">https://'.$domain.'/r'.$line['hash'].'</a></td>';
        echo '<td>'.$line['count'].'</td>';
        echo '<td><button id="deletebutton" class="mdl-button mdl-js-button mdl-button--raised mdl-button--accent" onclick="deletelink('.$line['id'].')">X</button></td>';

        echo "</tr>\n";
    }
    

    mysql_free_result($result);

    mysql_close($link);
    
    echo '<tr id="pages"><td>'.$pages.'</td></tr>';
}


function add($url)
{ //Add new link to the database
	global $db_server, $db_login, $db_pass, $db_name;
    $link = mysql_connect($db_server, $db_login, $db_pass)
    or die('Unable to connect: ' . mysql_error());
    mysql_select_db($db_name) or die('Unable to select the database');


    $query = 'SELECT MAX(`id`) FROM `links`';
    $result = mysql_query($query) or die('MySQL error: ' . mysql_error()); 
    
    $maxid = mysql_result($result, 0); //Finding the last ID
    
    $res = myhash($maxid+1);
    $query = 'INSERT INTO `links` (`id`, `link`, `count`, `hash`) VALUES ('.($maxid+1).', \''.$url.'\', 0, \''.$res.'\')'; //Query for adding new link
    mysql_query($query) or die('MySQL error: ' . mysql_error());
    

    mysql_close($link);
    
    return $res;
}

function deletelink($linkid)
{ //Deleting the link
	global $db_server, $db_login, $db_pass, $db_name;
    $link = mysql_connect($db_server, $db_login, $db_pass)
    or die('Unable to connect: ' . mysql_error());
    mysql_select_db($db_name) or die('Unable to select the database');


    
    $query = 'DELETE FROM `links` WHERE `id` = '.$linkid; //Query for deleting
    mysql_query($query) or die('MySQL error: ' . mysql_error());
    

    mysql_close($link);
}


function increment($id)
{ //Incrementing number of clicks to the short URL
	global $db_server, $db_login, $db_pass, $db_name;
    $link = mysql_connect($db_server, $db_login, $db_pass)
    or die('Unable to connect: ' . mysql_error());
    mysql_select_db($db_name) or die('Unable to select the database');


    $query = 'SELECT * FROM links WHERE hash = \''.$id.'\'';
    $result = mysql_query($query) or die('MySQL error: ' . mysql_error());

    $line = mysql_fetch_array($result, MYSQL_ASSOC);
    
    if (isset($line['link']))
    {
        header ('Location: '.$line['link']); //Redirecting to full URL
	}
	else
	{
		header('Location: '.$default_url); //Error, redirecting to default URL
	}
    
    echo 'If nothing happens, please click <a href="'.$line['link'].'"here</a>';
    
    $c = $line['count']; //Number of clicks
    
    mysql_query('UPDATE `links` SET `count` = '.($c+1).' WHERE `hash`=\''.$id.'\''); //Query for updating the number of clicks


    mysql_free_result($result);
    mysql_close($link);
    
}
?>
