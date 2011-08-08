function social_pop_up(social_url, social_window_id){
	window.open(social_url, social_window_id, 'width=500, height=400, scrollbars=yes');
}

jQuery(document).ready(function(){
	jQuery('#fb-share-trigger').click(function(evt){
		evt.preventDefault();
		social_pop_up('http://www.facebook.com/sharer.php?u=http://beta.drdoormat.com/index.html', 'fbsharewindow');
	});

	jQuery('#tw-share-trigger').click(function(evt){
		evt.preventDefault();
		social_pop_up('http://twitter.com/home?status=Check this out http://beta.drdoormat.com/index.html', 'twsharewindow');
	});

	jQuery('#newsletter-trigger').click(function(evt){
		evt.preventDefault();
		social_pop_up('http://visitor.r20.constantcontact.com/d.jsp?llr=w6uwdydab&p=oi&m=1103542047670', 'newsletterwindow');
	});
});
