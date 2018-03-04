<?php
require_once 'backend/mysql.php';
class mysql_link extends mysql{

	public function __construct(){
		parent::__construct();
		
		include('backend/mysql_login.php');

		$this->mysql_link_table = $mysql_link_table;
		$this->mysql_tag_table = $mysql_tag_table;
		$this->mysql_link_tag_table = $mysql_link_tag_table;
		//this method only needs to be called right now... during dev.. otherwise this never needs to be called
		//$this->init_tables("no");
	}

	///this is called by the parent class if there is no user table
	public function init_tables($users_table){
		$this->create_users_table($users_table);
		$this->create_link_table();
		$this->create_tag_table();
		$this->create_link_tag_table();
	}

	////create database stuff
	function create_link_table(){
		if(!$this->table_exists($this->mysql_link_table))
		{
			mysqli_query($this->conn,"CREATE TABLE $this->mysql_link_table(
				id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user INT(11) NOT NULL,
				url TEXT NOT NULL,
				description TEXT NOT NULL,
				imagelink VARCHAR(255) NOT NULL,
				posttime DATETIME
				)")or die ($this->errMsg = mysqli_error($this->conn));
		}
	}
	function create_tag_table(){
		if(!$this->table_exists($this->mysql_tag_table))
		{
			mysqli_query($this->conn,"CREATE TABLE $this->mysql_tag_table(
				id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				tag VARCHAR(36) NOT NULL,
				user VARCHAR(36) NOT NULL,
				posttime DATETIME
				)")or die ($this->errMsg = mysqli_error($this->conn));
		}
	}
	function create_link_tag_table(){
		if(!$this->table_exists($this->mysql_link_tag_table))
		{
			mysqli_query($this->conn,"CREATE TABLE $this->mysql_link_tag_table(
				id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				link INT(11) NOT NULL,
				tag INT(11) NOT NULL
				)")or die ($this->errMsg = mysqli_error($this->conn));
		}
	}
}
?>