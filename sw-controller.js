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
	
	//Check the subscription state and show the button
	checkSubstate(reg);
	
	//Create the subscribe and unsubscribe functions
	window.subscribe = createSubFunc(reg);
	window.unsubscribe = createUnsubFunc(reg);
	
}





/* Subscription Controls */

//Subscribe to notifications
function createSubFunc(reg) {
	
	return function() {
	
		reg.pushManager.subscribe({
		
			userVisibleOnly: true
		
		}).then(function(pushSubscription){
		
			console.log('Endpoint: ', pushSubscription.endpoint);
			setSubscribed();
			clearTimeout(waitingTooLong);
				
		}).catch(function(error) {
		
		    console.log('Error subscribing', error);
		    setUnsubscribed();
		    clearTimeout(waitingTooLong);
			
		});
		
	}
	
}

//Unsubscribe from notifications
function createUnsubFunc(reg) {

	return function() {
	
		reg.pushManager.getSubscription().then(function(sub){
		
			sub.unsubscribe().then(function(event) {
			
			    console.log('Unsubscribed!', event);
			    setUnsubscribed();
			    clearTimeout(waitingTooLong);

			}).catch(function(error) {
			
			    console.log('Error unsubscribing', error);
			    setSubscribed();
			    clearTimeout(waitingTooLong);
			    			    
			});	
			
			
		
		});
	
		
		
	}
	
}


//Check subscription state
function checkSubstate(reg){

	try {
	
		reg.pushManager.getSubscription().then(function(sub){
			
			if(sub === null) {
				setUnsubscribed();
			} else {
				setSubscribed();
			}
					
		}).catch(function(err) {
			onError(err + ". Please try refreshing this page.");
		});
		
	}
	
	catch(e) {
		setUnsubscribed();
	}
	
}

//Unregistered button state
function setUnsubscribed() {
	registerBtn.style.display = "block";
	registerBtn.className = "unregistered";
	registerBtn.innerHTML = "Subscribe to notifications";
}


//Registered button state
function setSubscribed() {
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


