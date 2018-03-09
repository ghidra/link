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
			<input name="new_link" type="text" id="new_link" placeholder="http://" class="new_link_input">
			<input name="new_desc" type="text" id="new_desc" placeholder="description" class="new_desc_input">
			<input name="new_tags" type="text" id="new_tags" placeholder="tag1, tag2" class="new_tags_input">
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

	if(count($fetched_links)<1)
	{
		$s.="Unbeleivable, there are no links here.";
	}
	else
	{
		for($i=0;$i<count($fetched_links); $i++)
		{
			$s.=link_page($fetched_links[$i]);
		}
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
$baseUrl = '/';


$regularExpression  = "((https?|ftp)\:\/\/)?"; // SCHEME Check
$regularExpression .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass Check
$regularExpression .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP Check
$regularExpression .= "(\:[0-9]{2,5})?"; // Port Check
$regularExpression .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path Check
$regularExpression .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query String Check
$regularExpression .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor Check

function process_new_link($payload){

	$localStatus = '';
	$mysql = new mysql_link();

	//////////////////////////////
	/////CHECK URL
	//https://stackoverflow.com/a/44029246
	$uurl = $payload['new_link'];
	$final_url='';

	if(preg_match("/^$regularExpression$/i", $uurl)) 
	{ 
	    if(preg_match("@^http|https://@i",$uurl)) 
	    {
	        $final_url = preg_replace("@(http://)+@i",'http://',$uurl);// return "*** - ***Match : ".$final_url;
	    }
	    else 
	    { 
	          $final_url = 'http://'.$uurl;// return "*** / ***Match : ".$final_url;
	    }
	}
	else 
	{
	     if (substr($uurl, 0, 1) === '/') 
	     { 
	         $final_url = $baseUrl.$uurl; // return "*** / ***Not Match :".$final_url."<br>".$baseUrl.$posted_url;
	     }
	     else 
	     { 
	         //$final_url = $baseUrl."/".$final_url; 
	     	$final_url='';// return "*** - ***Not Match :".$posted_url."<br>".$baseUrl."/".$posted_url;
	     }
	}
	//$localStatus = $final_url;
	if($final_url!='')
	{
		echo $final_url.'<br>';
	}
	//////////////////////////////

	//https://www.w3schools.com/php/filter_validate_url.asp
	//http://www.php.net/parse_url
	///check that the link is valid
	// $url = filter_var($payload['new_link'], FILTER_SANITIZE_URL);
	// if ( filter_var( $url , FILTER_VALIDATE_URL,FILTER_FLAG_HOST_REQUIRED ) === FALSE ) {
	//    	$localStatus = "$url is not a valid URL";
	    
	// } else {
	//     $localStatus = "$url is a valid URL";
	// }

	///add it to the database
	$mysql->add_link($payload['new_link'],$payload['new_desc'],'fake image link',0);
	$last_id = $mysql->conn->insert_id;
	
	//deal with the tags
	$tags = explode(",",$payload['new_tags']);
	for($i=0;$i<count($tags); $i++)
	{
		$mysql->add_tag(trim($tags[$i]),$last_id);
		//$mysql->errMsg.='---'.$tags[$i];
	}

	return $localStatus . $mysql->errMsg;
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