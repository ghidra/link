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

function process_login(){
	var elements = document.getElementById("login_form").elements;
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
			new_link_page();
		}
	);
	//new_link_page();
}

function new_link_page(){
	a.get(
		"link.php",
		"q=new_link_page",
		function(lamda){
			document.getElementById("new_link").innerHTML = lamda;
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
			new_link_page();
		}
	);
	
	///now lets load the links we have so far
}