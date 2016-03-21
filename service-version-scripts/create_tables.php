<?php

/*
 * About Me:
 * CHECK FOR EXISTENCE OF REQUIRED TABLES FOR PROJECT DING.
 * CREATE THE TABLES IF THEY DON'T EXIST.
 *
**/


// Required Table Specifications Array 
$tables = array(
	array(
		"tableName" => "pding_transactions", 
		"tableColumnsSql" => "order_id VARCHAR(20), product_code VARCHAR(30), product_name VARCHAR(120), subtotal DECIMAL(11,4), quantity SMALLINT, transaction_date DATETIME, notification_sent TINYINT(1)"
	),
	array(
		"tableName" => "pding_notifications", 
		"tableColumnsSql" => "title VARCHAR(256), body VARCHAR(256), action TEXT, image VARCHAR(256), notification_date DATETIME"	
	)/*,
	array(
		"tableName" => "pding_users", 
		"tableColumnsSql" => ""	
	),
	array(
		"tableName" => "pding_images",
		"tableColumnsSql" => ""
	)*/
);

// Loop through the tables in the spec, checking and creating where necessary
foreach($tables AS $table) {

	//Check whether table exists
	$table_name = $table["tableName"];
	$sql = "SELECT * FROM information_schema.tables WHERE table_schema = '$database' AND table_name = '$table_name' LIMIT 1;";
	$result = mysqli_fetch_array(mysqli_query($conn,$sql));

	//Create it if not
	if (count($result) == 0) {
		$sql = "CREATE TABLE $table_name (id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY, " . $table["tableColumnsSql"] . ");";
		//echo $sql;
		mysqli_query($conn,$sql);
	}
	
}

?>