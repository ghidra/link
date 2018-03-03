<?php
if (!isset($_SESSION)) {
	session_start();
}
class login{

	var $logged_in = false;
	var $errMsg = '';
	var $mysql;
	var $user_table = '';

	public function __construct($mysql,$post){

		$this->mysql=$mysql;
		$this->user_table = $mysql->user_table;

		if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false)
	      	$this->check_post_data($post);
	    else
	    	$logged_in = true;
		
	}

	private function check_post_data($post){
		if (isset($post['txtUserid'])) {
			$check_to = $this->mysql->get_user_password($this->user_table,$post['txtUserid']);//get the user data
			if($check_to!='denied'){//we are a go, user exists
				$passwordhashed = sha1($post['txtUserpw']);
				if ($passwordhashed === $check_to) {
		        	$_SESSION["logged_in"] = true;
		        	$_SESSION["edit_user"] = $post['txtUserid'];//this is here so I can 
		        	$this->logged_in = true;
		    	} else {
		        	$this->errMsg = 'wrong password';
		    	} 	
			}else{
				$this->errMsg = 'user does not exist';
			}     
		} 
		//------if we are making a new user
		if(isset($post['setuser'])){
			if ($post['setpw1'] === $post['setpw2'] && $post['setpw1'] != '') {//check the password so that it is valid
				$this->mysql->create_user($this->user_table,$post['setuser'],$post['setpw1'],1);
			}else{
				$this->errMsg='something went wrong, with the password';
			}
		}
	}

	public function get_login_page(){
		$page='';
		$setup=$this->mysql->table_exists($this->user_table);
		if(!$setup){//if we have not been set up before, fill out the nessisary information to proceed
			//html_head('link password setup');
			$page.= '<div >You are new to Link, and have not set up a user.';
			$page.= ' Please take the time now to enter a user name and password so that you can have access to';
			$page.= ' the Link interface.</div>';

			///make 3 fields, one user name, 2 passwords
			$page.='<div class="login_form">
 			register new user:
 			<form action="" method="post" name="login_form" id="login_form">
 			u:<input name="setuser" type="text" id="setuser" value="" class="login_input">
 			p:<input name="setpw1" type="password" id="setpw1" value="" class="login_input">
 			p:<input name="setpw2" type="password" id="setpw2" value="" class="login_input">
 			<input type="button" name="Submit" value="Submit" onclick="process_login()">
 			</form>
 			</div>';
		}else{
			$page.='<div class="login_form">
 			<form action="" method="post" name="login_form" id="login_form">
 			<input name="txtUserid" type="text" id="txtUserid" value="user" class="login_input">
 			<input name="txtUserpw" type="password" id="txtUserpw" value="password" class="login_input">
 			<input type="button" name="Submit" value="Submit" onclick="process_login()">
 			</form>
 			</div>';
		}
		return $page;
	}
}

class logout{
	public function __construct(){
		//session_start();
		$_SESSION['logged_in'] = false;
	}
}

?>