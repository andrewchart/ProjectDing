// Oauth2 Client ID
var CLIENT_ID = '458570257017-6uiai4meaigeirfpnm22g8lej2d1qu4n.apps.googleusercontent.com';

// Set authorized scope
var SCOPES = ['https://www.googleapis.com/auth/analytics.readonly'];

// Handles the authorization flow.
function authorise(event) {
	// `immediate` should be false when invoked from the button click.
	var useImmediate = event ? false : true;
	var authData = {
		client_id: CLIENT_ID,
		scope: SCOPES,
		immediate: useImmediate
	};

	gapi.auth.authorize(authData, function(response) {
		var authButton = document.getElementById('authorise');
		
		if (response.error) {
			authButton.hidden = false;
		}
	
		else {
	    	authButton.hidden = true;
	  	}
	});
}		

//Get current number of active users
function getCurrentActiveUsers(API_BASE, API_KEY, GA_VIEW_ID, pagePath) {
	
	
	
	//If a page path has been specified
	if (pagePath != "") {
		var filters = "&filters=rt:pagePath==" + pagePath;
		var msg2 = "on " + pagePath;
	} else {
		var filters = "";
		var msg2 = "on all pages";
	}
	
	//Make the request and handle response
	var requestUrl = API_BASE + "/data/realtime?ids=ga:" + GA_VIEW_ID + "&metrics=rt:activeUsers" + filters + "&key=" + API_KEY;
	gapi.client.request(requestUrl).then(
	
		function(response){
			
			$("#result span").empty();
		
			//console.log(response);
			if(response.status == "200" && response.result.totalResults > 0) {
			
				$("#number").html(response.result.rows[0]);
				$("#message1").html("Active Users");
				$("#message2").html(msg2);
				
			} else if(response.status == "200" && response.result.totalResults == 0) {
			
				$("#number").html("0");
				$("#message1").html("Active Users");
				$("#message2").html(msg2);
				
			} else {
			
				$("#message2").html("ERROR: " + response.status).addClass("error");
				
			}
			
			$('#getCurrentActiveUsers').attr("disabled",false).html("Get Current Active Users");
		},
		
		function(error) { 
		
			$("#number, #message1").empty();
			$("#message2").html("ERROR: " + error.result.error.message).addClass("error");
			$('#getCurrentActiveUsers').attr("disabled",false).html("Get Current Active Users");
			
		}
	);
	
}

//Button handlers
$('#authorise').on('click', authorise);

$('#getCurrentActiveUsers').on('click', function() { 
	
	
	$(this).attr("disabled",true).html('Please wait...');
	
	var pagePath = $('#page').val();
	
	getCurrentActiveUsers(API_BASE, API_KEY, GA_VIEW_ID, pagePath); 
});