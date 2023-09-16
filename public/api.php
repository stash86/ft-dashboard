<?php
// header("Content-Type:application/json");
// require_once('../app/api.php');	

$enabled = boolval(getenv('API')) ?: false;
if ($enabled) {
	echo "true";
} else {
	echo "false";
}