<?php
function returnError($string) {
	header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    die(json_encode(array('error' => $string)));
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
	$stmt = $mysqli->prepare("SELECT COUNT(*), username, first_name, last_name, hashed_password, is_admin FROM users WHERE username=?")
		or returnError('Query Prep Failed: '.htmlentities($mysqli->error));
	$stmt->bind_param('s', $username)
		or returnError('Parameter Binding Failed: '.htmlentities($mysqli->error));
	$stmt->execute()
		or returnError('Query Execution Failed: '.htmlentities($mysqli->error));
	$stmt->bind_result($user_count, $username, $user_first_name, $user_last_name, $hashed_password, $is_admin)
		or returnError('Result Binding Failed: '.htmlentities($mysqli->error));
	$stmt->fetch()
		or returnError('Result Fetching Failed: '.htmlentities($mysqli->error));
	$stmt->close();
	if( $user_count === 1 && crypt($password, $hashed_password) === $hashed_password) {
		// Set session variables
		$_SESSION['logged_in'] = true;
		$_SESSION['username'] = $username;
		$_SESSION['user_first_name'] = $user_first_name;
		$_SESSION['user_last_name'] = $user_last_name;
		$_SESSION['admin_logged_in'] = $is_admin;
		$_SESSION['token'] = substr(md5(rand()), 0, 10);
		// Set up an array to be returned as JSON
		$JSONArray = array(
			'success' => true, 
			'token' => htmlentities($_SESSION['token']), 
			'user_first_name' => htmlentities($user_first_name), 
			'user_last_name' => htmlentities($user_last_name),
			'is_admin' => $is_admin
		);
		returnSuccess($JSONArray);
	}else{
		returnError('Log in failed. Please try again!');
	}
}
?>