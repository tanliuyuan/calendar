<?php
require_once('database.php');
require_once('error.php');

function returnSuccess($JSONArray) {
	header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    exit(json_encode($JSONArray));
}

function login($username, $password) {
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
		session_start();
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