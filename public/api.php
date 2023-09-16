<?php
// header("Content-Type:application/json");
// require_once('../app/api.php');	

$enabled = getenv('API') ?: false;
if (boolval($enabled)) {
	echo "true";
} else {
	echo "false";
}