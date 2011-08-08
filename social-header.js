function facebook_share(){
	window.open('http://facebook.com', 'fbsharewindow', 'width=300, height=300, scrollbars=yes');
}

$('#fb-share-trigger').click(function(evt){
	facebook_share();
});
