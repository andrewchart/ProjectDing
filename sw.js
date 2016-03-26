

/* Install Event */
self.addEventListener('install', function(event) {
	self.skipWaiting(); //debug
	console.log("New SW installed 1723");
});

/* Activate Event */
self.addEventListener('activate', function(event) {
	console.log("New SW activated");
});

/* Push Received */
self.addEventListener('push', function(event) {

	//console.log('Push message received!', event);
	
	//Fetch 
	var url = "latest-notification.json";
	fetch(url).then(  
	    function(response) {  
			if (response.status !== 200) {  
				console.log('HTTP Error. Status Code: ' + response.status);  
				return;  
			}
	      
	     	response.json().then(function(json) {  
	      		
	      		//Display the message
				self.registration.showNotification(json.title, {
					actions: [ //Chrome 48+, only 2 actions allowed so far? Log for event.action in notificationclick listener
						{action: "1", title: "View order details"}
					],
					body: json.message,
					icon: json.image,
					tag: 'oneNotificationAtaTime' + Math.random(),
					data: {
						defaultAction: json.actions.defaultAction,
						action1: json.actions.action1
					}
				});
				
	    	}); 
	    }).catch(function(err) {  
			console.log('Fetch Error', err);  
		});
});



/* Listen for a click */
self.addEventListener("notificationclick", function(event){
	
	//Make sure the notification closes
	event.notification.close();
	
	//Get the right url based upon user action or default
	switch(event.action) {
		case "1":
			var url = event.notification.data.action1;
			break;
		case "2":
			var url = event.notification.data.action2; 
			break;
		case "3":
			var url = event.notification.data.action3;
			break;
		case "4": 
			var url = event.notification.data.action4;
			break;
		default:
			var url = event.notification.data.defaultAction;
	}
	
	//Handle the url
	event.waitUntil(clients.matchAll({
		type: "window"
	}).then(function(clientList) {
		
		//If the url is already open, focus it
		for (var i = 0; i < clientList.length; i++) {
			var client = clientList[i];
			if (client.url == url && 'focus' in client) {
		  		return client.focus(); //Need to do something to refresh the data on the url, I guess?
			}
		}
		
		//Open new window
		if (clients.openWindow) {
			return clients.openWindow(url);	
		}
			
	}));
	
});
