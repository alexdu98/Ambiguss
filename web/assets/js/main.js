$(document).ready(function() {

	$.ajaxSetup({ cache: true });

	$.getScript('//connect.facebook.net/fr_FR/sdk.js', function(){
		FB.init({
			appId: '1793610560900722',
			version: 'v2.7'
		});

		FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
				console.log('Logged in.');
			}
			else {
				//FB.login();
			}
		});
		$('#loginbutton,#feedbutton').removeAttr('disabled');
		FB.getLoginStatus(updateStatusCallback);

		FB.getLoginStatus(function(){
			//alert('Identifier... Rediriger');
		});
	});

	$('#login-facebook').click(function(){
		FB.login();
	});

	function updateStatusCallback(){
		return null;
	}

});