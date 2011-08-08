function facebook_share(){
	window.open('http://facebook.com', 'fbsharewindow', 'width=300, height=300, scrollbars=yes');
}

$(document).ready(function(){
	$('#fb-share-trigger').click(function(evt){
		evt.preventDefault();
		facebook_share();
	});
});
