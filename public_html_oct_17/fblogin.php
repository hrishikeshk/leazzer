<!DOCTYPE html>
<html>
<head>
<title>Facebook Login JavaScript Example</title>
<meta charset="UTF-8">
<script>
	window.fbAsyncInit = function() 
  {
    FB.init({
      appId      : '347743159096213',
      cookie     : true,  
      xfbml      : true,  
      version    : 'v2.8'
    });
    FB.getLoginStatus(function(response)
    {
    	if(response.status == 'connected')
    	{
    		getFBUserData();
    	}
    });
  };
  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
  
  function onFBLogin()
  {
  	FB.login(function(response) 
  	{
	    if (response.authResponse) 
	    {
	    	getFBUserData();
	  	} 
    	else 
    	{
    		 console.log('User cancelled login or did not fully authorize.');
    	}
		},{scope:'email'});
  }
  function getFBUserData()
  {
  	FB.api('/me',{locale:'en_US',fields:'id,first_name,last_name,email'},
  	function(response){
  		console.log(response.first_name+" --- "+response.last_name+" --- "+response.email);
  	});
  }
</script>
</head>
<body>
<a href="#" onclick="onFBLogin();"><img src="images/fbimg.png" border="0" alt=""></a>

<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/5051579.js"></script>
<!-- End of HubSpot Embed Code -->

</body>
</html>
