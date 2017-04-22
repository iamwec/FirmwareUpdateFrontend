<?php
$task = substr($_SERVER['PATH_INFO'], 1);
$body = file_get_contents('php://input');

if (!empty($inData)) {
	$file = fopen('response.json', 'w');
	fwrite($file, $task.PHP_EOL);
	fwrite($file, $body);
	fclose($file);
}
?>