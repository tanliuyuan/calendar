<?php
ini_set("session.cookie_httponly", 1);
session_start();

require_once('database.php');
require_once('functions.php');

// Fetch, validate, and assign input data
if(isset($_POST) && isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
	if($_SESSION['token'] === $_POST['token']) {
		if(!empty($_SESSION['username']))
			$creator = $_SESSION['username'];
		else
			returnError('Error while adding event: No user is logged in!');
		if(!empty($_POST['title']))
			$title = preg_match('/^[A-Za-z.\ \'\-]{1,50}$/', $_POST['title']) ? $_POST['title'] : "";
		if(empty($title))
			returnError('Error while adding event: Title not valid');
		if(!empty($_POST['start_time']))
			$start_time = preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}) ([AP]M)$/', $_POST['start_time']) ? $_POST['start_time'] : "";
		if(empty($start_time))
			returnError('Error while adding event: Start time not valid');
		if(!empty($_POST['end_time']))
			$end_time = preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/', $_POST['end_time']) ? $_POST['end_time'] : "";
		if(empty($end_time))
			returnError('Error while adding event: End time not valid');

		// Add new event into database on behalf of the currently logged in user
		$stmt = $mysqli->prepare("INSERT INTO events (creator, title, start_time, end_time) values (?, ?, ?, ?)") 
			or returnError('Query Prep Failed: '.htmlspecialchars($mysqli->error));
		$stmt->bind_param('ssss', $creator, $title, $start_time, $end_time) 
			or returnError('Parameter Binding Failed: '.htmlspecialchars($mysqli->error));
		$stmt->execute() 
			or returnError('Query Execution Failed: '.htmlspecialchars($mysqli->error));
		$stmt->close();
		
		// Return success message
		$JSONArray = array(
			'success' => true
		);
		returnSuccess($JSONArray);
	} else {
		returnError('Fatal: CSRF detected!');
	}
}
?>