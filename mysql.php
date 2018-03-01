<?php
class mysql{
	var $user=array();
	var $conn;
	var $database_name='';

	public function __construct($mysql_database_name){
		$this->database_name = $mysql_database_name;
		include_once('mysql_login.php');
		$this->conn = mysqli_connect ($mysql_host, $mysql_user, $mysql_pass) or die ("I cannot connect to the database because: " . mysqli_error($this->conn));
		mysqli_select_db ($this->conn,$mysql_database_name) or die ("I cannot select the database '$mysql_database_name' because: " . mysqli_error($this->conn));
	}

	//-------------------------------------
	//      check if table exists
	//-------------------------------------
	//http://www.electrictoolbox.com/check-if-mysql-table-exists/php-function/
	public function table_exists($table){
		$exists=0;
		$result = mysqli_query($this->conn,"SHOW TABLES LIKE '$table'") or die ('error reading database while looking for a specific table');
		if (mysqli_num_rows ($result)>0)$exists=1;
		
		return $exists;
	}
	//
	public function create_user($table,$user,$password,$permission){
		//if the table does not exist, amek it. This should only ever happen once
		if(!$this->table_exists($table)) $this->create_users_table($table);//create the table if it ain't already there
		//I need to make sure that there isn't already a user with the same name. so that it isn't input twice
		$passwordhashed = sha1($password);
		$query = "INSERT INTO '$user' (user, password, permission) 
				  VALUES ('$user', '$passwordhashed', '$permission')";
	
		mysqli_query($query) or die('Error, creating user ' . mysqli_error());    
	}
	public function get_user_password($user){
		// $all_users = mysqli_query("SELECT * FROM tentacle_users ORDER BY id DESC") or die( mysql_error());//get info from album table
		// while($au = mysql_fetch_array( $all_users )){
		// 	if($au['user']==$user) {//this user does indeed exists
		// 		return $au['password'] ;
		// 	}else{
		// 		return 'denied';//no user	
		// 	}
		// }
	}

	////create database stuff
	function create_users_table($name){
		mysqli_query("CREATE TABLE $name(
			id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user VARCHAR(36) NOT NULL,
			password VARCHAR(44) NOT NULL,
			permission TINYINT(1) NOT NULL
			)")or die (mysqli_error());
	}
}
?>