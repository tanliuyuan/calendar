<?php
ini_set("session.cookie_httponly", 1);
session_start();

require_once('database.php');
require_once('functions.php');

date_default_timezone_set('America/Chicago');

// Make sure user is logged in and CSRF token is valid
if(isset($_POST) && isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
	if($_SESSION['token'] === $_POST['token']) {
		// Fetch, validate, and assign input data
		if(!empty($_SESSION['username']))
			$creator = $_SESSION['username'];
		else
			returnError('Error while editing event: No user is logged in!');
		if(!empty($_POST['id']))
			$id = $_POST['id'];
		if(empty($id))
			returnError('Error while deleting event: Can\'t find event id!');
		if(!empty($_POST['title']))
			$title = preg_match('/^[A-Za-z.\ \'\-]{1,50}$/', $_POST['title']) ? $_POST['title'] : "";
		if(empty($title))
			returnError('Error while editing event: Title not valid');
		if(!empty($_POST['start_time']))
			$start_time = preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/', $_POST['start_time']) ? $_POST['start_time'] : "";
		if(empty($start_time))
			returnError('Error while editing event: Start time not valid');
		if(!empty($_POST['end_time']))
			$end_time = preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/', $_POST['end_time']) ? $_POST['end_time'] : "";
		if(empty($end_time))
			returnError('Error while editing event: End time not valid');
		// Make sure end time comes after start time
		if (new DateTime($start_time) > new DateTime($end_time))
			returnError('An end time that is before the start time? Are you a time traveller?');
		if (new DateTime($start_time) === new DateTime($end_time))
			returnError('Start time and end time are the same? You can\'t possibly make it that fast!');
			
		// Make sure event is created by user
		$stmt = $mysqli->prepare("SELECT COUNT(*), events.id FROM events INNER JOIN users ON events.creator=users.username WHERE events.creator=? AND events.id=?") 
			or returnError('Query Prep Failed: '.htmlspecialchars($mysqli->error));
		$stmt->bind_param('sd', $creator, $id) 
			or returnError('Parameter Binding Failed: '.htmlspecialchars($mysqli->error));
		$stmt->execute() 
			or returnError('Query Execution Failed: '.htmlspecialchars($mysqli->error));
		$stmt->bind_result($event_count, $event_id)
			or returnError('Result Binding Failed: '.htmlentities($mysqli->error));
		$stmt->fetch()
			or returnError('Result Fetching Failed: '.htmlentities($mysqli->error));
		$stmt->close();
		
		// If event is created by user, then proceed
		if($event_count === 1) {
			// Delete event from database
			$stmt = $mysqli->prepare("UPDATE events SET title=?, start_time=?, end_time=? WHERE id=?") 
				or returnError('Query Prep Failed: '.htmlspecialchars($mysqli->error));
			$stmt->bind_param('sssd', $title, $start_time, $end_time, $id) 
				or returnError('Parameter Binding Failed: '.htmlspecialchars($mysqli->error));
			$stmt->execute() 
				or returnError('Query Execution Failed: '.htmlspecialchars($mysqli->error));
		} else {
			returnError('Error while editing event: Event is not created by user!');
		}
		
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