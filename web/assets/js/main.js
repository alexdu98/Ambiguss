$(document).ready(function() {

	$.ajaxSetup({ cache: true });

	$('#login-facebook').click(function(){
		$.getScript('//connect.facebook.net/fr_FR/sdk.js', function(){
			FB.init({
				appId: '1793610560900722',
				version: 'v2.8'
			});

			FB.getLoginStatus(function(response) {
				if (response.status === 'connected') {
					FB.login(function(response){
						if(response.authResponse){
							FB.api('/me', {fields: 'id, email, gender'}, function(response) {
								saveFacebookUser(response);
							});
						}
					}, {scope: 'public_profile, email'});
				}
				else {
					FB.login(function(response){
						if(response.authResponse){
							FB.api('/me', {fields: 'id, email, gender'}, function(response) {
								saveFacebookUser(response);
							});
						}
					}, {scope: 'public_profile, email'});
				}
			});
		});
	});

	function saveFacebookUser(facebookUser){
		var url = Routing.generate('user_inscription_provider',
			{provider: 'facebook'}
		);
		$.post(url,
			{data: JSON.stringify(facebookUser)},
			function(data){
				return true;
			}
		);
	}

});