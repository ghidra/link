a = new rad.ajax();

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

function links_page(refresh){
	var obj ={};//empty for now
	a.get(
		"link.php",
		"q=links_page&payload="+JSON.stringify(obj),
		function(lamda){
			var el = document.getElementById("links");
			if(refresh)
			{
				el.innerHTML=lamda;
			}
			else
			{
				el.innerHTML+=lamda;
			}
			//new_link_page();
			tags_page();
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

window.onload=function(){
	///lets set up the login part
	a.get(
		"link.php",
		"q=login",
		function(lamda){
			document.getElementById("login").innerHTML = lamda;
			links_page(true);
		}
	);
	
	///now lets load the links we have so far
}