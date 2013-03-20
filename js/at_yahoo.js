document.getElementById('at-yahoo-connect').onclick = function (){
	
	var at_current_url = window.location.href;
	
	var url_attr = document.getElementById('at-yahoo-connect');
	var at_url = url_attr.getAttribute('at_login_url');
   
	if (at_url.indexOf('?') > -1){
		at_url += '&yOauth=initiate';
	} else{
		at_url += '?yOauth=initiate';
	}
	
	var cookieName = 'addthis_yh_redirect';
	var cookieValue = at_current_url;
	
	var the_date = new Date();
	var unix_time = the_date.getTime();
	var expiration = unix_time + ( 3600 * 1000 );
	 
	the_date.setTime( expiration );	
	
	document.cookie = cookieName +"=" + cookieValue + ";expires=" + the_date.toGMTString()+";path=/";
	
	window.location.href = at_url;
}
