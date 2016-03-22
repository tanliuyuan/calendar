<?php
ini_set("session.cookie_httponly", 1);
session_start();

require_once('database.php');
require_once('error.php');

// Fetch, validate, and assign input data
if(!empty($_POST['login_username']) && !empty($_POST['login_password'])) {
	$username = preg_match('/^[A-Za-z0-9_-]{3,16}$/', $_POST['username']) ? $_POST['username'] : "";
	if(empty($username))
		returnError('username not valid');
	$password = preg_match('/^[A-Za-z0-9_-]{6,18}$/', $_POST['password']) ? $_POST['password'] : "";
	if(empty($password))
		returnError('password not valid');
	else
		login($username, $password);
}

function returnSuccess($JSONArray) {
	header('HTTP/1.1 200 OK');
    header('Content-Type: application/json');
    exit(json_encode($JSONArray));
}

function login($username, $password) {
	// Connect to database
	$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'calendar');
	if($mysqli->connect_errno)
		returnError('Connection Failed: ' . htmlentities($mysqli->connect_error));
	$stmt = $mysqli->prepare("SELECT COUNT(*), id, first_name, last_name, hashed_password FROM users WHERE username=?")
		or returnError('Query Prep Failed: '.htmlentities($mysqli->error));
	$stmt->bind_param('s', $username)
		or returnError('Parameter Binding Failed: '.htmlentities($mysqli->error));
	$stmt->execute()
		or returnError('Query Execution Failed: '.htmlentities($mysqli->error));
	$stmt->bind_result($user_count, $user_id, $user_first_name, $user_last_name, $hashed_password)
		or returnError('Result Binding Failed: '.htmlentities($mysqli->error));
	$stmt->fetch()
		or returnError('Result Fetching Failed: '.htmlentities($mysqli->error));
	$stmt->close();
	if( $user_count === 1 && crypt($password, $hashed_password) === $hashed_password) {
		// Set session variables
		$_SESSION['user_id'] = $user_id;
		$_SESSION['user_first_name'] = $user_first_name;
		$_SESSION['user_last_name'] = $user_last_name;
		$_SESSION['token'] = substr(md5(rand()), 0, 10);
		// Set up an array to be returned as JSON
		$JSONArray = array(
			'success' => true, 
			'token' => htmlentities($_SESSION['token']), 
			'user_first_name' => htmlentities($_SESSION['user_first_name']), 
			'user_last_name' => htmlentities($_SESSION['user_last_name'])
		);
		returnSuccess($JSONArray);
	}else{
		returnError('Log in failed. Please try again!');
	}
}
?>