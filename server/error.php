<?php
function returnError($string) {
	header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    die(json_encode(array('error' => $string)));
}
?>