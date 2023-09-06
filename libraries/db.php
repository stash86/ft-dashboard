<?php
require_once(__DIR__.'/../vendor/autoload.php');

$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: '';
$pass = getenv('DB_PASS') ?: '';
$port = getenv('DB_PORT') ?: '27017';

$client = new MongoDB\Client("mongodb://". $user . ":" . $pass . "@" .  $host . ":27017");

$databaseName = "myDatabase";
$collectionName = "myCollection";
$database = $client->selectDatabase($databaseName);
$collection = $database->selectCollection($collectionName);