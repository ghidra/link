<?php
class mysql{
	var $user=array();
	var $conn;

	public function __construct($mysql_database_name){
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
}
?>