<?php
$api = getenv('API') ?: false;
if ($api) {
	header("Content-Type:application/json");
	require_once('../app/api.php');	
}
