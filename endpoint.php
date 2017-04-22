<?php
include_once('total.php');

$task = substr($_SERVER['PATH_INFO'], 1);
$body = file_get_contents('php://input');

if (!empty($body)) {
	switch($task) {
		case 'enroll':
			// Enroll receives device information requesting access code
			$fh = fopen('enroll.json', 'w');
			break;
		case 'discoveryResults':
			// Discovery results returns list 
			$fh = fopen('discoveryResults.json', 'w');
			break;
		default:
			$fh = fopen('response.json', 'w');
			break;
	}

	// Write the response to file, wait for handler to pick it up
	fwrite($fh, $body);

	// Close the file
	fclose($fh);
}
?>