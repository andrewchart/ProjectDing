<?php 

/*
 * About Me:
 * GET AN ARRAY OF USERS WHO ARE SUBSCRIBED TO NOTIFICATIONS 
 * AND cURL THE GCM API IN ORDER TO SEND THEM A PUSH NOTIFICATION
 *
**/


//GCM Credentials
define("API_KEY", "AIzaSyCLAwUH94XkrLdp_jFp7pPElA5qQJiHI5A");
define("ENDPOINT", "https://android.googleapis.com/gcm/send"); //Apparently can't curl this with a "/"


//Query all the endpoints we think are subscribed
$sql = "SELECT endpoint FROM pding_users WHERE endpoint LIKE '" . ENDPOINT . "%'";
$result = mysqli_query($conn, $sql);


//Isolate the registration IDs and add to an array
$users = array();
while($row = mysqli_fetch_row($result)) {
	$reg_id = str_ireplace(ENDPOINT . "/", "", $row[0]); //Need to get rid of the slash as well to get the registration ID string...
	array_push($users, $reg_id);
	
}

//Stringify the reg IDs
$reg_ids = implode('","',$users);	


//Build the request payload 
$data = '{"registration_ids":["' . $reg_ids . '"]}';
//echo $data;


//Execute cURL
if (!function_exists('curl_init')){
    die('Sorry cURL is not installed!');
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, ENDPOINT);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: key=" . API_KEY,
		"Content-Type: application/json"
	));
	
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$output = curl_exec($ch);
curl_close($ch);



//Process the return json
echo "<br><Br>GCM Response: <pre>";
$response = json_decode($output);
print_r( $response ); 
echo "</pre>";


//Seem to have to relate registration IDs to responses by array index...
//i.e. $reg_ids[3] relates to $response->results[3]
//Record the last response to the DB in this way
foreach($users as $index => $value) {
	
	$key = key($response->results[$index]);
	$responseMsg = "$key: " . $response->results[$index]->$key;
	
	$sql = "UPDATE pding_users SET last_response = '$responseMsg' WHERE endpoint = '" . ENDPOINT . "/$value'";
	//echo $sql . "<br><br>";
	mysqli_query($conn,$sql);
}


//Remove any dead subscriptions from the database
$sql = "DELETE FROM pding_users WHERE last_response = 'error: InvalidRegistration'";
mysqli_query($conn,$sql);

?>