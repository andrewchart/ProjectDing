<?php

/*
 * About Me:
 * WRITE USERS' SUBSCRIPTION ENDPOINTS TO THE PDING_USERS TABLE
 * HANDLE CLEANSING OF THE TABLE (UNSUBSCRIBES AND DEAD SUBSCRIPTIONS)
 *
**/



//Validate the http request
function valid_request() {
	if(!isset($_GET['action'])) return false;
	if($_GET['action'] !== "add" && $_GET['action'] !== "delete" && $_GET['action'] !== "check") return false;
	if(!isset($_GET['endpoint'])) return false;
	return true;
}

if(!valid_request()) die('Invalid request');


//Database Connection
include_once('database_connection.php');


//Execute the action
$endpoint = pg_escape_string(urldecode($_GET['endpoint']));

if($_GET['action'] === "add") {

	$sql = "INSERT INTO pding_users VALUES ('', '$endpoint')";
	$success = "added";
	mysqli_query($conn, $sql);
	if(mysqli_affected_rows($conn) > 0) {
		echo $success;
	} else {
		error();
	}

} 

elseif($_GET['action'] === "delete") {

	$sql = "DELETE FROM pding_users WHERE endpoint='$endpoint'";
	$success = "deleted";
	mysqli_query($conn, $sql);
	if(mysqli_affected_rows($conn) > 0) {
		echo $success;
	} else {
		error();
	}

}

else {
	
	$sql = "SELECT id FROM pding_users WHERE endpoint='$endpoint' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($conn,$sql)) == 1) {
		echo "subscribed";
	} else {
		echo "not subscribed";
	}
	
}


function error() {
	echo "Sorry, an error occurred: ";
	echo mysqli_error($conn);
}
	

?>