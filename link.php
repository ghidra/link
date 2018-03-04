<?php
//session_start();

require_once 'mysql_link.php';
require_once 'backend/login.php';


function logout_button()
{
	return '<button onclick="logout()">logout</button>';
}
function new_link_page()
{
	$s='<div id="container_new_link">
		<form name="new_link_form" id="new_link_form">
			<input name="new_link" type="text" id="new_link" value="http://" class="new_link_input">
			<input name="new_desc" type="text" id="new_desc" value="description" class="new_desc_input">
			<input name="new_tags" type="text" id="new_tags" value="tag1, tag2" class="new_tags_input">
			<input type="button" name="Submit" value="Submit" onclick="add_new_link()">
		</form>
	</div>';

	return $s;
}

////
function link_page()
{
	$s='<div id="container_link">
		<!---image
		description
		link
		date
		-->
	</div>';
}

//////////////////
function attemp_login($payload){
	$mysql = new mysql_link();
	$login = new login($mysql,$payload);

	if(!$login->logged_in)
	{
		return $mysql->errMsg . $login->errMsg . $login->get_login_page();
	}
	else
	{
		return logout_button();
	}
}
/////////////////////
if ( isset($_GET['q'])  )
{
	if($_GET['q']=='logout')
	{
		$logout = new logout();
		echo attemp_login($_GET);
	}

	if($_GET['q']=='login')	
	{
		if(isset($_SESSION['logged_in']))
		{
			echo logout_button();
		}
		else
		{
			echo attemp_login($_GET);//json_decode($_GET['payload'],true);
		}
	}

	if($_GET['q']=='new_link_page')
	{
		if(isset($_SESSION['logged_in']))
		{
			echo new_link_page();
		}
	}
}

////passwords are send via post
if ( isset($_POST['q'])  )
{
	if($_POST['q']=='login')
	{
		echo attemp_login($_POST);
	}
}

?>