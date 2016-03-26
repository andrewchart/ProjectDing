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
mysqli_set_charset($conn, "utf8");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>