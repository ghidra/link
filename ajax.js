rad={};

rad.ajax=function(){
	return this;
};

rad.ajax.prototype.request=function(script,data,method){
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200)
	    	if(method!=null)
	    		method(this.responseText);
	};
	xhttp.open("GET", script+"?"+data, true);
	xhttp.send();

};