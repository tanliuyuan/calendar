<?php
require_once('database.php');
require_once('error.php');
require_once('login.php');

// Fetch, validate, and assign input data
if(isset($_POST)) {
	if(!empty($_POST['username']))
		$username = preg_match('/^[A-Za-z0-9_-]{3,16}$/', $_POST['username']) ? $_POST['username'] : "";
	if(empty($username))
		returnError('username not valid');
	if(!empty($_POST['first_name']))
		$first_name = preg_match('/[A-Za-z \'-]{1,50}/', $_POST['first_name']) ? $_POST['first_name'] : "";
	if(empty($first_name))
		returnError('first name not valid');
	if(!empty($_POST['last_name']))
		$last_name = preg_match('/[A-Za-z \'-]{1,50}/', $_POST['last_name']) ? $_POST['last_name'] : "";
	if(empty($last_name))
		returnError('last name not valid');
	if(!empty($_POST['password']))
		$password = preg_match('/^[A-Za-z0-9_-]{6,18}$/', $_POST['password']) ? $_POST['password'] : "";
	if(empty($password))
		returnError('password not valid');
	else
		// Encrypt password
		$hashed_password = crypt($password);
}

// Check if username exists
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username=?") 
	or returnError('Query Prep Failed: '.htmlentities($mysqli->error));
$stmt->bind_param('s', $username) 
	or returnError('Parameter Binding Failed: '.htmlentities($mysqli->error));
$stmt->execute() 
	or returnError('Query Execution Failed: '.htmlentities($mysqli->error));
$stmt->bind_result($user_count) 
	or returnError('Result Binding Failed: '.htmlentities($mysqli->error));
$stmt->fetch() 
	or returnError('Result Fetching Failed: '.htmlentities($mysqli->error));
if($user_count >= 1)
	returnError('Username already exists. Please try another one!');
$stmt->close();

// Add new user into database 
$stmt = $mysqli->prepare("INSERT INTO users (username, first_name, last_name, hashed_password) values (?, ?, ?, ?)") 
	or returnError('Query Prep Failed: '.htmlentities($mysqli->error));
$stmt->bind_param('ssss', $username, $first_name, $last_name, $hashed_password) 
	or returnError('Parameter Binding Failed: '.htmlentities($mysqli->error));
$stmt->execute() 
	or returnError('Query Execution Failed: '.htmlentities($mysqli->error));
$stmt->close();

// Log in as new user
//login($username, $password);
?>