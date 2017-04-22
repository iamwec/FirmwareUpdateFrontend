<?php
$Task = $_POST['Task'];

switch($Task) {

	// get file saved by endpoint.php
	case 'get':
		$filename = 'response.json';
		if(file_exists($filename)) {
			$fh = fopen($filename, 'r');
			$data = json_decode(fread($fh, filesize($filename)), true);
			echo $data;
		}
		break;

	// save user settings
	case 'save':
		break;

	// call discover service
	case 'discover':
		break;

	// call delete endpoint
	case 'delete':
		break;

	default:
		break;
}
?>