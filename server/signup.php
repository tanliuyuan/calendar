<?php
require_once('database.php');

// Fetch, validate, and assign input data
if(isset($_POST)) {
	if(!empty($_POST['username'])
		$username = preg_match('/^[A-Za-z0-9_]{3,16}$/', $_POST['username']) ? $_POST['username'] : "";
	if(empty($username))
		die('Fatal: username not set!');
	if(!empty($_POST['first_name'])
		$firstName = preg_match('/[A-Za-z ]{1,50}/', $_POST['first_name']) ? $_POST['first_name'] : "";
	if(!empty($_POST['last_name'])
		$lastName = preg_match('/[A-Za-z ]{1,50}/', $_POST['last_name']) ? $_POST['last_name'] : "";
	if(!empty($_POST['password'])
		$password = preg_match('/^[A-Za-z0-9_-]{6,18}$/', $_POST['password']) ? $_POST['password'] : "";
	if(empty($password))
		die('Fatal: password not set!');
	else
		// Encrypt password
		$hashed_password = crypt($password);

}

// Add new user into database 
$stmt = $mysqli->prepare("INSERT INTO users (username, first_name, last_name, hashed_password, is_admin) values (?, ?, ?, ?, 1)");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
 
$stmt->bind_param('ssss', $username, $first_name, $last_name, $hashed_password);
 
$stmt->execute();
 
$stmt->close();
?>