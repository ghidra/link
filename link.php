<?php
// if (!isset($_SESSION)) {
// 	session_start();
// }

require_once 'mysql_link.php';
require_once 'backend/login.php';


function logout_button()
{
	$s='<div>
		<div id="user_id" class="invisible">'.$_SESSION['user_id'].'</div>
		<div id="user_name">'.$_SESSION['user'].'</div>
		<button onclick="logout()">logout</button>
	</div>';

	return $s;
}
function new_link_page()
{
	$s='<div id="container_new_link">
		<form name="new_link_form" id="new_link_form">
			<input name="new_link" type="text" id="new_link" value="http://" class="new_link_input">
			<input name="new_desc" type="text" id="new_desc" value="description" class="new_desc_input">
			<input name="new_tags" type="text" id="new_tags" value="tag1, tag2" class="new_tags_input">
			<input type="button" name="Submit" value="Submit" onclick="process_new_link()">
		</form>
	</div>';

	return $s;
}

////
function link_page($link_data)
{
	/*
		$link_data['id']
		$link_data['user']
		$link_data['url']
		$link_data['description']
		$link_data['imagelink']
		$link_data['private']
		$link_data['posttime']
	*/
	$s='<div id="container_link">
		<a href="'.$link_data['url'].'"><div id="link_ahref">'.$link_data['url'].'</div></a>
		<div id="link_description">'.$link_data['description'].'</div>
		<div id="link_posttime">'.$link_data['posttime'].'</div>
	</div>';

	return $s;
}

function get_focused_links($focus)
{
	///first stop on making the link page. This is where we determine how to look into the database
	$mysql = new mysql_link();
	$fetched_links = $mysql->get_all_public_links(0,10);

	$s=$mysql->errMsg;

	for($i=0;$i<count($fetched_links); $i++)
	{
		$s.=link_page($fetched_links[$i]);
	}

	echo $s;

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
		//get the user information

		return logout_button();
	}
}

/////////////////////

//this method adds to the database... doesnt need to return anything, if successful, the links list is refreshed
function process_new_link($payload){
	$mysql = new mysql_link();
	$login = new login($mysql,$payload);

	if(!$login->logged_in)
	{
		$mysql->add_link($payload['new_link'],$payload['new_desc'],'fake image link',0);
	}

	return $mysql->errMsg;
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

	if($_GET['q']=='links_page')
	{
		echo get_focused_links(json_decode($_GET['payload']));
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
	///we are given a new link to process and put into the database
	if($_POST['q']=='process_new_link')
	{
		echo process_new_link($_POST);
	}
}

?>