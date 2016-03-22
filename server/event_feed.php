<?php
$array = array(
	'1' => array(
		'title' => 'Event1',
		'start' => '2016-03-11T03:45:40+00:00',
		'end' => '2016-03-11T04:45:40+00:00'
	),
	'2' => array(
		'title' => 'Event1',
		'start' => '2016-04-11T03:45:40+00:00',
		'end' => '2016-04-11T04:45:40+00:00'
	)
);
echo(json_encode($array));
?>