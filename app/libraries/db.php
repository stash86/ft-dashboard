<?php
require_once(__DIR__.'/../../vendor/autoload.php');
try {
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
	$dotenv->load();	
} catch (Exception $e) {
	echo json_encode($e);
}


$host = $_ENV['DB_HOST'] ?: '127.0.0.1';
$user = $_ENV['DB_USER'] ?: '';
$pass = $_ENV['DB_PASS'] ?: '';
$port = $_ENV['DB_PORT'] ?: '27017';

// $host = getenv('DB_HOST') ?: '127.0.0.1';
// $user = getenv('DB_USER') ?: '';
// $pass = getenv('DB_PASS') ?: '';
// $port = getenv('DB_PORT') ?: '27017';

$client = new MongoDB\Client("mongodb://". $user . ":" . $pass . "@" .  $host . ":27017");

$databaseName = "myDatabase";
$collectionName = "myCollection";
$database = $client->selectDatabase($databaseName);
$collection = $database->selectCollection($collectionName);