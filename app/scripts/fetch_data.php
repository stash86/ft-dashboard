<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

$start_time = microtime(true);

require_once(__DIR__ . '/../libraries/api.php');
require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../libraries/date_helper.php');
require_once(__DIR__ . '/../libraries/db.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Initialize PostgreSQL connection with explicit configuration
$db = new PostgreSqlDb();

$path = __DIR__ . '/../../bots.json';
$jsonString = file_get_contents($path);
$current_encoding = mb_detect_encoding($jsonString, "UTF-8", true);
if ($current_encoding !== "UTF-8") {
    $jsonString = mb_convert_encoding($jsonString, "UTF-8", $current_encoding);
}
$jsonData = json_decode($jsonString, true);

$current_time = new DateTime();

$start_0 = ($_ENV['START_0'] == 'true');
$bot_name_as_index = ($_ENV['BOT_NAME_AS_INDEX'] == 'true');

$array_others = [];

$find_char = array("_prod", "_");
$replace_char = array("", " ");

$profits = [];
$profits["daily"] = [];
$profits["weekly"] = [];
$profits["monthly"] = [];

$profits["daily_public"] = [];
$profits["weekly_public"] = [];
$profits["monthly_public"] = [];
$count_bots = 0;

$initial_balance_sum = 0;
$all_profit_sum = 0;
$closed_profit_sum = 0;
$open_profit_sum = 0;

/**
 * Save data to the database.
 *
 * @param \App\Libraries\PostgreSqlDb $db The PostgreSQL database connection.
 * @param string $label The label for the document.
 * @param array $data The data to be saved.
 */
function save_data_to_db($db, $label, $data)
{
    // Ensure ip_address is in the data and use it as the strategy_name for PostgreSQL compatibility
    $data['ip_address'] = $label; // Make sure ip_address field exists in the JSON data

    // Use upsertJsonData with IP address as both the key and the strategy_name
    // This ensures the record can be found both by strategy_name (primary key) and by JSON field search
    $success = $db->upsertJsonData('strategies', $label, $data, 'strategy_name');

    if ($success) {
        echo "Saved '{$label}' data to database\n";
    } else {
        echo "Failed to save '{$label}' data to database\n";
    }
}

/**
 * Save summary data to the strategies table.
 * This is used for data that the StrategyModel expects to find in strategies table
 *
 * @param \App\Libraries\PostgreSqlDb $db The PostgreSQL database connection.
 * @param string $ipAddress The IP address identifier (like 'sum_bots', 'daily', etc.).
 * @param array $data The data to be saved.
 */
function save_strategy_summary_to_db($db, $ipAddress, $data)
{
    // Add ip_address to the data for consistency
    $dataWithIp = array_merge($data, ['ip_address' => $ipAddress]);

    $success = $db->upsertJsonData('strategies', $ipAddress, $dataWithIp, 'strategy_name');

    if ($success) {
        echo "Saved '{$ipAddress}' summary data to strategies table\n";
    } else {
        echo "Failed to save '{$ipAddress}' summary data to strategies table\n";
    }
}

$jsonDataCount = count($jsonData);
for ($i = 0; $i < $jsonDataCount; $i++) {
    $public_bot = $jsonData[$i]['public'] ?? true;

    echo "fetch datas from {$jsonData[$i]['ip_address']}\n";
    $api = new Api(
        $jsonData[$i]['ip_address'],
        $jsonData[$i]['username'],
        $jsonData[$i]['password']
    );
    echo "fetch profit";
    $start_temp = microtime(true);
    $profit = json_decode($api->profit(), true);
    $end_temp = microtime(true);
    $dur_temp = ($end_temp - $start_temp);
    echo " took " . $dur_temp . " secs\n";
    $public_name = $jsonData[$i]['name'] ?? '';
    if (empty($profit)) {
        // Check if strategy exists and remove it if no profit data
        $existing = $db->findOneByJsonField('strategies', 'ip_address', $jsonData[$i]['ip_address']);
        if (!is_null($existing)) {
            // For deletion, we need to find by strategy_name (which should be the IP for compatibility)
            $deleteResult = $db->rawQuery(
                "DELETE FROM strategies WHERE data->>'ip_address' = ?",
                [$jsonData[$i]['ip_address']]
            );
            echo "Deleted {$jsonData[$i]['ip_address']} data from database\n";
        }
        continue;
    }

    echo "fetch balance";
    $start_temp = microtime(true);
    $balance = json_decode($api->balance(), true);
    $end_temp = microtime(true);
    $dur_temp = ($end_temp - $start_temp);
    echo " took " . $dur_temp . " secs\n";
    $start_balance = floatval($balance['starting_capital']);

    if ($start_balance > 0) {
        $trade_count = $profit['closed_trade_count'];

        echo "fetch config";
        $start_temp = microtime(true);
        $config = json_decode($api->showConfig(), true);
        $end_temp = microtime(true);
        $dur_temp = ($end_temp - $start_temp);
        echo " took " . $dur_temp . " secs\n";

        echo "fetch trades";
        $start_temp = microtime(true);
        $trades_call = json_decode($api->trades($trade_count), true);
        $trades = [];
        if (!empty($trades_call) && array_key_exists('trades', $trades_call)) {
            $trades = $trades_call['trades'];
        }
        $end_temp = microtime(true);
        $dur_temp = ($end_temp - $start_temp);
        echo " took " . $dur_temp . " secs\n";

        $profit_all_coin = floatval($profit['profit_all_coin']);
        $profit_closed_coin = floatval($profit['profit_closed_coin']);
        $profit_open_coin = $profit_all_coin - $profit_closed_coin;

        if (!$config["dry_run"]) {
            $initial_balance_sum += $start_balance;
            $all_profit_sum += $profit_all_coin;
            $closed_profit_sum += $profit_closed_coin;
            $open_profit_sum += $profit_open_coin;
        } else {
            echo "DRY RUN, skip profit summary calculation\n";
        }

        $profit['profit_open_coin'] = $profit_open_coin;
        $profit['profit_open_ratio'] = ($profit_open_coin / $start_balance);
        $profit['days'] = days_interval($current_time, $profit['bot_start_date']);
        $profit['last_trade_days'] = days_interval($current_time, $profit['latest_trade_date']);


        $profit['winrate'] = round($profit['winrate'] * 100, 2) ?:
            (($trade_count > 0) ? round($profit['winning_trades'] * 100 / $trade_count, 2) : 0);

        // Prepare profit chart data
        $total_profit = 0;
        $total_profit_abs = 0;
        $chartProfitData = [];

        $time_data = [];

        if (!empty($trades)) {
            $num_trades = count($trades);
            echo "Has {$num_trades} trades\n";
            for ($j = 0; $j < $num_trades; $j++) {
                $time_data[] = array(
                    'close_timestamp' => $trades[$j]['close_timestamp'],
                    'close_profit_abs' => $trades[$j]['close_profit_abs'],
                    'close_date' => $trades[$j]['close_date'],
                    'stake' => $config['stake_currency']
                );
            }
        } else {
            echo "No trades data\n";
        }

        usort($time_data, function ($a, $b) {
            return $a["close_timestamp"] > $b["close_timestamp"] ? -1 : 1;
        });

        for ($j = (count($time_data) - 1); $j >= 0; $j--) {
            if ($start_0 && (count($chartProfitData) == 0)) {
                $chartProfitData[] = [
                    "close_profit" => 0,
                    "close_profit_abs" => 0,
                    "close_date" => $time_data[$j]['close_date'],
                    "close_timestamp" => $time_data[$j]['close_timestamp'],
                    "stake" => $time_data[$j]['stake'],
                    "is_open" => 0
                ];
            }

            $close_profit = (($time_data[$j]['close_profit_abs'] * 100 / $start_balance) + $total_profit);
            $total_profit = $close_profit;
            $close_profit = round($close_profit, 3);

            $close_profit_abs = ($time_data[$j]['close_profit_abs'] + $total_profit_abs);
            $total_profit_abs = $close_profit_abs;
            // $closed_trades_asc[$i]['is_open'] = 0;

            $chartProfitData[] = [
                "close_profit" => $close_profit,
                "close_profit_abs" => $close_profit_abs,
                "close_date" => $time_data[$j]['close_date'],
                "close_timestamp" => $time_data[$j]['close_timestamp'],
                "stake" => $time_data[$j]['stake'],
                "is_open" => 0
            ];
        }

        echo "fetch status";
        $start_temp = microtime(true);
        $status = json_decode($api->status(), true);
        $end_temp = microtime(true);
        $dur_temp = ($end_temp - $start_temp);
        echo " took " . $dur_temp . " secs\n";

        if (count($status) > 0) {
            $open_trades_profit = 0;
            foreach ($status as $key => $value) {
                $open_trades_profit += ($value['profit_abs'] + $value['realized_profit']);
                $open_rate = $value['open_rate'];
                $min_rate = $value['min_rate'];
                $max_rate = $value['max_rate'];
                $value['min_profit'] = ($min_rate - $open_rate) / $open_rate;
                $value['max_profit'] = ($max_rate - $open_rate) / $open_rate;
            }
            $date_now = new DateTime();
            $open_profit_pct = $total_profit + ($open_trades_profit * 100 / $start_balance);
            $open_profit_abs = $total_profit_abs + $open_trades_profit;
            $chartProfitData[] = array(
                'close_date' => date_format($date_now, "Y-m-d H:i:s.u"),
                'close_timestamp' => (($date_now->getTimestamp()) * 1000),
                'close_profit' => $open_profit_pct,
                'close_profit_abs' => $open_profit_abs,
                'stake' => $config['stake_currency'],
                'is_open' => 1
            );
        }

        $strategy_name = trim($config['strategy']);
        if ($bot_name_as_index) {
            $strategy_name = trim($config['bot_name']);
        }

        echo "fetch performance";
        $start_temp = microtime(true);
        $performance = json_decode($api->performance(), true);
        $end_temp = microtime(true);
        $dur_temp = ($end_temp - $start_temp);
        echo " took " . $dur_temp . " secs\n";

        echo "fetch entries";
        $start_temp = microtime(true);
        $entries = json_decode($api->entries(), true);
        $end_temp = microtime(true);
        $dur_temp = ($end_temp - $start_temp);
        echo " took " . $dur_temp . " secs\n";

        echo "fetch exits";
        $start_temp = microtime(true);
        $exits = json_decode($api->exits(), true);
        $end_temp = microtime(true);
        $dur_temp = ($end_temp - $start_temp);
        echo " took " . $dur_temp . " secs\n";

        echo "fetch mix tags";
        $start_temp = microtime(true);
        $mixTags = json_decode($api->mixTags(), true);
        $end_temp = microtime(true);
        $dur_temp = ($end_temp - $start_temp);
        echo " took " . $dur_temp . " secs\n";

        $chart_daily_profit = [];
        $chart_weekly_profit = [];
        $chart_monthly_profit = [];

        if (!$config["dry_run"]) {
            echo "fetch daily";
            $start_temp = microtime(true);
            $daily = json_decode($api->daily(), true);
            $end_temp = microtime(true);
            $dur_temp = ($end_temp - $start_temp);
            echo " took " . $dur_temp . " secs\n";

            echo "fetch weekly";
            $start_temp = microtime(true);
            $weekly = json_decode($api->weekly(), true);
            $end_temp = microtime(true);
            $dur_temp = ($end_temp - $start_temp);
            echo " took " . $dur_temp . " secs\n";

            echo "fetch monthly";
            $start_temp = microtime(true);
            $monthly = json_decode($api->monthly(), true);
            $end_temp = microtime(true);
            $dur_temp = ($end_temp - $start_temp);
            echo " took " . $dur_temp . " secs\n";

            foreach ($daily["data"] as $day) {
                $current_date = $day["date"];
                if (!array_key_exists($current_date, $profits["daily"])) {
                    $profits["daily"][$current_date] = ["abs" => 0, "rel" => 0, "trade_count" => 0];
                    $profits["daily_public"][$current_date] = ["abs" => 0, "rel" => 0, "trade_count" => 0];
                }

                $current_abs = $day["abs_profit"];
                $current_rel = $day["rel_profit"];
                $trade_count = $day["trade_count"];

                $profits["daily"][$current_date]["abs"] += $current_abs;
                $profits["daily"][$current_date]["rel"] += $current_rel;
                $profits["daily"][$current_date]["trade_count"] += $trade_count;

                if ($public_bot) {
                    $profits["daily_public"][$current_date]["abs"] += $current_abs;
                    $profits["daily_public"][$current_date]["rel"] += $current_rel;
                    $profits["daily_public"][$current_date]["trade_count"] += $trade_count;
                }

                $chart_daily_profit[$current_date] = array(
                    'date' => $current_date,
                    'abs_profit' => $current_abs,
                    'rel_profit' => $current_rel,
                    'trade_count' => $trade_count
                );
            }

            foreach ($weekly["data"] as $week) {
                $current_week = $week["date"];
                if (!array_key_exists($current_week, $profits["weekly"])) {
                    $profits["weekly"][$current_week] = ["abs" => 0, "rel" => 0, "trade_count" => 0];
                    $profits["weekly_public"][$current_week] = ["abs" => 0, "rel" => 0, "trade_count" => 0];
                }

                $current_abs = $week["abs_profit"];
                $current_rel = $week["rel_profit"];
                $trade_count = $week["trade_count"];

                $profits["weekly"][$current_week]["abs"] += $current_abs;
                $profits["weekly"][$current_week]["rel"] += $current_rel;
                $profits["weekly"][$current_week]["trade_count"] += $trade_count;

                if ($public_bot) {
                    $profits["weekly_public"][$current_week]["abs"] += $current_abs;
                    $profits["weekly_public"][$current_week]["rel"] += $current_rel;
                    $profits["weekly_public"][$current_week]["trade_count"] += $trade_count;
                }

                $chart_weekly_profit[$current_week] = array(
                    'date' => $current_week,
                    'abs_profit' => $current_abs,
                    'rel_profit' => $current_rel,
                    'trade_count' => $trade_count
                );
            }

            foreach ($monthly["data"] as $month) {
                $current_month = $month["date"];
                if (!array_key_exists($current_month, $profits["monthly"])) {
                    $profits["monthly"][$current_month] = ["abs" => 0, "rel" => 0, "trade_count" => 0];
                    $profits["monthly_public"][$current_month] = ["abs" => 0, "rel" => 0, "trade_count" => 0];
                }

                $current_abs = $month["abs_profit"];
                $current_rel = $month["rel_profit"];
                $trade_count = $month["trade_count"];

                $profits["monthly"][$current_month]["abs"] += $current_abs;
                $profits["monthly"][$current_month]["rel"] += $current_rel;
                $profits["monthly"][$current_month]["trade_count"] += $trade_count;

                if ($public_bot) {
                    $profits["monthly_public"][$current_month]["abs"] += $current_abs;
                    $profits["monthly_public"][$current_month]["rel"] += $current_rel;
                    $profits["monthly_public"][$current_month]["trade_count"] += $trade_count;
                }

                $chart_monthly_profit[$current_month] = array(
                    'date' => $current_month,
                    'abs_profit' => $current_abs,
                    'rel_profit' => $current_rel,
                    'trade_count' => $trade_count
                );
            }
        } else {
            echo "DRY RUN, skip daily weekly monthly calculation\n";
        }

        echo "Strategy name is {$strategy_name}\n";

        $sanitized_name = trim(str_replace($find_char, $replace_char, $strategy_name));
        $document = [
            "ip" => $jsonData[$i]['ip_address'],
            "public_name" => $public_name,
            "strategy" => $sanitized_name,
            "strategy_version" => $config['strategy_version'],
            "profit" => $profit,
            "trades" => $trades,
            "status" => $status,
            "balance" => $balance,
            "config" => $config,
            "performance" => $performance,
            "entries" => $entries,
            "exits" => $exits,
            "mix_tags" => $mixTags,
            "daily" => $chart_daily_profit,
            "weekly" => $chart_weekly_profit,
            "monthly" => $chart_monthly_profit,
            "chart_profit_data" => $chartProfitData,
            "fetched" => $current_time->format('Y-m-d H:i:s')
        ];

        // Save strategy data to PostgreSQL
        save_data_to_db($db, $jsonData[$i]['ip_address'], $document);

        echo "Avg duration {$profit['avg_duration']}\n\n";
        $dur_sec = duration_string_to_seconds($profit['avg_duration']);
        if ($dur_sec > 0) {
            $array_others[] = [
                "strategy" => $sanitized_name,
                "duration" => $profit['avg_duration'],
                "duration_sec" => $dur_sec,
                "trade_count" => $profit['closed_trade_count'],
                "winning_trades" => $profit['winning_trades'],
                "losing_trades" => ($profit['closed_trade_count'] - $profit['winning_trades']),
            ];
        }

        $count_bots++;
    }
}

$rows = [];
foreach ($profits["daily"] as $date => $data) {
    if ($count_bots > 1) {
        $data["rel"] = $data["rel"] / $count_bots;
    }

    array_push($rows, [
        "date" => $date,
        "abs" => $data["abs"],
        "rel" => $data["rel"] * 100,
        "trade_count" => $data["trade_count"],
    ]);
}

save_strategy_summary_to_db($db, 'daily', $rows);

$rows = [];
foreach ($profits["weekly"] as $date => $data) {
    if ($count_bots > 1) {
        $data["rel"] = $data["rel"] / $count_bots;
    }

    array_push($rows, [
        "date" => $date,
        "abs" => $data["abs"],
        "rel" => $data["rel"] * 100,
        "trade_count" => $data["trade_count"],
    ]);
}

save_strategy_summary_to_db($db, 'weekly', $rows);

$rows = [];
foreach ($profits["monthly"] as $date => $data) {
    if ($count_bots > 1) {
        $data["rel"] = $data["rel"] / $count_bots;
    }

    array_push($rows, [
        "date" => $date,
        "abs" => $data["abs"],
        "rel" => $data["rel"] * 100,
        "trade_count" => $data["trade_count"],
    ]);
}

save_strategy_summary_to_db($db, 'monthly', $rows);

$rows = [];
foreach ($profits["daily_public"] as $date => $data) {
    array_push($rows, [
        "date" => $date,
        "abs" => $data["abs"],
        "rel" => $data["rel"] * 100,
        "trade_count" => $data["trade_count"],
    ]);
}

save_strategy_summary_to_db($db, 'daily_public', $rows);

$rows = [];
foreach ($profits["weekly_public"] as $date => $data) {
    array_push($rows, [
        "date" => $date,
        "abs" => $data["abs"],
        "rel" => $data["rel"] * 100,
        "trade_count" => $data["trade_count"],
    ]);
}

save_strategy_summary_to_db($db, 'weekly_public', $rows);

$rows = [];
foreach ($profits["monthly_public"] as $date => $data) {
    array_push($rows, [
        "date" => $date,
        "abs" => $data["abs"],
        "rel" => $data["rel"] * 100,
        "trade_count" => $data["trade_count"],
    ]);
}

save_strategy_summary_to_db($db, 'monthly_public', $rows);

save_strategy_summary_to_db($db, 'others', $array_others);

$label_sum = "sum_bots";
$summary_data = [
    "sum_initial" => $initial_balance_sum,
    "sum_profit" => $all_profit_sum,
    "sum_closed" => $closed_profit_sum,
    "sum_open" => $open_profit_sum
];

save_strategy_summary_to_db($db, $label_sum, $summary_data);

// End Clock Time in Seconds
$end_time = microtime(true);

// Calculate the Script Execution Time
$execution_time = ($end_time - $start_time);

echo "Script Execution Time = " . $execution_time . " sec";
