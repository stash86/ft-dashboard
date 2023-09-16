<?php
// header("Content-Type:application/json");
// require_once('../app/api.php');	

$enabled = getenv('API_ENABLED') ?: "false";
if (boolval($enabled)) {
	echo "true";
} else {
	echo "false";
}