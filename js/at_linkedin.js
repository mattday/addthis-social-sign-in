document.getElementById('at-linkedin-connect').onclick = function (){	
	var url = window.location.href.split('#')[0];
	if (url.indexOf('?') > -1){
		url += '&lType=initiate';
	}else {
		url += '?lType=initiate';
	}
	window.location.href = url;
}
