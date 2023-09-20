<?php
require_once('../app/libraries/api.php');
require_once('../vendor/autoload.php');

$path = '../bots.json';
$jsonString = file_get_contents($path);
$jsonData = json_decode($jsonString, true);
$current_time = new DateTime();

for($i=0; $i < count($jsonData); $i++){
    echo "fetch datas from {$jsonData[$i]['ip_address']}<br/>";
    $api = new Api($jsonData[$i]['ip_address'], $jsonData[$i]['username'], $jsonData[$i]['password']);
    $profit = json_decode($api->profit(), true);
    if (empty($profit)) {
        echo "No data <br/><br/>";
        continue;
    }
    $config = json_decode($api->show_config(), true);
    $strategy_name = $config['strategy'];
    echo "strategy name is {$strategy_name}<br/><br/>";
}