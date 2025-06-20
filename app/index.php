<?php

require_once('libraries/api.php');
require_once('../vendor/autoload.php');
require_once('libraries/date_helper.php');
require_once('libraries/db.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$start_0 = ($_ENV['START_0'] == 'true') ?: false;
$chart_min_trades = $_ENV['CHART_MIN_TRADES'] ?: 2;
if ($start_0) {
    $chart_min_trades++;
}

$max_closed_trades = intval($_ENV['MAX_CLOSED_TRADES_SHOWN']) ?: -1;

$path = '../bots.json';
$jsonString = file_get_contents($path);
$jsonData = json_decode($jsonString, true);

$current_time = new DateTime();
$data = [];

$db = new PostgreSqlDb();

for ($i = 0; $i < count($jsonData); $i++) {
    $ip_to_check = $jsonData[$i]['ip_address'];
    $data1 = $db->findById('strategies', $ip_to_check);

    if (is_null($data1)) {
        continue;
    }

    if (!is_array($data1['status'])) {
        $data1['status'] = json_decode($data1['status'], true);
    }

    $data[$data1['strategy']] = [
        "strategy" => $data1['strategy'],
        "profit" => $data1['profit'],
        "trades" => $data1['trades'] ?? [],
        "status" => $data1['status'] ?? [],
        "balance" => $data1['balance'] ?? [],
        "config" => $data1['config'] ?? [],
        "chart_profit_data" => $data1['chart_profit_data'] ?? []
    ];
}
ksort($data);

// $data_others = $db->findById('strategies', 'others');

?>

<html data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Unofficial FT Read-only Dashboard</title>
        <?php
        if (file_exists('assets/freqtrade.png')) {
            ?>
            <link rel="icon" type="image/ico" href="assets/freqtrade.png">
            <?php
        }

        require_once("assets/cdn.php");
        ?>
    </head>
    <body class="text-bg-dark">
        <div class="container">
            <div class="d-none d-md-block mb-4">
                <div>
                    <canvas id="chart1"></canvas>
                </div>
                <div class="d-flex flex-column">
                    <div>How to use</div>
                    <ul>
                        <li>When all data are shown, clicking on a legend above gonna hide all other data</li>
                        <li>When some data are hidden, clicking on legend will add/remove that data from the chart</li>
                        <li>Use this to compare performance of different bots</li>
                        <li>Since the bots trades at different time, some comparison might not look good</li>   
                    </ul>
                </div>
            </div>
            <div class="d-block d-md-none mb-3">
                <div class="d-flex justify-content-center">
                    <em>Combined profit chart is hidden on small screen.</em>
                </div>                
            </div>
            <div>
                <div class="d-flex justify-content-center">
                    <h5>Open Trades</h5>  (Look at each strategies' tab for more details)
                </div>
                <table class="table table-responsive table-striped" id="tableOpenTrades">
                    <thead>
                        <tr>
                            <td>Strategy</td>
                            <td>Trade ID</td>
                            <td>Dir</td>
                            <td>Pair</td>
                            <td>Open Since</td>
                            <td>Open Profit (%)</td>
                            <td>Closed Profit</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($data as $key => $value) {
                            if (count($value['status']) > 0) {
                                $date_now = new DateTime();
                                for ($i = 0; $i < count($value['status']); $i++) {
                                    $open_date = new DateTime($value['status'][$i]['open_date']);
                                    // $close_date = new DateTime($value['status'][$i]['close_date']);
                                    $total_profit = (
                                        floatval($value['status'][$i]['profit_abs']) +
                                        floatval($value['status'][$i]['realized_profit'])
                                    )
                                    ?>
                        <tr class="<?php echo ($total_profit > 0) ? 'table-success' : (($total_profit < 0) ? 'table-danger' : 'table-light'); ?>">
                            <td><?php echo $key; ?></td>
                            <td><?php echo $value['status'][$i]['trade_id']; ?></td>
                            <td><?php echo (intval($value['status'][$i]['is_short']) === 0 ? 'L' : 'S'); ?></td>
                            <td><?php echo $value['status'][$i]['pair']; ?></td>
                            <td><?php echo duration_string($open_date, $date_now); ?></td>
                            <td><?php echo round(floatval($value['status'][$i]['profit_abs']), 3) . ' ' . $value['status'][$i]['quote_currency'] . ' (' . round(floatval($value['status'][$i]['profit_ratio']) * 100, 3) . '%)'; ?></td>
                            <td><?php echo round(floatval($value['status'][$i]['realized_profit']), 3) . ' ' . $value['status'][$i]['quote_currency']; ?></td>
                        </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-5">
                <div class="d-flex justify-content-center"><h5>Performances Summary</h5></div>
                <table class="table table-responsive table-dark" id="tableSummary">
                    <thead>
                        <tr>
                            <td>Strategy</td>
                            <td>Exchange</td>
                            <td>Profit %</td>
                            <td>Profit Open</td>
                            <td>Exp</td>
                            <td>PF</td>
                            <td>DD</td>
                            <td>Trade #</td>
                            <td>Avg Duration</td>
                            <td>Win Rate</td>
                            <td>Days</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($data as $key => $value) {
                            ?>
                        <tr>
                            <td><?php echo $key; ?></td>
                            <td><?php echo "{$value['config']['exchange']} {$value['config']['trading_mode']}"; ?></td>
                            <td><?php echo round(floatval($value['profit']['profit_closed_ratio']) * 100, 3) . '%'; ?></td>
                            <td><?php echo round(floatval($value['profit']['profit_open_coin']), 3) . ' ' . $value['balance']['stake']; ?></td>
                            <td><?php echo round(floatval($value['profit']['expectancy']), 3) . ' (' . round(floatval($value['profit']['expectancy_ratio']), 3) . ')'; ?></td>
                            <td><?php echo round(floatval($value['profit']['profit_factor']), 3); ?></td>
                            <td><?php echo round(floatval($value['profit']['max_drawdown']) * 100, 3) . '%'; ?></td>
                            <td><?php echo $value['profit']['trade_count']; ?></td>
                            <td><?php echo $value['profit']['avg_duration']; ?></td>
                            <td><?php echo $value['profit']['winrate'] . '%'; ?></td>
                            <td><?php echo $value['profit']['days']; ?></td>
                        </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="accordion accordion-flush mt-4" id="mainAccordion">
                <?php
                foreach ($data as $key => $value) {
                    $sanitizedKey = str_replace([' ', '.', '#', '_'], [''], $key);
                    ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne<?php echo $sanitizedKey; ?>">
                      <button class="accordion-button collapsed text-bg-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne<?php echo $sanitizedKey; ?>" aria-expanded="false" aria-controls="collapseOne<?php echo $sanitizedKey; ?>">
                        Details for <?php echo $key; ?>
                      </button>
                    </h2>
                    <div id="collapseOne<?php echo $sanitizedKey; ?>" class="accordion-collapse collapse border border-secondary" aria-labelledby="headingOne<?php echo $sanitizedKey; ?>" data-bs-parent="#mainAccordion">
                        <div class="accordion-body text-bg-dark">

                            <div class="accordion accordion-flush mt-4" id="mainAccordion<?php echo $sanitizedKey; ?>">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingConfig<?php echo $sanitizedKey; ?>">
                                      <button class="accordion-button collapsed text-bg-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConfig<?php echo $sanitizedKey; ?>" aria-expanded="false" aria-controls="collapseConfig<?php echo $sanitizedKey; ?>">
                                        Config for <?php echo $key; ?>
                                      </button>
                                    </h2>
                                    <div id="collapseConfig<?php echo $sanitizedKey; ?>" class="accordion-collapse collapse border border-secondary" aria-labelledby="headingConfig<?php echo $sanitizedKey; ?>" data-bs-parent="#mainAccordion<?php echo $sanitizedKey; ?>">
                                      <div class="accordion-body text-bg-dark">
                                        <p class="text-white">Exchange (Market): <?php echo "{$value['config']['exchange']} ({$value['config']['trading_mode']})"; ?></p>
                                        <p class="text-white">Starting balance: <?php echo $value['balance']['starting_capital'] . ' ' . $value['balance']['stake']; ?></p>
                                        <p class="text-white">Max open trades: <?php echo $value['config']['max_open_trades']; ?></p>
                                        <p class="text-white"><?php echo "{$value['config']['order_types']['entry']} Entry, {$value['config']['order_types']['exit']} Exit"; ?></p>
                                      </div>
                                    </div>
                                </div>
                                <?php

                                if (intval($value['profit']['trade_count']) > 0) {
                                    $profit_closed = floatval($value['profit']['profit_closed_coin']);
                                    $profit_open = floatval($value['profit']['profit_open_coin']);
                                    $profit_open_pct = round($profit_open * 100 / floatval($value['balance']['starting_capital']), 3);
                                    ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSummary<?php echo $sanitizedKey; ?>">
                                      <button class="accordion-button collapsed text-bg-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSummary<?php echo $sanitizedKey; ?>" aria-expanded="false" aria-controls="collapseSummary<?php echo $sanitizedKey; ?>">
                                        Profit Summary for <?php echo $key; ?>
                                      </button>
                                    </h2>
                                    <div id="collapseSummary<?php echo $sanitizedKey; ?>" class="accordion-collapse collapse border border-secondary" aria-labelledby="headingSummary<?php echo $sanitizedKey; ?>" data-bs-parent="#mainAccordion<?php echo $sanitizedKey; ?>">
                                      <div class="accordion-body text-bg-dark">
                                        <p class="text-white">Total trades: <?php echo $value['profit']['trade_count'] ?> trades</p>
                                        <p class="<?php echo ($profit_closed > 0) ? 'text-success' : (($profit_closed < 0) ? 'text-danger' : 'text-white'); ?>">Profit Closed: <?php echo round($profit_closed, 3) . ' ' . $value['balance']['stake'] . ' (' . $value['profit']['profit_closed_percent'] . '%)'; ?></p>
                                        <p class="<?php echo ($profit_open > 0) ? 'text-success' : (($profit_open < 0) ? 'text-danger' : 'text-white'); ?>">Profit Open: <?php echo round($profit_open, 3) . " {$value['balance']['stake']} ({$profit_open_pct}%)"; ?></p>
                                        <p class="text-white">Expectancy (Ratio): <?php echo round(floatval($value['profit']['expectancy']), 3) . ' (' . round(floatval($value['profit']['expectancy_ratio']), 3) . ')' ?></p>
                                        <p class="text-white">Profit Factor: <?php echo round(floatval($value['profit']['profit_factor']), 3) ?></p>
                                        <p class="text-white">Max DD: <?php echo round(floatval($value['profit']['max_drawdown']) * 100, 3) ?> %</p>
                                      </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTableOpen<?php echo $sanitizedKey; ?>">
                                      <button class="accordion-button collapsed text-bg-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTableOpen<?php echo $sanitizedKey; ?>" aria-expanded="false" aria-controls="collapseTableOpen<?php echo $sanitizedKey; ?>">
                                        <?php echo ($max_closed_trades > 0) ? "Last {$max_closed_trades} " : ""; ?>Closed Trades for <?php echo $key; ?>
                                      </button>
                                    </h2>
                                    <div id="collapseTableOpen<?php echo $sanitizedKey; ?>" class="accordion-collapse collapse border border-secondary accordion-collapse-tradeClosed" aria-labelledby="headingTableOpen<?php echo $sanitizedKey; ?>" data-bs-parent="#mainAccordion<?php echo $sanitizedKey; ?>" data-table-closed="tableClosed<?php echo $sanitizedKey; ?>">
                                      <div class="accordion-body text-bg-dark overflow-auto">
                                        <table class="table-secondary table table-responsive tableClosed" id="tableClosed<?php echo $sanitizedKey; ?>">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Pair</th>
                                                    <th>Enter Tag</th>
                                                    <th>Exit Reason</th>
                                                    <th>Profit</th>
                                                    <th>Trade Duration</th>
                                                    <th>Closed</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $count_print = 0;
                                            $date_now = new DateTime();
                                            for ($i = 0; $i < count($value['trades']); $i++) {
                                                if (intval($value['trades'][$i]['is_open']) > 0) {
                                                    continue;
                                                }

                                                if (($max_closed_trades < 0) || ($count_print < $max_closed_trades)) {
                                                    $count_print++;
                                                } else {
                                                    break;
                                                }

                                                $open_date = new DateTime($value['trades'][$i]['open_date']);
                                                $close_date = new DateTime($value['trades'][$i]['close_date']);

                                                ?>
                                                <tr class="<?php echo (floatval($value['trades'][$i]['close_profit_abs']) >= 0) ? 'table-success' : 'table-danger'; ?>">
                                                    <td><?php echo $value['trades'][$i]['trade_id']; ?></td>
                                                    <td><?php echo $value['trades'][$i]['pair']; ?></td>
                                                    <td><?php echo $value['trades'][$i]['enter_tag']; ?></td>
                                                    <td><?php echo $value['trades'][$i]['exit_reason']; ?></td>
                                                    <td><?php echo round(floatval($value['trades'][$i]['close_profit_abs']), 3) . ' ' . $value['trades'][$i]['quote_currency'] . ' (' . round(floatval($value['trades'][$i]['close_profit']) * 100, 3) . '%)'; ?></td>
                                                    <td><?php echo duration_string($open_date, $close_date); ?></td>
                                                    <td><?php echo duration_string($close_date, $date_now) . ' ago'; ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                      </div>
                                    </div>
                                </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            if (count($value['status']) > 0) {
                                ?>
                            <div class="d-flex flex-row justify-content-center mt-3">
                                <h3><strong>Open Trades for <?php echo $key; ?></strong> (Click the row for more detail)</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-secondary accordion" id="tableOpen">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Dir</th>
                                            <th>Pair</th>
                                            <th>Current Stake</th>
                                            <th>Enter Tag</th>
                                            <th>Open Since</th>
                                            <th>Open Profit</th>
                                            <th>Closed Profit</th>
                                            <th>Open Rate</th>
                                            <th>Current Rate</th>
                                            <th>Min Rate</th>
                                            <th>Max Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $date_now = new DateTime();
                                        for ($i = 0; $i < count($value['status']); $i++) {
                                            $min_profit = round(floatval($value['status'][$i]['min_profit']) * 100, 3);
                                            $max_profit = round(floatval($value['status'][$i]['max_profit']) * 100, 3);

                                            $open_date = new DateTime($value['status'][$i]['open_date']);
                                            // $close_date = new DateTime($value['status'][$i]['close_date']);
                                            $total_profit = (floatval($value['status'][$i]['profit_abs']) + floatval($value['status'][$i]['realized_profit']))

                                            ?>
                                        <tr class="<?php echo ($total_profit > 0) ? 'table-success' : (($total_profit < 0) ? 'table-danger' : 'table-light'); ?>" data-bs-toggle="collapse" data-bs-target="#trade<?php echo $value['status'][$i]['trade_id']; ?>">
                                            <td><?php echo $value['status'][$i]['trade_id']; ?></td>
                                            <td><?php echo (intval($value['status'][$i]['is_short']) === 0 ? 'L' : 'S'); ?></td>
                                            <td><?php echo $value['status'][$i]['pair']; ?></td>
                                            <td><?php echo round(floatval($value['status'][$i]['stake_amount']), 3) . ' ' . $value['status'][$i]['quote_currency']; ?></td>
                                            <td><?php echo $value['status'][$i]['enter_tag'] ?: ''; ?></td>
                                            <td><?php echo duration_string($open_date, $date_now); ?></td>
                                            <td><?php echo round(floatval($value['status'][$i]['profit_abs']), 3) . ' ' . $value['status'][$i]['quote_currency'] . ' (' . round(floatval($value['status'][$i]['profit_ratio']) * 100, 3) . '%)'; ?></td>
                                            <td><?php echo round(floatval($value['status'][$i]['realized_profit']), 3) . ' ' . $value['status'][$i]['quote_currency']; ?></td>
                                            <td><?php echo $value['status'][$i]['open_rate']; ?></td>
                                            <td><?php echo $value['status'][$i]['current_rate']; ?></td>
                                            <td><?php echo $value['status'][$i]['min_rate'] . ' (' . $min_profit . '%)'; ?></td>
                                            <td><?php echo $value['status'][$i]['max_rate'] . ' (' . $max_profit . '%)'; ?></td>
                                        </tr>
                                        <tr class="collapse accordion-collapse" id="trade<?php echo $value['status'][$i]['trade_id']; ?>" data-bs-parent="#tableOpen">
                                            <td class="table-secondary" colspan="12">
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex justify-content-center"><u>Detail for trade #<?php echo $value['status'][$i]['trade_id']; ?></u></div>
                                                    <table class="table table-secondary">
                                                        <thead>
                                                            <tr>
                                                                <th>Action</th>
                                                                <th>Rate</th>
                                                                <th>Coin Amount</th>
                                                                <th>Stake Amount</th>
                                                                <th>Filled</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-group-divider">
                                                            <?php
                                                            $orders = $value['status'][$i]['orders'];
                                                            $coin = $value['status'][$i]['base_currency'];
                                                            for ($j = 0; $j < count($orders); $j++) {
                                                                $filled_date = new DateTime('@' . (intval($orders[$j]['order_filled_timestamp']) / 1000));
                                                                ?>
                                                            <tr>
                                                                <td><?php echo $orders[$j]['ft_order_side']; ?></td>
                                                                <td><?php echo $orders[$j]['safe_price']; ?></td>
                                                                <td><?php echo $orders[$j]['filled'] . ' ' . $coin; ?></td>
                                                                <td><?php echo $orders[$j]['cost'] . ' ' . $value['status'][$i]['quote_currency']; ?></td>
                                                                <td><?php echo duration_string($filled_date, $date_now); ?></td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                                <?php
                            } else {
                                ?>
                            <div class="d-flex justify-content-center mt-3">
                                <div>No open trade currently</div>
                            </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                    <?php
                }
                ?>
            </div>

        </div>
        <script type="text/javascript">
            var chart1, chart2, chart3
            let table = new DataTable('#tableSummary', {
                columnDefs: [
                    {
                        targets: [2, 3, 4, 5, 6, 7, 8, 9, 10],
                        className: 'dt-body-right'
                    },
                    { className: 'dt-head-center', targets: '_all' }
                ],
                drawCallback: table_drawCallback,
                fixedColumns: true,
                scrollX: true,
                scrollY: '400px',
                scrollCollapse: true
            })

            let tableOpenTrades = new DataTable('#tableOpenTrades', {
                columnDefs: [
                    {
                        targets: [1, 5, 6],
                        className: 'dt-body-right'
                    },
                    { className: 'dt-head-center', targets: '_all' }
                ],
                drawCallback: table_drawCallback,
                scrollX: true,
                scrollY: '400px',
                scrollCollapse: true
            })

            $('.accordion-collapse-tradeClosed').on('shown.bs.collapse', function(){
                id_table = this.dataset.tableClosed
                if (!$.fn.dataTable.isDataTable('#'+id_table) ) {
                    $('#'+id_table).DataTable({
                        columnDefs: [
                            { className: 'dt-head-center', targets: '_all' }
                        ],
                        drawCallback: table_drawCallback,
                        ordering: false,
                        scrollX: true,
                        scrollY: '400px',
                        scrollCollapse: true,
                        searching: false,
                    })
                }
                
            })
            
            $(document).ready(function(){
                draw_chart()
            })

            $(window).resize(function(){
                draw_chart()
            })

            function draw_chart() {
                if($('#chart1').is(":visible")) {
                    if (typeof chart1 === 'undefined') {
                        var ctx1 = document.getElementById('chart1');
                        chart1 = new r2bot_line_chart(ctx1, get_config_profit(), false);    
                    }
                }
            }

            function get_config_profit () {
                dataset = []

                <?php
                $count = 0;
                foreach ($data as $key => $value) {
                    if (count($value['chart_profit_data']) < $chart_min_trades) {
                        continue;
                    }
                    ?>

                data = JSON.parse('<?php echo json_encode($value['chart_profit_data']); ?>')
                dataset.push({
                    label:'<?php echo $value['strategy']; ?>',
                    data: data.map(item => ({ close_date: new Date(item.close_date), close_profit: item.close_profit, close_profit_abs: item.close_profit_abs, is_open: item.is_open })),
                    pointRadius: 1,
                    borderWidth: 1,
                    segment: {
                        borderDash: function(context) {
                            return (context.p1.raw.is_open == 1) ? [5, 5] : [];
                        },
                    },
                    stepped: true,
                })

                    <?php
                }
                ?>

                return {
                    type: 'line',
                    data: {
                        datasets: dataset
                    },
                    options:{
                        parsing: {
                            xAxisKey: 'close_date',
                            yAxisKey: 'close_profit'
                        },
                        elements: {
                            point: {
                                radius: 0,
                                pointStyle: 'none',
                            },
                        },
                        interaction: {
                          mode: 'nearest',
                          intersect: false,
                          axis:'x',
                        },
                        plugins:{
                            title:{
                                display:true,
                                font:{
                                    weight: 'bold',
                                    size: 16
                                },
                                text:'Profit',
                                position:'top'
                            },
                            legend:{
                                display: true,
                            },
                            tooltip:{
                                callbacks:{
                                    title:function(context) {
                                        var date = new Date(context[0].label);
                                        return date.toLocaleDateString('en-EN',{dateStyle:'long'});
                                    },
                                    label:function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }

                                        var index = context.dataIndex;
                                        if (context.parsed.y !== null) {
                                            percent = context.parsed.y.toPrecision(3);
                                            profit = context.dataset.data[index].close_profit_abs.toPrecision(3);
                                            label += `${percent}% (${profit})`;
                                        }
                                        
                                        return label;
                                    }
                                }
                            }
                        },
                        scales:{
                            x:{
                                type: 'time',
                                time: {
                                    unit: 'day'
                                },
                                grid:{
                                    display: false,
                                },
                            },
                            y:{
                                display:true,
                                grid:{
                                    color: '#a7a7a7',
                                    drawTicks: false,
                                },
                                position: 'left',
                                title:{
                                    display: true,
                                    text: 'Profit %',
                                },
                                type:'linear'
                            },
                        }
                    }
                };
            }

        </script>
        <div class="container">
            <footer>
                <div class="d-flex flex-row justify-content-center text-white">
                    <span style="padding: 0;">Created by <a href="https://github.com/stash86">stash86</a>. Powered by Freqtrade</span>
                </div>
            </footer>
        </div>
        
    </body>
</html>
