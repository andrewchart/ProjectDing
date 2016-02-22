<?php 

/*
 * About Me:
 * GENERIC CONNECTION TO THE MYSQL DATABASE
 *
**/

$servername = "localhost";
$database = "project_ding";
$username = "application";
$password = "SUvexpL52KYv38VS";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

//Check for database tables and create if not exists
$tables = array(
	array(
		"tableName" => "transactions", 
		"tableColumnsSql" => "order_id VARCHAR(20), product_code VARCHAR(30), product_name VARCHAR(120), subtotal DECIMAL(11,4), quantity SMALLINT, transaction_date DATETIME"
	)/*,
	array(
		"tableName" => "actions", 
		"tableColumnsSql" => ""	
	),
	array(
		"tableName" => "users", 
		"tableColumnsSql" => ""	
	)*/
);

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