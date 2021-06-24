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
			<textarea name="new_desc" type="text" id="new_desc" placeholder="description" class="new_desc_input"></textarea>
			<input name="new_tags" type="text" id="new_tags" placeholder="tag1, tag2" class="new_tags_input">
			<input type="button" name="Submit" value="Submit" onclick="process_new_link()">
		</form>
	</div>';

	return $s;
}

////
function link_html($link_data,$tag_data=array())
{
	/*
		$link_data['id']
		$link_data['user']
		$link_data['url']
		$link_data['description']
		$link_data['imagelink']
		$link_data['private']
		$link_data['posttime']

		$tag_data[i]->name
		$tag_data[i]->id
	*/
	//make the tag part first to insert later
	$stags='';
	for($i=0;$i<count($tag_data); $i++)
	{
		$stags.='<div class="link_tag" onclick="javascrpt:load_tagid_page(\''.$tag_data[$i]->id.'\')">'.$tag_data[$i]->name.'</div>';//$tag_data[$i]
	}
	$s='<div class="container_link">
		<a class="link_ahref" href="'.$link_data['url'].'" target="_blank"><div class="link_ahref_bg">'.$link_data['url'].'</div></a>
		<div id="link_description">'.$link_data['description'].'</div>
		<div class="container_link_tags">'.$stags.'</div>
		<div id="link_posttime">'.$link_data['posttime'].'</div>
	</div>';

	return $s;
}
function tag_html($tag_data)
{
	/*
		$tag_data['tag_id']
		$tag_data['tag']
		$tag_data['user']
		$tag_data['posttime']
	*/
	$s='<div class="container_tag" onclick="javascrpt:load_tagid_page(\''.$tag_data["tag_id"].'\')">'.$tag_data['tag'].'</div>';

	return $s;
}
function paging_info($total_count,$begin,$end)
{
	$d= new stdClass();//'<div class="paging_container">';
	$d->total_count = $total_count;
	$d->begin = $begin;
	$d->end = $end;
	return $d;
}
function reached_end_html()
{
	$s='<div class="reached_end">How Awesome, you have scrolled to the end</div>';

	return $s;
}
function get_focused_links($focus=true,$payload)
{
	///first stop on making the link page. This is where we determine how to look into the database
	$mysql = new mysql_link();
	$fetched_link_data;//includes links array and total links
	$fetched_links;
	$fetched_tags;

	if($focus)
	{
		$fetched_link_data = $mysql->get_all_personal_links($payload->begin,$payload->limit,$payload->tag);
	}
	else
	{
		$fetched_link_data = $mysql->get_all_public_links($payload->begin,$payload->limit,$payload->tag);
	}
	$fetched_links = $fetched_link_data->links;
	$fetched_tags = $fetched_link_data->tags;

	$data = new stdClass();
	$data->html = $mysql->errMsg;

	if(count($fetched_links)<1)
	{
		$data->html.="Unbeleivable, there are no links here.";
	}
	else
	{
		for($i=0;$i<count($fetched_links); $i++)
		{
			if(count($fetched_tags)>=i)
			{
				$data->html.=link_html($fetched_links[$i],$fetched_tags[$i]);
			}
			else
			{
				$data->html.=link_html($fetched_links[$i]);
			}
		}
	}
	
	///now make the page footer
	$data->paging = paging_info($fetched_link_data->total_count,$fetched_link_data->start_offset,$fetched_link_data->end_offset);
	//make a happy greeting for reaching the end
	if($fetched_link_data->end_offset>=$fetched_link_data->total_count)
	{
		$data->html.=reached_end_html();
	}

	echo json_encode($data);

}

function get_tags()
{
	$mysql = new mysql_link();
	$fetched_tags = $mysql->get_tags();
	$s=$mysql->errMsg;

	if(count($fetched_tags)<1)
	{
		$s.="Unbeleivable, there are no tags here.";
	}
	else
	{
		for($i=0;$i<count($fetched_tags); $i++)
		{
			$s.=tag_html($fetched_tags[$i]);
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
///test method to see if we are getting a tag propper
/*
function process_new_link($payload){
	$s='';
	$mysql = new mysql_link();
	$tags = explode(",",$payload['new_tags']);
	for($i=0;$i<count($tags); $i++)
	{
		$s.=$mysql->get_tag_id(trim($tags[$i])).'---';
		
	}
	return $s;
}*/
function process_new_link($payload){

	$localStatus = '';
	$mysql = new mysql_link();

	//////////////////////////////
	/////CHECK URL
	//https://stackoverflow.com/a/44029246
	$uurl = $payload['new_link'];
	$final_url='';

	$baseUrl = '/';
	$regularExpression  = "((https?|ftp)\:\/\/)?"; // SCHEME Check
	$regularExpression .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass Check
	$regularExpression .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP Check
	$regularExpression .= "(\:[0-9]{2,5})?"; // Port Check
	$regularExpression .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path Check
	$regularExpression .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query String Check
	$regularExpression .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor Check

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
	     	$localStatus = $final_url;///copy the fineal_url string across so I can see it
	     	$final_url='';// return "*** - ***Not Match :".$posted_url."<br>".$baseUrl."/".$posted_url;
	     }
	}
	//return parse_url($url, PHP_URL_SCHEME) === null ?$scheme . $url : $url;
	///add it to the database
	if($final_url!='')
	{
		$mysql->add_link($final_url,$payload['new_desc'],'fake image link');
		$last_id = $mysql->conn->insert_id;
		
		//deal with the tags
		$tags = explode(",",$payload['new_tags']);
		for($i=0;$i<count($tags); $i++)
		{
			$mysql->add_tag(trim($tags[$i]),$last_id);
			//$mysql->errMsg.='---'.$tags[$i];
		}
	}
	else
	{
		$localStatus = 'URL: '.$localStatus.' is invalid. Was NOT added. ';
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
		//test here to show personal, or all
		if(isset($_SESSION['logged_in']))
		{
			echo get_focused_links(true,json_decode($_GET['payload']));
		}
		else
		{
			//json_decode($_GET['payload']
			echo get_focused_links(false,json_decode($_GET['payload']));
		}
	}
	if($_GET['q']=='tags_page')
	{
		echo get_tags();
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