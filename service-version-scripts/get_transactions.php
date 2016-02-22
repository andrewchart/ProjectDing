<?php

/*
 * About Me:
 * GET TODAY'S ECOMMERCE TRANSACTION LINES (Products) FROM GA AND WRITE THEM TO A DATABASE TABLE 
 *
**/


/* Create the Analytics Client Object and Authenticate it */

//Include Google API Library
require_once '../google-api-php-client/src/Google/autoload.php';

//Google Analytics API version 3
define("API_BASE", "https://www.googleapis.com/analytics/v3");

//Service Account Credentials
$service_account = json_decode(file_get_contents('../private/service_account_credentials.json'));


// Create and configure a new client object.
$client = new Google_Client();
$client->setApplicationName("ProjectDing");
$analytics = new Google_Service_Analytics($client);

//Authenticate
$cred = new Google_Auth_AssertionCredentials(
	$service_account->client_email,
	array(Google_Service_Analytics::ANALYTICS_READONLY),
	$service_account->private_key
);

$client->setAssertionCredentials($cred);
if($client->getAuth()->isAccessTokenExpired()) {
	$client->getAuth()->refreshTokenWithAssertion($cred);
}




/* Query the API for latest transaction data */
define("GA_VIEW_ID", "ga:" . file_get_contents('../private/ga_view_id.txt'));
$from = "today";
$to = "today";
$metrics = "ga:itemRevenue, ga:itemQuantity";
$parameters = array(
	"dimensions" => "ga:transactionId, ga:productSku, ga:productName"
);
			
$results = $analytics->data_ga->get(GA_VIEW_ID, $from, $to, $metrics, $parameters);

echo "<pre>"; print_r($results); echo "</pre>";

			

/* Write the results into the transaction data table */

//DB connection and check tables exist
require_once('database_connection.php');

$values = array();

// Check if the order is already
// in the table and add it to $values if not
foreach($results->rows AS $result) {
	
	$sql = "SELECT COUNT(order_id) FROM transactions WHERE order_id = '$result[0]'";
	$count = mysqli_fetch_array(mysqli_query($conn, $sql));
	if($count[0] > 0) continue; 
	
	$line = array(
		"id" => "''",
		"order_id" => "'" . $result[0] . "'",
		"product_code" =>  "'" . $result[1] . "'",
		"product_name" =>  "'" . $result[2] . "'",
		"subtotal" =>  $result[3],
		"quantity" =>  $result[4],
		"transaction_date" => "NOW()"
	);
	
	array_push($values, $line);
}

//Loop through the transactions and add them to the table
foreach($values AS $row) {
		
	$data = implode($row, ", ");
	$sql = "INSERT INTO transactions VALUES (" . $data . ")";
	//echo $sql;
	mysqli_query($conn,$sql);
	
}


?>