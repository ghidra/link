<?php
require_once 'backend/mysql.php';
class mysql_link extends mysql{

	public function __construct(){
		parent::__construct();
	}

	////create database stuff
	function create_users_table($table){
		mysqli_query($this->conn,"CREATE TABLE $table(
			id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user VARCHAR(36) NOT NULL,
			password VARCHAR(44) NOT NULL,
			permission TINYINT(1) NOT NULL
			)")or die (mysqli_error($this->conn));
	}
}
?>