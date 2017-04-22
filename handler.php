<?php
include_once('total.php');

$Task = $_POST['Task'];

switch($Task) {
	// get file saved by endpoint.php
	case 'get_enroll':
		$filename = 'enroll.json';
		echo get_file_data($filename);
		break;

	case 'get_discoveryResults':
		$filename = 'discoveryResults.json';
		echo get_file_data($filename);
		break;

	// Call serviceURL/enroll to start enrollment, start jquery check to get_enroll for 120 seconds
	case 'start_enrollment':
		break;

	// Call serviceURL/accessoryValidation and send info
	case 'send_accessoryValidation':
		$accCodes = json_encode($_POST['accCodes']);
		if (!empty($accCodes)) {
			//call
		}
		break;

	// call serviceURL/applyUpdates
	case 'start_applyUpdate':
		break;

	// call serviceURL/searchForUpdates
	case 'start_searchForUpdates':
		break;

	// /resumeUpdate/{deviceId}
	// ??

	// call serviceURL/unenroll/{deviceId}
	case 'delete':
		$deviceID = $_POST['deviceID'];
		if (!empty($deviceID)) {
			//call
		}
		break;

	// /kill
	// /resetApp

	default:
		break;
}

function get_file_data($filename) {
	if(file_exists($filename)) {
		$fh = fopen($filename, 'r');
		$data = fread($fh, filesize($filename));
		echo $data;
		fclose($fh);
	} else {
		// Return nothing here, null means nothing found
	}
}
?>