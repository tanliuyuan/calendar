<?php
ini_set("session.cookie_httponly", 1);
session_start();

require_once('database.php');
require_once('functions.php');

$eventsArray = array();

// Fetch all public events
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['username'])) {
	$stmt = $mysqli->prepare("SELECT events.id, events.title, events.start_time, events.end_time FROM events WHERE events.is_public=1 ") 
		or returnError('Query Prep Failed: '.htmlspecialchars($mysqli->error));
	$stmt->execute() 
		or returnError('Query Execution Failed: '.htmlspecialchars($mysqli->error));
	$result = $stmt->get_result() 
		or returnError('Result Getting Failed: '.htmlspecialchars($mysqli->error));
	while($row = $result->fetch_assoc()) {
		$event = array(
			'id' => $row['id'],
			'title' => $row['title'],
			'start' => $row['start_time'],
			'end' => $row['end_time'],
			'className' => 'public',
			'backgroundColor' => '#FF9009',
			'borderColor' => '#FF9009'
		);
		array_push($eventsArray, $event);
	}
	$stmt->close();
}

// Fetch private events from currently logged in user
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['username'])) {
	$stmt = $mysqli->prepare("SELECT events.id, events.title, events.start_time, events.end_time FROM events INNER JOIN users ON events.creator=users.username WHERE users.username=? AND events.is_public=0") 
		or returnError('Query Prep Failed: '.htmlspecialchars($mysqli->error));
	$stmt->bind_param('s', $_SESSION['username']) 
		or returnError('Parameter Binding Failed: '.htmlspecialchars($mysqli->error));
	$stmt->execute() 
		or returnError('Query Execution Failed: '.htmlspecialchars($mysqli->error));
	$result = $stmt->get_result() 
		or returnError('Result Getting Failed: '.htmlspecialchars($mysqli->error));
	while($row = $result->fetch_assoc()) {
		$event = array(
			'id' => $row['id'],
			'title' => $row['title'],
			'start' => $row['start_time'],
			'end' => $row['end_time'],
			'className' => 'private'
		);
		array_push($eventsArray, $event);
	}
	$stmt->close();
}

// Return events as a JSON feed
returnSuccess($eventsArray);
?>