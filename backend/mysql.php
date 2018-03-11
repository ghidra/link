<?php
class mysql{

	var $conn;
	var $user_table = '';
	var $user_name = '';///incase we find it... lets just save this temporarily
	var $user_id = -1;///same with this
	var $errMsg = '';

	public function __construct(){
		include('mysql_login.php');
		$this->user_table = $mysql_user_table;
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
	public function init_tables($users_table){
		$this->create_users_table($users_table);
	}
	public function create_user($table,$user,$password,$permission){
		//if the table does not exist, amek it. This should only ever happen once
		if(!$this->table_exists($table)) $this->init_tables($table);//$this->create_users_table($table);//create the table if it ain't already there
		//I need to make sure that there isn't already a user with the same name. so that it isn't input twice
		$passwordhashed = sha1($password);
		$created = date("Y-m-d H:i:s");
		$query = "INSERT INTO $table (user, password, created, permission) VALUES ('$user', '$passwordhashed', '$created', $permission)";
	
		mysqli_query($this->conn,$query) or die('Error, creating user ' . mysqli_error($this->conn));    
	}
	public function get_user_password($table,$user){
		$all_users = mysqli_query($this->conn,"SELECT * FROM $table ORDER BY user_id DESC") or die( mysqli_error($this->conn));//get info from album table
		while($au = mysqli_fetch_array( $all_users )){
			if($au['user']==$user) {//this user does indeed exists
				//store some of the information for now
				$this->user_id = $au['user_id'];
				$this->user_name = $au['user'];
				return $au['password'] ;
			}else{
				return 'denied';//no user	
			}
		}
	}

	////create database stuff
	function create_users_table($table){
		if(!$this->table_exists($table))
		{
			mysqli_query($this->conn,"CREATE TABLE $table(
				user_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user VARCHAR(36) NOT NULL UNIQUE KEY,
				password VARCHAR(44) NOT NULL,
				created DATETIME,
				permission TINYINT(1) NOT NULL
				)")or die (mysqli_error($this->conn));
		}
	}
}
?>