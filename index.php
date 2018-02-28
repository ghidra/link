<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

define('mysql_database_name','link');

if (!isset($_SESSION)) {
	session_start();
}

//session_start();
require_once 'mysql.php';
$mysql = new mysql(mysql_database_name);
//------if we are loging into tentacle
$errMsg = '';
if (isset($_POST['txtUserid'])) {
	// $check_to = $mysql->get_user_password($_POST['txtUserid']);//get the user data
	// if($check_to!='denied'){//we are a go, user exists
	// 	$passwordhashed = sha1($_POST['txtUserpw']);
	// 	if ($passwordhashed === $check_to) {
 //        	$_SESSION["edit_isLogin"] = true;
 //        	$_SESSION["edit_user"] = $_POST['txtUserid'];//this is here so I can send it back to tentacle cloud
	// 		//header('Location:index.php');
 //        	echo '<script>document.location.href="index.php";</script>';
 //           	exit;
 //    	} else {
 //        	$errMsg = 'wrong password';
 //    	} 	
	// }else{
	// 	$errMsg = 'user does not exist';
	// }     
} 
//------if we are making a new user
if(isset($_POST['setuser'])){
	// if ($_POST['setpw1'] === $_POST['setpw2'] && $_POST['setpw1'] != '') {//check the password so that it is valid
	// 	$mysql->create_user($_POST['setuser'],$_POST['setpw1'],1);
	// }else{
	// 	$errMsg='something went wrong, with the password';
	// }
}
//--------------------------
function html_head($title){
	$s='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head>
		<title>'.$title.'</title><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="css/tentacle.css" type="text/css" media="screen" /></head>
		<body><div class="fixed_menu">
				<div class="dd_container">
					<div class="menu_bar_stripe" style="background-color:#CD8C4E;">
						<div class="menu_button_right" style="background-color:black;cursor:default;">tentacle</div>
						<div style="clear:both;"></div>
					</div>
				';
	echo $s;
}
function html_set_password_field(){
	$s='<div style="width:100px;margin-left:auto;margin-right:auto;text-align:center;margin-top:16px;">
		register new user:<br>
		<form action="" method="post" name="setLogin" id="setLogin">
		u:<input name="setuser" type="text" id="setuser" value="" style="width:80px"><br>
		p:<input name="setpw1" type="password" id="setpw1" value="" style="width:80px"><br>
		p:<input name="setpw2" type="password" id="setpw2" value="" style="width:80px"><br>
		<input type="submit" name="Submit" value="Submit">
		</form>
		</div>';
	echo $s;
}
function html_password_field(){
	$s='<div style="width:80px;margin-left:auto;margin-right:auto;text-align:center;margin-top:16px;">
		<form action="" method="post" name="frmLogin" id="frmLogin">
		<input name="txtUserid" type="text" id="txtUserid" value="user" style="width:80px"><br>
		<input name="txtUserpw" type="password" id="txtUserpw" value="password" style="width:80px"><br>
		<input type="submit" name="Submit" value="Submit">
		</form>
		</div>';
	echo $s;
}
function html_end_page(){
	$s='</div>
			</div></body></html>';	
	echo $s;
}
function error_read($err){
	echo '<div style="color:red;width:200px;text-align:center;margin-left:auto;margin-right:auto">'.$err.'</div>';	
}

$setup=$mysql->table_exists('link_users');//check to see if tentacle has been set up before.
if(!$setup){//if we have not been set up before, fill out the nessisary information to proceed
	//$mysql->create_tentacle_users_table();
	html_head('link password setup');
	echo '<div style="width:200px;margin-left:auto;margin-right:auto;margin-top:16px">You are new to Link, and have not set up a user.';
	echo ' Please take the time now to enter a user name and password so that you can have access to';
	echo ' the Link interface.</div><br>';
	
	error_read($errMsg);
	
	html_set_password_field();
	html_end_page();
}else{
	html_head('link password');
	error_read($errMsg);
	html_password_field();
	html_end_page();
}
?>