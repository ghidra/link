a = new rad.ajax();
wait=false;///this hopefully keeps us from keeping looking when we reach end of page
searchtag=null;///the tag we find in the search string

function logout(){
	a.get(
		"link.php",
		"q=logout",
		function(lamda){
			document.getElementById("login").innerHTML = lamda;
		}
	);
	document.getElementById("new_link").innerHTML = "";
}

function process_login(form_name){
	var elements = document.getElementById(form_name).elements;
	var obj ={};
	obj.q = "login";
    for(var i = 0 ; i < elements.length ; i++){
        var item = elements.item(i);
        obj[item.name] = item.value;
    }
    //alert(JSON.stringify(obj));
    a.post(
		"link.php",
		obj,
		//"q=login&payload="+JSON.stringify(obj),
		function(lamda){
			document.getElementById("login").innerHTML = lamda;
			links_page(true);
		}
	);
}

function new_link_page()
{
	a.get(
		"link.php",
		"q=new_link_page",
		function(lamda){
			document.getElementById("new_link").innerHTML = lamda;
		}
	);
}

function process_new_link()
{
	var elements = document.getElementById("new_link_form").elements;
	var obj ={};
	obj.q = "process_new_link";
	var simpleValidate = true;
    for(var i = 0 ; i < elements.length ; i++){
        var item = elements.item(i);
        obj[item.name] = item.value;
        if(item.name === "new_link" && item.value === ""){
        	simpleValidate = false;
        }
    }
    //alert(JSON.stringify(obj));
    if(simpleValidate)
    {
    	//alert('there was an attempt');
	    a.post(
			"link.php",
			obj,
			function(lamda){
				//alert(lamda);
				links_page(true);
				if(lamda.length>0){
					alert(lamda);
				}
				//alert("return from process new link: "+lamda);
			}
		);
	}
	else
	{
		alert("updated Link field is empty: ");
	}
}

function links_page(refresh,begin,limit){
	var begin_id = (begin)?begin:0;
	var limit_id = (limit)?limit:10;
	var obj ={'begin':begin_id,'limit':limit_id,'tag':searchtag};
	a.get(
		"link.php",
		"q=links_page&payload="+JSON.stringify(obj),
		function(lamda){
			//console.log(lamda);
			data = JSON.parse(lamda);
			var el = document.getElementById("links");
			if(refresh)
			{
				el.innerHTML=data.html;//lamda;
			}
			else
			{
				el.innerHTML+=data.html;
			}
			//update the paging information
			document.getElementById("total_links_count").innerHTML = data.paging.total_count;
			document.getElementById("start_offset").innerHTML = data.paging.begin;
			document.getElementById("end_offset").innerHTML = data.paging.end; 
			//new_link_page();
			tags_page();
			wait=false;
		}
	);
}
function tags_page(){
	a.get(
		"link.php",
		"q=tags_page",
		function(lamda){
			document.getElementById("tags").innerHTML = lamda;
			new_link_page();
		}
	);
}

////called from the page
function load_tagid_page(tagid){
	//console.log(tag);
	window.location.search = 'tagid='+tagid;
}

window.onload=function(){
	//is there something in the search string
	
	///lets set up the login part
	a.get(
		"link.php",
		"q=login",
		function(lamda){
			document.getElementById("login").innerHTML = lamda;

			params = new URLSearchParams(document.location.search.substring(1));
			searchtag = parseInt(params.get("tagid"), 10);
			if(searchtag){
				console.log("we need to load tagid page: "+searchtag);
			}
			links_page(true);
		}
	);
	
	///now lets load the links we have so far
}
///check if we scroll to the bottom of the page:
window.onscroll = function(ev) {
    if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight) {
        if(!wait){
			wait=true;
			var max_limit = Math.min(10,document.getElementById("total_links_count").innerHTML-document.getElementById("end_offset").innerHTML);
			if(max_limit>0){
				//console.log(document.getElementById("total_links_count").innerHTML);
				//console.log(document.getElementById("end_offset").innerHTML	);
				//console.log(max_limit);
				//console.log("-----");
		        links_page(false,document.getElementById("end_offset").innerHTML,max_limit);
    		}
    	}
    }
};