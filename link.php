<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


if ( isset($_GET['q'])  )
{
	require_once 'mysql_link.php';
	require_once 'backend/login.php';

	if($_GET['q']=='logout')
	{
		$login = new logout();
	}
	if($_GET['q']=='login' or $_GET['q']=='logout')
	{
		$mysql = new mysql_link();

		///get the payload if there is one
		if(isset($_GET['payload']) )
		{
			$login = new login($mysql,json_decode($_GET['payload'],true));
		}
		else
		{
			$login = new login($mysql,$_POST);///this will never have anything like this... but it wont fail i guess
		}
		
		if(!$login->logged_in)
		{
			echo $login->errMsg;//this will also spit out the error messages
			echo $login->get_login_page();
		}
		else
		{
			echo '<button onclick="logout()">logout</button>';
		}
	}

}

?>