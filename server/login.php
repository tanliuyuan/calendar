<?php
ini_set("session.cookie_httponly", 1);
session_start();

require_once('database.php');
require_once('functions.php');

// Fetch, validate, and assign input data
if(isset($_POST)) {
	if(!empty($_POST['username']))
		$username = preg_match('/^[A-Za-z0-9_-]{3,16}$/', $_POST['username']) ? $_POST['username'] : "";
	if(empty($username))
		returnError('username not valid');
	if(!empty($_POST['password']))
		$password = preg_match('/^[A-Za-z0-9_-]{6,18}$/', $_POST['password']) ? $_POST['password'] : "";
	if(empty($password))
		returnError('password not valid');
	else
		login($username, $password);
}
?>