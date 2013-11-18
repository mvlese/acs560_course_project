<?php

function logger($message) {
	$date = new DateTime();
	$msg = $date->format('Y-m-d H:i:s');
	$msg .= " ";
	$msg .= $message . "\n";
	error_log($msg, 3, "jot-error.log");
}

?>
