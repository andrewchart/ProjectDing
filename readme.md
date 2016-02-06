#Project DING
*Going DING like you've never dung before with the Google Analytics Realtime API*


1. Access realtime Google Analytics data on the client side
	* Access current number of active users ..
	* .. Via OAuth2 Authentication (for users with permissions to view the property)
	* User triggers a client side request to return a result on demand
	* Optionally filter by a dimension e.g. Page
	
	
2. Create a service to display current users on a given page to a public user
	* Server to poll API at intervals
	* Store result in a local cache to ensure polling stays within quota
	* Client to monitor the cache (via a continual connection?) ..
	* .. and update the DOM in realtime


3. Create a service to receive a message when a new GA Goal/Event is available
	* ...
	
4. Create front end "shadowing" to disguise infrequent polling
	* CSS3 animation ocurring on number changes more regularly than any polling - "pulse"
	* e.g. increment number from n to _n_ over a time period
	* increment step size and animations speeds should be relative to amount of activity
	
	
	
__6/2/2016:__

Created OAuth2 Interface and client side function to request the API directly and update
the DOM with the response