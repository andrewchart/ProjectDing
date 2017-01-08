<?php

/*
 * About Me:
 * GET TODAY'S ECOMMERCE TRANSACTION LINES (Products) FROM GA AND WRITE THEM TO A DATABASE TABLE
 *
**/


/* Create the Analytics Client Object and Authenticate it */

//Include Google API Library
require_once 'google-api-php-client/src/Google/autoload.php';

// Create and configure a new client object.
$client = new Google_Client();
$client->setApplicationName("ProjectDing");
$analytics = new Google_Service_Analytics($client);

//Authenticate
$auth = new Google_Auth_AssertionCredentials(
	$credentials->service_account->client_email,
	array(Google_Service_Analytics::ANALYTICS_READONLY),
	$credentials->service_account->private_key
);

$client->setAssertionCredentials($auth);
if($client->getAuth()->isAccessTokenExpired()) {
	$client->getAuth()->refreshTokenWithAssertion($auth);
}




/* Query the API for latest transaction data */
define("GA_VIEW_ID", "ga:" . $credentials->google_analytics->ga_view_id);
$from = "today";
$to = "today";
$metrics = "ga:itemRevenue, ga:itemQuantity";
$parameters = array(
	"dimensions" => "ga:transactionId, ga:productSku, ga:productName"
);

$results = $analytics->data_ga->get(GA_VIEW_ID, $from, $to, $metrics, $parameters);

/* If there are no results.. */
if(count($results->rows) == 0) {
	echo 'No orders yet today!';
}

/* If there are results: write them to the transaction data table */
else {

	$values = array();

	foreach($results->rows AS $result) {

		//Check that the transaction doesn't already exist
		$sql = "SELECT COUNT(transaction_id) FROM pding_transactions WHERE transaction_id = '$result[0]'";
		$count = mysqli_fetch_array(mysqli_query($conn, $sql));
		if($count[0] > 0) continue;

		//Ready the data for insertion into pding_transactions
		$line = array(
			"id" => "''",
			"transaction_id" => "'" . $result[0] . "'",
			"product_code" =>  "'" . $result[1] . "'",
			"product_name" =>  "'" . $result[2] . "'",
			"subtotal" =>  $result[3],
			"quantity" =>  $result[4],
			"transaction_date" => "NOW()",
			"notification_queued" => 0
		);

		array_push($values, $line);
	}

	//Loop through the transactions and add them to the table
	foreach($values AS $row) {

		$data = implode($row, ", ");
		$sql = "INSERT INTO pding_transactions VALUES (" . $data . ")";
		mysqli_query($conn,$sql);

	}

	echo "Transactions Processed.";

}

/* Drop any lines from the table that are more than 3 days old */
$sql = "DELETE FROM pding_transactions WHERE transaction_date < DATE_SUB(NOW(), INTERVAL 3 DAY)";
mysqli_query($conn,$sql);P


?>
