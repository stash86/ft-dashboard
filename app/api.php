<?php
require_once('../vendor/autoload.php');
require_once('libraries/db.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$allowed_response = ['status', 'trades', 'profit'];
$result = [];
$enabled = ($_ENV['API'] == 'true') ?: false;

echo $_ENV['API'].'</br>';

if ($enabled) {
    echo "a";
} else {
    echo "b";
}

// if (isset($_GET['response']) && ($_GET['response']!="") && isset($_GET['bot_id']) && ($_GET['bot_id']!="") && $enabled) {
// 	// include('db.php');
// 	$response = $_GET['response'];
//     $bot_id = $_GET['bot_id'];
//     if(in_array($response, $allowed_response)) {
//         $path = '../bots.json';
//         $jsonString = file_get_contents($path);
//         $jsonData = json_decode($jsonString, true);

//         if(isset($jsonData[intval($bot_id)-1])) {
//             $ip_to_check = $jsonData[intval($bot_id)-1]['ip_address'];
//             $data1 = $collection->findOne(['_id' => $ip_to_check]);

//             if (!is_null($data1)) {
//                 $result = response($data1[$response], 200, "Data Found");
//             } else {
//                 $result = response(NULL, 204,"No Record Found");
//             }
//         } else {
//             $result = response(NULL, 204,"No Bot Found");
//         }

        
//     } else {
//         $result = response(NULL, 204,"No Response Found");
//     }
// }else{
// 	$result = response(NULL, 400,"Invalid Request");
// }

// echo json_encode($result);

// function response($data,$response_code,$response_desc){
//     $result = [];
// 	$result['data'] = $data;
// 	$result['response_code'] = $response_code;
// 	$result['response_desc'] = $response_desc;
	
// 	return $result;
// }