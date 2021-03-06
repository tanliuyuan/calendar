<?php
ini_set("session.cookie_httponly", 1);
session_start();

require_once('database.php');
require_once('functions.php');

date_default_timezone_set('America/Chicago');

// Fetch, validate, and assign input data
if(isset($_POST) && isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
	if($_SESSION['token'] === $_POST['token']) {
		if(!empty($_SESSION['username']))
			$creator = $_SESSION['username'];
		else
			returnError('Error while adding event: No user is logged in!');
		if(!empty($_POST['title']))
			$title = preg_match('/^[A-Za-z0-9.\ \'\-]{1,50}$/', $_POST['title']) ? $_POST['title'] : "";
		if(empty($title))
			returnError('Error while adding event: Title not valid');
		if(!empty($_POST['start_time']))
			$start_time = preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/', $_POST['start_time']) ? $_POST['start_time'] : "";
		if(empty($start_time))
			returnError('Error while adding event: Start time not valid');
		if(!empty($_POST['end_time']))
			$end_time = preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/', $_POST['end_time']) ? $_POST['end_time'] : "";
		if(empty($end_time))
			returnError('Error while adding event: End time not valid');
		if(isset($_POST['is_public']) && $_POST['is_public'] == true)
			$is_public = 1;
		else
			$is_public = 0;
		// Make sure end time comes after start time
		if (new DateTime($start_time) > new DateTime($end_time))
			returnError('An end time that is before the start time? Are you a time traveller?');
		if (new DateTime($start_time) == new DateTime($end_time))
			returnError('Start time and end time are the same? You can\'t possibly make it that fast!');

		// Add new event into database on behalf of the currently logged in user
		$stmt = $mysqli->prepare("INSERT INTO events (creator, title, start_time, end_time, is_public) values (?, ?, ?, ?, ?)") 
			or returnError('Query Prep Failed: '.htmlspecialchars($mysqli->error));
		$stmt->bind_param('ssssd', $creator, $title, $start_time, $end_time, $is_public) 
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