<?php
// Constants
$host = 'localhost';
$username = 'wustl_inst';
$password = 'wustl_pass';
$db = 'calendar';

// Connect to database
$mysqli = new mysqli($host, $username, $password, $db);
 
if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>