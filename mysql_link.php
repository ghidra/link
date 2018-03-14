<?php
require_once 'backend/mysql.php';
class mysql_link extends mysql{

	public function __construct(){
		parent::__construct();
		
		include('backend/mysql_login.php');

		$this->mysql_link_table = $mysql_link_table;
		$this->mysql_tag_table = $mysql_tag_table;
		$this->mysql_link_tag_table = $mysql_link_tag_table;
		$this->mysql_link_table = $mysql_link_table;
		//this method only needs to be called right now... during dev.. otherwise this never needs to be called
		//$this->init_tables("no");
	}

	///this is called by the parent class if there is no user table
	public function init_tables($users_table){
		$this->create_users_table($users_table);
		$this->create_link_table();
		$this->create_tag_table();
		$this->create_tag_rel_table();
	}

	//////////////////////////////////////////////
	// create database stuff
	//////////////////////////////////////////////

	function create_link_table(){
		if(!$this->table_exists($this->mysql_link_table))
		{
			mysqli_query($this->conn,"CREATE TABLE $this->mysql_link_table(
				link_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id INT(11) NOT NULL,
				url TEXT NOT NULL,
				description TEXT NOT NULL,
				imagelink VARCHAR(255) NOT NULL,
				posttime DATETIME,
				FOREIGN KEY (user_id) REFERENCES $this->user_table(user_id) ON DELETE CASCADE
				)")or die ($this->errMsg = mysqli_error($this->conn));
		}
	}
	function create_tag_table(){
		if(!$this->table_exists($this->mysql_tag_table))
		{
			mysqli_query($this->conn,"CREATE TABLE $this->mysql_tag_table(
				tag_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				tag VARCHAR(36) NOT NULL UNIQUE KEY,
				user_id INT(11) NOT NULL,
				posttime DATETIME
				)")or die ($this->errMsg = mysqli_error($this->conn));
		}
	}
	function create_tag_rel_table(){
		if(!$this->table_exists($this->mysql_link_tag_table))
		{
			mysqli_query($this->conn,"CREATE TABLE $this->mysql_link_tag_table(
				rel_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				link_id INT(11) NOT NULL,
				tag_id INT(11) NOT NULL,
				FOREIGN KEY (tag_id) REFERENCES $this->mysql_tag_table(tag_id) ON DELETE CASCADE,
				FOREIGN KEY (link_id) REFERENCES $this->mysql_link_table(link_id) ON DELETE CASCADE
				)")or die ($this->errMsg = mysqli_error($this->conn));
		}
	}
	///this table is for is another user likes/links to link another user posted
	// function create_linked(){
	// 	//link id
	// 	//user who liked/linked it
	// 	if(!$this->table_exists($this->mysql_linked_table))
	// 	{
	// 		mysqli_query($this->conn,"CREATE TABLE $this->mysql_linked_table(
	// 			linked_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	// 			link_id INT(11) NOT NULL,
	// 			user VARCHAR(36) NOT NULL,
	// 			posttime DATETIME
	// 			)")or die ($this->errMsg = mysqli_error($this->conn));
	// 	}
	// }

	//////////////////////////////////////////////
	// query tables for data
	//////////////////////////////////////////////

	//This LIKE method doesnt work with tag1, and tag2, it finds the first one... i need to get exact match
	// private function tag_exists($tag)
	// {
	// 	///I might not need this... I can just ignore
	// 	$exists=0;
	// 	$result = mysqli_query($this->conn,"SELECT * FROM $this->mysql_tag_table WHERE tag LIKE '$tag'") or die ($this->errMsg .= 'error trying to find a similar tag');
	// 	if (mysqli_num_rows ($result)>0)$exists=1;
		
	// 	return $exists;
	// }
	private function get_tag_id($tag)
	{
		return mysqli_query($this->conn,"SELECT tag_id FROM $this->mysql_tag_table WHERE tag LIKE '$tag'") or die ($this->errMsg .= 'error trying to find id of tag');
	}

	//////////////////////////////////////////////
	// now fill tables with data
	//////////////////////////////////////////////

	public function add_link($url,$description,$imagelink){
		$user_id = $_SESSION['user_id'];
		$posttime = date("Y-m-d H:i:s");

		//$query = "INSERT INTO $this->mysql_link_table (user, url, description, imagelink, private, posttime) VALUES ('$user_id', '$url', '$description','$imagelink',$private,$posttime)";
		$query = "INSERT INTO $this->mysql_link_table (user_id, url, description, imagelink, posttime) VALUES ('$user_id', '$url', '$description','$imagelink','$posttime')";
		mysqli_query($this->conn,$query) or die($this->errMsg = 'Error, adding link ' . mysqli_error($this->conn)); 
	}

	public function add_tag($tag,$link_id){
		//https://www.phpro.org/tutorials/Tagging-With-PHP-And-MySQL.html
		$user_id = $_SESSION['user_id'];
		$posttime = date("Y-m-d H:i:s");
		$tag_id = -1;

		//if(!$this->tag_exists($tag))
		//{
			//$query = "INSERT INTO $this->mysql_tag_table (tag, user, posttime) VALUES ('$tag','$user_id','$posttime')";
			$query = "INSERT IGNORE INTO $this->mysql_tag_table (tag, user_id, posttime) VALUES ('$tag','$user_id','$posttime')";
			mysqli_query($this->conn,$query) or die($this->errMsg .= 'Error, adding tag ' . mysqli_error($this->conn)); 
			$tag_id = $this->conn->insert_id;
		//}
		//else
		//{
			///the tag already exists.. i need to get the tag id value
			$tag_id = $this->get_tag_id($tag);
			//$this->get_tag_id($tag);
		//}

		$query = "INSERT INTO $this->mysql_link_tag_table (link_id, tag_id) VALUES ($link_id,$tag_id)";
		mysqli_query($this->conn,$query) or die($this->errMsg .= 'Error, adding link tag relationship ' . mysqli_error($this->conn)); 	
	}

	//////////////////////////////////////////////
	// now get data from tables
	//////////////////////////////////////////////

	public function get_all_public_links($begin,$limit)
	{
		$raw =  mysqli_query($this->conn,"SELECT * FROM $this->mysql_link_table ORDER BY link_id DESC LIMIT $begin, $limit") or die($this->errMsg = 'Error, getting all public links, or, there are NO LINKS to get: '. mysqli_error());
		$count=0;
		$arr=array();
		while($info = mysqli_fetch_array( $raw ))
		{
		// 	$arr[$count]=array('id'=>$info['id'] , 
		// 		'user'=>$info['user'], 
		// 		'url'=>$info['url'],
		// 		'description'=>$info['description'] , 
		// 		'imagelink'=>$info['imagelink'],
		// 		'posttime'=>$info['posttime']);
			$arr[$count] = $info;
			$count++;
		}
		return $arr;
	}

	public function get_all_personal_links($begin,$limit)
	{
		$user_id = $_SESSION['user_id'];
		$raw =  mysqli_query($this->conn,"SELECT * FROM $this->mysql_link_table WHERE user_id LIKE $user_id ORDER BY link_id DESC LIMIT $begin, $limit") or die($this->errMsg = 'Error, getting all personal links, or, there are NO LINKS to get: '. mysqli_error());
		$count=0;
		$arr=array();
		while($info = mysqli_fetch_array( $raw ))
		{
			$arr[$count] = $info;
			$count++;
		}
		return $arr;
	}

	public function get_tags()
	{
		$raw =  mysqli_query($this->conn,"SELECT * FROM $this->mysql_tag_table ORDER BY tag_id DESC ") or die($this->errMsg = 'Error, getting all public tags '. mysqli_error());
		$count=0;
		$arr=array();
		while($info = mysqli_fetch_array( $raw ))
		{
			$arr[$count] = $info;
			$count++;
		}
		return $arr;
	}
}
?>