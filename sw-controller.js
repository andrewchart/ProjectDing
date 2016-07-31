
/* DOM Elements */
var registerBtn = document.getElementById("register");
var errorMsg = document.getElementById("errorMsg");


/* Register the serviceworker */

//Check for serviceworker and register it
if ('serviceWorker' in navigator) {

	navigator.serviceWorker.register("sw.js").then(function(reg){
		onRegistered(reg);
	}).catch(function(err){
		onError(err);
	});
	
} else {
	onError("Sorry, your browser does not support push notifications.");
}


//On Serviceworker Instantiation Success
function onRegistered(reg){
	
	console.log("Serviceworker available!");
	
	//Create the subscribe and unsubscribe functions
	window.subscribe = createSubFunc(reg);
	window.unsubscribe = createUnsubFunc(reg);
	window.checkSubstate = createIssubFunc(reg);
	
	//Check the subscription state and show the button
	checkSubstate();	
	
}





/* Subscription Controls */

//Subscribe to notifications
function createSubFunc(reg) {
	
	return function() {
	
		//TODO: Return an error message if the user has blocked push notifications for the site
	
		reg.pushManager.subscribe({
		
			userVisibleOnly: true
		
		}).then(function(sub){
		
			console.log('Endpoint: ', sub.endpoint);
			subscription(sub.endpoint, "add").then(function(){
				checkSubstate();
			});
	
				
		}).catch(function(error) {
		
			setUnsubscribed();
			onError("Sorry, we were not able to subscribe you to notifications. Please try again." + error)
			
		});
		
	}
	
}

//Unsubscribe from notifications
function createUnsubFunc(reg) {

	return function() {
	
		reg.pushManager.getSubscription().then(function(sub){

			subscription(sub.endpoint, "delete");
		
			sub.unsubscribe().then(function(event) {
			
			    console.log('Unsubscribed!');
			    setUnsubscribed();
			    

			}).catch(function(error) {
			
			    console.log('Error unsubscribing', error);
			    setSubscribed();
			    			    
			});	
			
		});
	
	}
	
}

//Check subscription state
function createIssubFunc(reg) {	

	return function() {

		try {
		
			reg.pushManager.getSubscription().then(function(sub){
				
				//If the browser has no subscription, we can set unsubbed state immediately
				if(sub === null) {
					setUnsubscribed();
				}
				
				//Otherwise, compare the browser sub to the database. Same endpoint needs
				//to exist in both otherwise we have no way of sending notifications
				else {
					subscription(sub.endpoint, "check").then(function(){
						console.log(subState);
						if(subState == "subscribed") {
							setSubscribed();
						} else {
							setUnsubscribed();
						}
					});
				}
						
			}).catch(function(err) {
				setUnsubscribed();
				onError(err + ". Please try refreshing this page.");
			});
			
		}
		
		catch(e) {
			setUnsubscribed();
		}
	
	}
	
}

//Register or de-register a subscription with the server
//Also: check the subscription state and return a resolved promise
function subscription(endpoint, action) {

	var url = "service-version-scripts/record_subscription.php?action=" + action + "&endpoint=" + endpoint;
	
	var fetchResult = fetch(url).then(function(response) {
	
		return response.text().then(function(data) {
			window.subState = data;
		});
					
	}).catch(function(err) {
		setUnsubscribed();
	});
	
	return Promise.resolve(fetchResult);		
}





/* State Controls */ 

//Unregistered button state
function setUnsubscribed() {
	clearTimeout(window.waitingTooLong);
	registerBtn.style.display = "block";
	registerBtn.className = "unregistered";
	registerBtn.innerHTML = "Subscribe to notifications";
}


//Registered button state
function setSubscribed() {
	clearTimeout(window.waitingTooLong);
	registerBtn.style.display = "block";
	registerBtn.className = "registered";
	errorMsg.innerHTML = "";
	registerBtn.innerHTML = "Unsubscribe from notifications";
}


//Waiting button state
function pleaseWait() {
	window.waitingTooLong = setTimeout(timeoutWaitingState, 8000);
	registerBtn.style.display = "block";
	registerBtn.className = "waiting";
	registerBtn.innerHTML = "Please Wait..."
}

//Timeout the waiting state
function timeoutWaitingState() {
	onError("Your request seems to be taking a while. It may be quicker for you to try again");
	checkSubstate();
}

//Error state
function onError(msg) {
	errorMsg.innerHTML = msg;
	errorMsg.style.display = "block";
}




//Bind click handler
registerBtn.onclick = function() {
	
	errorMsg.innerHTML = "";

	if(this.className === "unregistered") {
		pleaseWait();
		subscribe();
	} 
	
	else if (this.className === "registered") {
		pleaseWait();
		unsubscribe();
	}

}


