function social_pop_up(social_url, social_window_id){
	window.open(social_url, social_window_id, 'width=400, height=400, scrollbars=yes');
}

$(document).ready(function(){
	$('#fb-share-trigger').click(function(evt){
		evt.preventDefault();
		social_pop_up('http://www.facebook.com/sharer.php?u=http://beta.drdoormat.com/index.html', 'fbsharewindow');
	});

	$('#tw-share-trigger').click(function(evt){
		evt.preventDefault();
		social_pop_up('http://twitter.com/home?status=Check this out http://beta.drdoormat.com/index.html', 'twsharewindow');
	});

	$('#newsletter-trigger').click(function(evt){
		evt.preventDefault();
		social_pop_up('http://visitor.r20.constantcontact.com/d.jsp?llr=w6uwdydab&p=oi&m=1103542047670', 'newsletterwindow');
	});
});
