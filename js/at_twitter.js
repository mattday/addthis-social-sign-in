document.getElementById('at-twitter-connect').onclick = function (){
	var url = window.location.href.split('#')[0];
	if (url.indexOf('?') > -1){
		url += '&tOauth=initiate';
	}else {
		url += '?tOauth=initiate';
	}
	window.location.href = url;
}