a = new rad.ajax();

function logout(){
	a.get(
		"link.php",
		"q=logout",
		function(lamda){
			document.getElementById("login").innerHTML = lamda;
		}
	);
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
			///attach method to form
			var form = document.getElementById('login_form');
			if (form.attachEvent) {
			    form.attachEvent("submit", process_login);
			} else {
			    form.addEventListener("submit", process_login);
			}
		}
	);
	///now lets load the links we have so far
}