<?php
require_once(__DIR__.'/../libraries/coingekko.php');
require_once(__DIR__.'/../libraries/db.php');

$client = new Coingekko();
$data_prices = $client->getPriceProgrammed();

$price = $collection->findOne(['_id'=>'coingekko']);

if(is_null($price)) {
	$new_doc = [
		"_id"=> 'coingekko',
		"data" => $data_prices
	];

	$operation_insert = $collection->insertOne($new_doc);
	printf("Inserted %d document(s)\n", $operation_insert->getInsertedCount());
} else {
	$new_doc = [
		"_id"=> 'coingekko',
		"data" => $data_prices
	];

	$operation_update = $collection->updateOne(
		[ '_id' => 'coingekko' ],
		[ '$set' => [ 'data' => $data_prices ]]
	);

	printf("Matched %d document(s)\n", $operation_update->getMatchedCount());
	printf("Modified %d document(s)\n", $operation_update->getModifiedCount());
}