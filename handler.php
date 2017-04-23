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
		post_data('enroll');
		break;

	// Call serviceURL/accessoryValidation and send info
	case 'send_accessoryValidation':
		$accCodes = $_POST['accCodes'];
		if (is_array($accCodes) && sizeof($accCodes) > 0) {
			$tmpArr = array();
			$returnArr = array();
			$i = 0;
			foreach ($accCodes as $code) {
				$tmpArr[$i]['deviceId'] = substr($code['name'], 9);
				$tmpArr[$i]['deviceId'] = substr($returnArr[$i]['deviceId'], 0, -1);
				$tmpArr[$i]['accessoryCode'] = $code['value'];
				$i++;
			}
			$returnArr['devices'] = $tmpArr;
			post_data('accessoryValidation', json_encode($returnArr));
		}
		break;

	// call serviceURL/applyUpdates
	case 'start_applyUpdate':
		post_data('applyUpdates');
		break;

	// call serviceURL/searchForUpdates
	case 'start_searchForUpdates':
		post_data('searchForUpdates');
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
	if(file_exists($filename) && filesize($filename) > 0) {
		$fh = fopen($filename, 'r');
		$data = fread($fh, filesize($filename));
		echo $data;
		fclose($fh);
		//unlink($filename);
	} else {
		// Return nothing here, null means nothing found
	}
}

function post_data($endpoint, $data = array()) {
	$test = ServiceURL.$endpoint;
	$ch = curl_init(ServiceURL.$endpoint);
	$curlOptArr = array(
		CURLOPT_HTTPHEADER => 'POST',
		CURLOPT_TIMEOUT => 2,
		CURLOPT_POSTFIELDS => $data
	);

	curl_setopt_array($ch, $curlOptArr);

	$results = curl_exec($ch);
	curl_close($ch);
}
?>