<?php
ini_set("session.cookie_httponly", 1);
session_start();

require_once('database.php');
require_once('functions.php');

$eventsArray = array();

// Fetch events from currently logged in user
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['username'])) {
	$stmt = $mysqli->prepare("SELECT events.title, events.start_time, events.end_time FROM events INNER JOIN users ON events.creator=users.username WHERE users.username=?") 
		or returnError('Query Prep Failed: '.htmlspecialchars($mysqli->error));
	$stmt->bind_param('s', $_SESSION['username']) 
		or returnError('Parameter Binding Failed: '.htmlspecialchars($mysqli->error));
	$stmt->execute() 
		or returnError('Query Execution Failed: '.htmlspecialchars($mysqli->error));
	$result = $stmt->get_result() 
		or returnError('Result Getting Failed: '.htmlspecialchars($mysqli->error));
	while($row = $result->fetch_assoc()) {
		$event = array(
			'title' => htmlspecialchars($row['title']),
			'start' => htmlspecialchars($row['start_time']),
			'end' => htmlspecialchars($row['end_time'])
		);
		array_push($eventsArray, $event);
	}
	$stmt->close();
}

// Return events as a JSON feed
returnSuccess(json_encode($eventsArray));
?>