<?php
require_once(__DIR__.'/../libraries/api.php');
require_once(__DIR__.'/../../vendor/autoload.php');
require_once(__DIR__.'/../libraries/date_helper.php');
require_once(__DIR__.'/../libraries/db.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$path = '../../bots.json';
$jsonString = file_get_contents($path);
$jsonData = json_decode($jsonString, true);
$current_time = new DateTime();

$start_0 = ($_ENV['START_0'] == 'true') ?: false;
$bot_name_as_index = ($_ENV['BOT_NAME_AS_INDEX'] == 'true') ?: false;

$array_others=[];
$array_strategy_name=[];

$find_char = array("_prod","_");
$replace_char = array("", " ");

for($i=0; $i < count($jsonData); $i++){
	echo "fetch datas from {$jsonData[$i]['ip_address']}\n";
	$api = new Api($jsonData[$i]['ip_address'], $jsonData[$i]['username'], $jsonData[$i]['password']);
	$profit = json_decode($api->profit(), true);
	if (empty($profit)) {
		$find = $collection->findOne(['_id'=>$jsonData[$i]['ip_address']]);
		if(!is_null($find)) {
			$deleteResult = $collection->deleteOne(['_id'=>$jsonData[$i]['ip_address']]);
			$deleted = $deleteResult->getDeletedCount();
			echo "Delete {$deleted} {$jsonData[$i]['ip_address']} data from db\n";
		}
		continue;
	}
	
	$balance = json_decode($api->balance(), true);
	$start_balance = $balance['starting_capital'];

	$profit['profit_open_coin'] = floatval($profit['profit_all_coin']) - floatval($profit['profit_closed_coin']);
	$profit['profit_open_ratio'] = floatval($profit['profit_open_coin']) / floatval($start_balance);
	$profit['days'] = days_interval($current_time, $profit['first_trade_date']);
	$trade_count = $profit['closed_trade_count'];
	
	$profit['winrate'] = round($profit['winrate'] * 100, 2)?:(($trade_count > 0) ? round($profit['winning_trades'] * 100 / $trade_count, 2) : 0);

	$trades = json_decode($api->trades($trade_count), true)['trades'];
	
	// Prepare profit chart data
	$total_profit = 0;
    $total_profit_abs = 0;
    $chart_profit_data = [];

    $time_data = [];

    for($j=0 ; $j<count($trades) ; $j++){
        $time_data[] = array(
            'close_timestamp'=> $trades[$j]['close_timestamp'],
            'close_profit_abs'=> $trades[$j]['close_profit_abs'],
            'close_date'=> $trades[$j]['close_date']
        );
    }

    usort($time_data, function($a, $b){
        return $a["close_timestamp"] > $b["close_timestamp"] ? -1 : 1;
    });

    for($j = (count($time_data) - 1); $j >= 0; $j--){

    	if ($start_0 && ($j == (count($time_data) - 1))) {
    		$chart_profit_data[] = [
            	"close_profit" => 0,
            	"close_profit_abs" => 0,
            	"close_date" => $time_data[$j]['close_date'],
            	"is_open" => 0
            ];
    	}

        $close_profit = (($time_data[$j]['close_profit_abs'] * 100 / $start_balance) + $total_profit);
        $total_profit = $close_profit;
        $close_profit = round($close_profit, 3);

        $close_profit_abs =  ($time_data[$j]['close_profit_abs'] + $total_profit_abs);
        $total_profit_abs = $close_profit_abs;
        // $closed_trades_asc[$i]['is_open'] = 0;

        $chart_profit_data[] = [
        	"close_profit"=> $close_profit,
        	"close_profit_abs"=>$close_profit_abs,
        	"close_date"=>$time_data[$j]['close_date'],
        	"is_open"=>0
        ];
    }

	$status = json_decode($api->status(), true);

	if(count($status) > 0) {
        $open_trades_profit = 0;
        foreach ($status as $key => $value) {
            $open_trades_profit += ($value['profit_abs'] + $value['realized_profit']);
        }
        $date_now = new DateTime();
        $open_profit_pct = $total_profit + ($open_trades_profit * 100 / $start_balance);
        $open_profit_abs = $total_profit_abs + $open_trades_profit;
        $chart_profit_data[] = array('close_date'=>date_format($date_now,"Y-m-d H:i:s.u"), 'close_profit'=> $open_profit_pct, 'close_profit_abs'=> $open_profit_abs, 'is_open'=>1);
    }
	
	$config = json_decode($api->show_config(), true);
	$strategy_name = $config['strategy'];
	if ($bot_name_as_index){
		$strategy_name = $config['bot_name'];
	}

	$performance = json_decode($api->performance(), true);

	$entries = json_decode($api->entries(), true);
	$exits = json_decode($api->exits(), true);
	$mix_tags = json_decode($api->mix_tags(), true);

	echo "Strategy name is {$strategy_name}\n";
	
	$sanitized_name = str_replace($find_char, $replace_char, $strategy_name);
	
	$document = [
		"_id"=> $jsonData[$i]['ip_address'],
		"strategy"=> $sanitized_name,
		"strategy_version"=> $config['strategy_version'],
		"profit"=> $profit,
		"trades"=> $trades,
		"status"=> $status,
		"balance"=> $balance,
		"config"=> $config,
		"performance"=> $performance,
		"entries"=> $entries,
		"exits"=> $exits,
		"mix_tags"=> $mix_tags,
		"chart_profit_data"=> $chart_profit_data,
		"fetched"=> $current_time->format('Y-m-d H:i:s')
	];

	$find = $collection->findOne(['_id'=>$jsonData[$i]['ip_address']]);
	if(is_null($find)) {
		$insertDoc = $collection->insertOne($document);
		$inserted = $insertDoc->getInsertedCount();
		echo "Insert {$inserted} {$jsonData[$i]['ip_address']} data to db\n";
	} else {
		$operation_update = $collection->replaceOne(
			[ '_id' => $jsonData[$i]['ip_address'] ],
			$document
		);

		$replaced = $operation_update->getModifiedCount();

		echo "Replaced {$replaced} {$jsonData[$i]['ip_address']} data on db\n";
	}

	$array_strategy_name[] = $sanitized_name;

	$dur_sec = duration_string_to_seconds($profit['avg_duration']);
	if($dur_sec > 0) {
		$array_others[] = [
			"strategy"=> $sanitized_name,
			"duration"=> $profit['avg_duration'],
			"duration_sec"=> $dur_sec,
			"trade_count"=> $profit['closed_trade_count'],
			"winning_trades"=> $profit['winning_trades'],
			"losing_trades"=> ($profit['closed_trade_count'] - $profit['winning_trades']),
		];
	}
	
}

$document = [
	"_id"=> 'others',
	"data" => $array_others
];

$find = $collection->findOne(['_id'=>'others']);
if(is_null($find)) {
	$insertDoc = $collection->insertOne($document);
	$inserted = $insertDoc->getInsertedCount();
	echo "Insert {$inserted} 'others' data to db\n";
} else {
	$operation_update = $collection->replaceOne(
		[ '_id' => 'others' ],
		$document
	);

	$replaced = $operation_update->getModifiedCount();

	echo "Replaced {$replaced} 'others' data on db\n";
}

sort($array_strategy_name);

$document = [
	"_id"=> 'names',
	"data" => $array_strategy_name
];

$find = $collection->findOne(['_id'=>'names']);
if(is_null($find)) {
	$insertDoc = $collection->insertOne($document);
	$inserted = $insertDoc->getInsertedCount();
	echo "Insert {$inserted} 'names' data to db\n";
} else {
	$operation_update = $collection->replaceOne(
		[ '_id' => 'names' ],
		$document
	);

	$replaced = $operation_update->getModifiedCount();

	echo "Replaced {$replaced} 'names' data on db\n";
}