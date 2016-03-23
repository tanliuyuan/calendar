<?php
ini_set("session.cookie_httponly", 1);
session_start();

require_once('database.php');
require_once('functions.php');

if(isset($_POST) && isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
	if($_SESSION['token'] === $_POST['token']) {
		if(!empty($_SESSION['username']))
			$creator = $_SESSION['username'];
		else
			returnError('Error while deleting event: No user is logged in!');
		if(!empty($_POST['id']))
			$id = $_POST['id'];
		if(empty($id))
			returnError('Error while deleting event: Can\'t find event id!');
			
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
		
		// Delete event from database
		if($event_count === 1) {
			$stmt = $mysqli->prepare("DELETE FROM events WHERE id=?") 
				or returnError('Query Prep Failed: '.htmlspecialchars($mysqli->error));
			$stmt->bind_param('d', $id) 
				or returnError('Parameter Binding Failed: '.htmlspecialchars($mysqli->error));
			$stmt->execute() 
				or returnError('Query Execution Failed: '.htmlspecialchars($mysqli->error));
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