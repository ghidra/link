<?php

require_once 'mysql_link.php';
require_once 'backend/login.php';

function attemp_login($payload){
	$mysql = new mysql_link();
	$login = new login($mysql,$payload);

	if(!$login->logged_in)
		return $login->errMsg . $login->get_login_page();
	else
		return '<button onclick="logout()">logout</button>';
}

if ( isset($_GET['q'])  )
{
	if($_GET['q']=='logout')
		$login = new logout();

	if($_GET['q']=='login' or $_GET['q']=='logout')
		echo attemp_login($_GET);//json_decode($_GET['payload'],true);
}

////passwords are send via post
if ( isset($_POST['q'])  )
	echo attemp_login($_POST);

?>