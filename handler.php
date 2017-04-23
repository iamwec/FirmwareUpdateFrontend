<?php
include_once('total.php');

$Task = $_POST['Task'];

switch($Task) {
	// get file saved by endpoint.php
	case 'get_enroll':
		$filename = 'enroll.json';
		$returnData = get_file_data($filename);
		if (!empty($returnData)) {
			echo $returnData;
		}
		break;

	case 'get_discoveryResults':
		$filename = 'discoveryResults.json';
		$statuses = json_decode(get_file_data($filename), true);
		if(sizeof($statuses) > 0) {
			$deviceData = json_decode(get_file_data('enroll.json'), true)['devices'];
			for ($i = 0; $i < sizeof($deviceData); $i++) {
				if(in_array($deviceData[$i]['deviceId'], $statuses['registeredDevices']))
					$deviceData[$i]['status'] = '<span class="success">Registered</span>';
				if(in_array($deviceData[$i]['deviceId'], $statuses['failedVerification']))
					$deviceData[$i]['status'] = '<span class="error">Invalid Accessory Code</span>';
				if(in_array($deviceData[$i]['deviceId'], $statuses['failedToRespond']))
					$deviceData[$i]['status'] = '<span class="warning">Device Did Not Respond</span>';
			}
			echo json_encode($deviceData);
		}
		break;

	// Call serviceURL/enroll to start enrollment, start jquery check to get_enroll for 120 seconds
	case 'start_enrollment':
		post_data('enroll', '');
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
				$tmpArr[$i]['deviceId'] = substr($tmpArr[$i]['deviceId'], 0, -1);
				$tmpArr[$i]['accessoryCode'] = $code['value'];
				$i++;
			}
			$returnArr['devices'] = $tmpArr;
			post_data('accessoryValidation', json_encode($returnArr));
		}
		break;

	// call serviceURL/applyUpdates
	case 'start_applyUpdate':
		post_data('applyUpdates', '');
		break;

	// call serviceURL/searchForUpdates
	case 'start_searchForUpdates':
		post_data('searchForUpdates', '');
		break;

	// call serviceURL/unenroll/{deviceId}
	case 'delete':
		$deviceID = $_POST['deviceID'];
		if (!empty($deviceID)) {
			post_data('unenroll/'.$deviceID, '');
		}
		break;

	// /kill
	case 'kill':
		post_data('kill', '');
		break;

	// /resetApp
	case 'resetApp':
		post_data('resetApp', '');
		break;

	default:
		break;
}

function get_file_data($filename) {
	if(file_exists($filename) && filesize($filename) > 0) {
		$fh = fopen($filename, 'r');
		$data = fread($fh, filesize($filename));
		fclose($fh);
		return $data;
	} else {
		// Return nothing here, null means nothing found
	}
}

function post_data($endpoint, $data) {
	$ch = curl_init(ServiceURL.$endpoint);
	$curlOptArr = array(
		CURLOPT_HTTPHEADER => array("Content-type: application/json"),
		CURLOPT_POST => 1,
		CURLOPT_TIMEOUT => 2,
		CURLOPT_POSTFIELDS => $data
	);

	curl_setopt_array($ch, $curlOptArr);

	$results = curl_exec($ch);
	curl_close($ch);
}
?>