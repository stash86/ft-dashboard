<?php // phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

require_once __DIR__ . '/../../vendor/autoload.php';

class Api
{
    private $ip_address = '';
    private $username = '';
    private $password = '';
    private $httpClient;

    public function __construct(string $ip_address, string $username, string $password)
    {
        $this->ip_address = $ip_address;
        $this->username = $username;
        $this->password = $password;
        $this->httpClient = new GuzzleHttp\Client([
            'timeout' => 10.0, // Example: Set a default timeout
            'http_errors' => false, // Set to false to handle HTTP errors manually via status code
        ]);
    }

    public function balance(string $fiat_display = "")
    {
        // {
        //  "currencies":[
        //      {
        //          "currency":"BNB",
        //          "free":0.02,
        //          "balance":0.02,
        //          "used":0.0,
        //          "bot_owned":0.0,
        //          "est_stake":13.256,
        //          "est_stake_bot":0.0,
        //          "stake":"USDT",
        //          "side":"long",
        //          "is_position":false,
        //          "position":0.0,
        //          "is_bot_managed":false
        //      },{
        //          "currency":"USDT",
        //          "free":1000.54,
        //          "balance":1000.54,
        //          "used":0.0,
        //          "bot_owned":299.74501978999996,
        //          "est_stake":1000.54,
        //          "est_stake_bot":299.74501978999996,
        //          "stake":"USDT",
        //          "side":"long",
        //          "is_position":false,
        //          "position":0.0,
        //          "is_bot_managed":true
        //      }
        //  ],
        //  "total":1013.7959999999999,
        //  "total_bot":299.74501978999996,
        //  "symbol":"USD",
        //  "value":1013.7959999999999,
        //  "value_bot":299.74501978999996,
        //  "stake":"USDT",
        //  "note":"",
        //  "starting_capital":250.0,
        //  "starting_capital_ratio":0.19898007915999982,
        //  "starting_capital_pct":19.9,
        //  "starting_capital_fiat":250.0,
        //  "starting_capital_fiat_ratio":0.19898007915999982,
        //  "starting_capital_fiat_pct":19.9
        // }
        $params = array();
        if (!empty($fiat_display)) {
            $params['fiat_display_currency'] = $fiat_display;
        }
        return $this->call('GET', 'balance', $params);
    }

    public function blacklist(string $coin = '', string $stake = '.*')
    {
        // {
        //  "blacklist":[".*(_PREMIUM|BEAR|BULL|DOWN|HALF|HEDGE|UP|[1235][SL]|2021)/.*",".*(USDT_|USDT-).*","(AUD|BRZ|CAD|CHF|EUR|GBP|HKD|IDRT|JPY|NGN|RUB|SGD|TRY|UAH|USD|ZAR)/.*","(BUSD|CUSD|CUSDT|DAI|PAX|PAXG|SUSD|TUSD|USDC|USDN|USDP|USDT|UST|VAI|USDD|USDJ)/.*","(ACM|AFA|ALA|ALL|ALPINE|APL|ASR|ATM|BAR|CAI|CITY|FOR|GAL|GOZ|IBFK|JUV|LAZIO|LEG|LOCK-1|NAVI|NMR|NOV|OG|PFL|PORTO|PSG|ROUSH|SANTOS|STV|TH|TRA|UCH|UFC|YBO)/.*"],
        //  "blacklist_expanded":["BULL/USDT","BULL/BUSD","BEAR/USDT","BEAR/BUSD","ETHBULL/USDT","ETHBULL/BUSD","ETHBEAR/USDT","ETHBEAR/BUSD","EOSBULL/USDT","EOSBULL/BUSD","EOSBEAR/USDT","EOSBEAR/BUSD","XRPBULL/USDT","XRPBULL/BUSD","XRPBEAR/USDT","XRPBEAR/BUSD","BNBBULL/USDT","BNBBULL/BUSD","BNBBEAR/USDT","BNBBEAR/BUSD","BTCUP/USDT","BTCDOWN/USDT","ETHUP/USDT","ETHDOWN/USDT","ADAUP/USDT","ADADOWN/USDT","LINKUP/USDT","LINKDOWN/USDT","BNBUP/USDT","BNBDOWN/USDT","XTZUP/USDT","XTZDOWN/USDT","EOSUP/USDT","EOSDOWN/USDT","TRXUP/USDT","TRXDOWN/USDT","XRPUP/USDT","XRPDOWN/USDT","DOTUP/USDT","DOTDOWN/USDT","LTCUP/USDT","LTCDOWN/USDT","UNIUP/USDT","UNIDOWN/USDT","SXPUP/USDT","SXPDOWN/USDT","FILUP/USDT","FILDOWN/USDT","YFIUP/USDT","YFIDOWN/USDT","BCHUP/USDT","BCHDOWN/USDT","AAVEUP/USDT","AAVEDOWN/USDT","SUSHIUP/USDT","SUSHIDOWN/USDT","XLMUP/USDT","XLMDOWN/USDT","1INCHUP/USDT","1INCHDOWN/USDT","EUR/BUSD","EUR/USDT","GBP/BUSD","GBP/USDT","AUD/BUSD","AUD/USDT","AUD/USDC","TUSD/BTC","TUSD/ETH","TUSD/BNB","TUSD/USDT","PAX/BTC","PAX/BNB","PAX/USDT","PAX/ETH","USDC/BNB","USDC/USDT","PAX/TUSD","USDC/TUSD","USDC/PAX","BUSD/USDT","BUSD/NGN","BUSD/RUB","BUSD/TRY","USDT/TRY","USDT/RUB","USDT/ZAR","BUSD/ZAR","USDT/IDRT","BUSD/IDRT","USDT/UAH","BUSD/BIDR","USDT/BIDR","DAI/BNB","DAI/BTC","DAI/USDT","DAI/BUSD","USDT/BKRW","BUSD/BKRW","USDT/DAI","BUSD/DAI","PAXG/BNB","PAXG/BTC","PAXG/BUSD","PAXG/USDT","USDT/NGN","USDT/BRL","BUSD/BRL","SUSD/BTC","SUSD/ETH","SUSD/USDT","BUSD/BVND","USDT/BVND","USDC/BUSD","TUSD/BUSD","PAX/BUSD","BUSD/VAI","BUSD/UAH","USDP/BUSD","USDP/USDT","UST/BTC","UST/BUSD","UST/USDT","BUSD/PLN","BUSD/RON","NMR/BTC","NMR/BUSD","NMR/USDT","FOR/BTC","FOR/BUSD","JUV/BTC","JUV/BUSD","JUV/USDT","PSG/BTC","PSG/BUSD","PSG/USDT","OG/BTC","OG/USDT","ATM/BTC","ATM/USDT","ASR/BTC","ASR/USDT","ACM/BTC","ACM/BUSD","ACM/USDT","BAR/BTC","BAR/BUSD","BAR/USDT","ATM/BUSD","FOR/USDT","LAZIO/TRY","LAZIO/EUR","LAZIO/BTC","LAZIO/USDT","LAZIO/BUSD","CITY/BTC","CITY/BNB","CITY/BUSD","CITY/USDT","PORTO/BTC","PORTO/USDT","PORTO/TRY","PORTO/EUR","SANTOS/BTC","SANTOS/USDT","SANTOS/BRL","SANTOS/TRY","FOR/BNB","ALPINE/EUR","ALPINE/TRY","ALPINE/USDT","ALPINE/BTC","ALPINE/BUSD","SANTOS/BUSD","PORTO/BUSD","GAL/USDT","GAL/BUSD","GAL/BNB","GAL/BTC","GAL/EUR","GAL/TRY","GAL/ETH","GAL/BRL","OG/BUSD","ASR/BUSD"],
        //  "errors":{},
        //  "length":5,
        //  "method":["VolumePairList","AgeFilter"]}

        if (!empty($coin)) {
            $params = array();
            $params['blacklist'] = ["$coin/$stake"];
            return $this->call('POST', 'blacklist', $params);
        } else {
            return $this->call('GET', 'blacklist');
        }
    }

    public function call($method = 'GET', $command = 'ping', $data = null)
    {
        $url = "http://{$this->ip_address}/api/v1/{$command}";
        $method = strtoupper($method);

        $options = [
            'auth' => [$this->username, $this->password] // Basic Authentication
        ];

        // Add data based on method
        switch ($method) {
            case "POST":
            case "DELETE":
            case "PUT": // Added PUT here as well
                if (!empty($data)) {
                    // Send data as JSON payload
                    $options['json'] = $data;
                }
                break;
            default: // Primarily GET
                if (!empty($data)) {
                    // Send data as query parameters
                    $options['query'] = $data;
                }
        }

        try {
            // Make the request using the Guzzle client
            $response = $this->httpClient->request($method, $url, $options);

            $statusCode = $response->getStatusCode();

            // Check for successful status codes (2xx)
            if ($statusCode >= 200 && $statusCode < 300) {
                return $response->getBody()->getContents(); // Return response body on success
            } else {
                // Log or handle non-successful HTTP status codes
                error_log("Freqtrade API call failed: Method={$method}, URL={$url}, Status={$statusCode}, Body=" .
                    $response->getBody()->getContents());
                // Optionally return the error body or a specific error indicator
                return $response->getBody()->getContents(); // Or return null, or throw an exception
            }
        } catch (GuzzleHttp\Exception\ConnectException $e) {
            // Handle connection errors (e.g., host unreachable)
            error_log("Freqtrade API connection error: " . $e->getMessage());
            return null; // Return null or indicate connection failure
        } catch (GuzzleHttp\Exception\RequestException $e) {
            // Handle other request errors (includes errors when http_errors is true, but useful for logging context)
            error_log("Freqtrade API request error: " . $e->getMessage());
            if ($e->hasResponse()) {
                error_log("Response Body: " . $e->getResponse()->getBody()->getContents());
            }
            return null; // Return null or indicate request failure
        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            // Catch any other Guzzle exceptions
            error_log("Freqtrade API general Guzzle error: " . $e->getMessage());
            return null; // Return null or indicate general failure
        }
    }

    public function callPost($command)
    {
        return $this->call('POST', $command);
    }

    public function cancelOpenOrder($trade_id = 0)
    {
        return $this->call('DELETE', "trades/$trade_id/open-order");
    }

    public function count()
    {
        // {"current":0,"max":0,"total_stake":0.0}
        return $this->call('GET', 'count');
    }

    public function daily($days = 7)
    {
        // {
        //   "data":[
        //     {
        //       "date":"2022-09-29",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.3167557951,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     },
        //     {
        //       "date":"2022-09-28",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.3167557951,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     },
        //     {
        //       "date":"2022-09-27",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.3167557951,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     },
        //     {
        //       "date":"2022-09-26",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.3167557951,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     },
        //     {
        //       "date":"2022-09-25",
        //       "abs_profit":0.31243412,
        //       "rel_profit":0.0027648,
        //       "starting_balance":113.00432167509999,
        //       "fiat_value":0.31243412,
        //       "trade_count":1
        //     },
        //     {
        //       "date":"2022-09-24",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.00432167509999,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     },
        //     {
        //       "date":"2022-09-23",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.00432167509999,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     }
        //   ],
        //   "fiat_display_currency":"USD",
        //   "stake_currency":"USDT"
        // }
        return $this->call('GET', 'daily', array('timescale' => $days));
    }

    // Not working
    public function deleteBlacklist($coin = '', $stake = '.*')
    {
        // {
        //  "blacklist":[".*(_PREMIUM|BEAR|BULL|DOWN|HALF|HEDGE|UP|[1235][SL]|2021)/.*",".*(USDT_|USDT-).*","(AUD|BRZ|CAD|CHF|EUR|GBP|HKD|IDRT|JPY|NGN|RUB|SGD|TRY|UAH|USD|ZAR)/.*","(BUSD|CUSD|CUSDT|DAI|PAX|PAXG|SUSD|TUSD|USDC|USDN|USDP|USDT|UST|VAI|USDD|USDJ)/.*","(ACM|AFA|ALA|ALL|ALPINE|APL|ASR|ATM|BAR|CAI|CITY|FOR|GAL|GOZ|IBFK|JUV|LAZIO|LEG|LOCK-1|NAVI|NMR|NOV|OG|PFL|PORTO|PSG|ROUSH|SANTOS|STV|TH|TRA|UCH|UFC|YBO)/.*"],
        //  "blacklist_expanded":["BULL/USDT","BULL/BUSD","BEAR/USDT","BEAR/BUSD","ETHBULL/USDT","ETHBULL/BUSD","ETHBEAR/USDT","ETHBEAR/BUSD","EOSBULL/USDT","EOSBULL/BUSD","EOSBEAR/USDT","EOSBEAR/BUSD","XRPBULL/USDT","XRPBULL/BUSD","XRPBEAR/USDT","XRPBEAR/BUSD","BNBBULL/USDT","BNBBULL/BUSD","BNBBEAR/USDT","BNBBEAR/BUSD","BTCUP/USDT","BTCDOWN/USDT","ETHUP/USDT","ETHDOWN/USDT","ADAUP/USDT","ADADOWN/USDT","LINKUP/USDT","LINKDOWN/USDT","BNBUP/USDT","BNBDOWN/USDT","XTZUP/USDT","XTZDOWN/USDT","EOSUP/USDT","EOSDOWN/USDT","TRXUP/USDT","TRXDOWN/USDT","XRPUP/USDT","XRPDOWN/USDT","DOTUP/USDT","DOTDOWN/USDT","LTCUP/USDT","LTCDOWN/USDT","UNIUP/USDT","UNIDOWN/USDT","SXPUP/USDT","SXPDOWN/USDT","FILUP/USDT","FILDOWN/USDT","YFIUP/USDT","YFIDOWN/USDT","BCHUP/USDT","BCHDOWN/USDT","AAVEUP/USDT","AAVEDOWN/USDT","SUSHIUP/USDT","SUSHIDOWN/USDT","XLMUP/USDT","XLMDOWN/USDT","1INCHUP/USDT","1INCHDOWN/USDT","EUR/BUSD","EUR/USDT","GBP/BUSD","GBP/USDT","AUD/BUSD","AUD/USDT","AUD/USDC","TUSD/BTC","TUSD/ETH","TUSD/BNB","TUSD/USDT","PAX/BTC","PAX/BNB","PAX/USDT","PAX/ETH","USDC/BNB","USDC/USDT","PAX/TUSD","USDC/TUSD","USDC/PAX","BUSD/USDT","BUSD/NGN","BUSD/RUB","BUSD/TRY","USDT/TRY","USDT/RUB","USDT/ZAR","BUSD/ZAR","USDT/IDRT","BUSD/IDRT","USDT/UAH","BUSD/BIDR","USDT/BIDR","DAI/BNB","DAI/BTC","DAI/USDT","DAI/BUSD","USDT/BKRW","BUSD/BKRW","USDT/DAI","BUSD/DAI","PAXG/BNB","PAXG/BTC","PAXG/BUSD","PAXG/USDT","USDT/NGN","USDT/BRL","BUSD/BRL","SUSD/BTC","SUSD/ETH","SUSD/USDT","BUSD/BVND","USDT/BVND","USDC/BUSD","TUSD/BUSD","PAX/BUSD","BUSD/VAI","BUSD/UAH","USDP/BUSD","USDP/USDT","UST/BTC","UST/BUSD","UST/USDT","BUSD/PLN","BUSD/RON","NMR/BTC","NMR/BUSD","NMR/USDT","FOR/BTC","FOR/BUSD","JUV/BTC","JUV/BUSD","JUV/USDT","PSG/BTC","PSG/BUSD","PSG/USDT","OG/BTC","OG/USDT","ATM/BTC","ATM/USDT","ASR/BTC","ASR/USDT","ACM/BTC","ACM/BUSD","ACM/USDT","BAR/BTC","BAR/BUSD","BAR/USDT","ATM/BUSD","FOR/USDT","LAZIO/TRY","LAZIO/EUR","LAZIO/BTC","LAZIO/USDT","LAZIO/BUSD","CITY/BTC","CITY/BNB","CITY/BUSD","CITY/USDT","PORTO/BTC","PORTO/USDT","PORTO/TRY","PORTO/EUR","SANTOS/BTC","SANTOS/USDT","SANTOS/BRL","SANTOS/TRY","FOR/BNB","ALPINE/EUR","ALPINE/TRY","ALPINE/USDT","ALPINE/BTC","ALPINE/BUSD","SANTOS/BUSD","PORTO/BUSD","GAL/USDT","GAL/BUSD","GAL/BNB","GAL/BTC","GAL/EUR","GAL/TRY","GAL/ETH","GAL/BRL","OG/BUSD","ASR/BUSD"],
        //  "errors":{},
        //  "length":5,
        //  "method":["VolumePairList","AgeFilter"]}

        if (!empty($coin)) {
            $params = array();
            $params['pairs_to_delete'] = ["$coin/$stake"];

            return $this->call('DELETE', 'blacklist', $params);
        } else {
            return "Please provide pair(s) to be removed from blacklist.";
        }
    }

    public function deleteLock($lockid = 0)
    {
        return $this->call('DELETE', "locks/$lockid");
    }

    public function deleteLockPair($coin = '', $stake = '', $lockid = 0)
    {
        $params = array();
        if (!empty($coin)) {
            $params['pair'] = "$coin/$stake";
        }

        if (intval($lockid) > 0) {
            $params['lockid'] = intval($lockid);
        }

        return $this->call('POST', "locks/delete", $params);
    }

    public function deleteTrade($trade_id = 0)
    {
        // {"cancel_order_count":0,"result":"success",
        //  "result_msg":"Deleted trade 6. Closed 0 open orders.","trade_id":6}
        return $this->call('DELETE', "trades/$trade_id");
    }

    public function entries()
    {
        // [
        //   {
        //     "enter_tag":"buy_1",
        //     "profit_ratio":1.1656149046360789,
        //     "profit_pct":116.56,
        //     "profit_abs":22.973684720000016,
        //     "count":221
        //   }
        // ]
        return $this->call('GET', 'entries');
    }

    public function exits()
    {
        // [
        //   {
        //     "exit_reason":"fastk_profit_sell",
        //     "profit_ratio":3.1951696219200207,
        //     "profit_pct":319.52,
        //     "profit_abs":66.42404872000002,
        //     "count":185
        //   },
        //   {
        //     "exit_reason":"fastk_profit_sell_delay",
        //     "profit_ratio":0.09307955654235048,
        //     "profit_pct":9.31,
        //     "profit_abs":1.95517928,
        //     "count":11
        //   },
        //   {
        //     "exit_reason":"stop_loss",
        //     "profit_ratio":-0.3689323954583724,
        //     "profit_pct":-36.89,
        //     "profit_abs":-7.79176693,
        //     "count":2
        //   },
        //   {
        //     "exit_reason":"fastk_loss_sell",
        //     "profit_ratio":-1.7537018783679206,
        //     "profit_pct":-175.37,
        //     "profit_abs":-37.61377635,
        //     "count":23
        //   }
        // ]
        return $this->call('GET', 'exits');
    }

    public function forceenter($coin = '', $stake = '', $direction = 'long', $rate = 0)
    {
        // {
        //   "trade_id":6,
        //   "pair":"MASK/BUSD",
        //   "base_currency":"MASK",
        //   "quote_currency":"BUSD",
        //   "is_open":true,
        //   "is_short":false,
        //   "exchange":"binance",
        //   "amount":3.2,
        //   "amount_requested":3.25263617,
        //   "stake_amount":19.6736,
        //   "max_stake_amount":19.6736,
        //   "strategy":"Cenderawasih_30m_prod2",
        //   "enter_tag":"force_entry",
        //   "timeframe":30,
        //   "fee_open":0.001,
        //   "fee_open_cost":0.0196736,
        //   "fee_open_currency":"BUSD",
        //   "fee_close":0.001,
        //   "fee_close_cost":null,
        //   "fee_close_currency":null,
        //   "open_date":"2023-03-24 08:09:06",
        //   "open_timestamp":1679645346982,
        //   "open_rate":6.148,
        //   "open_rate_requested":6.146,
        //   "open_trade_value":19.6932736,
        //   "close_date":null,
        //   "close_timestamp":null,
        //   "close_rate":null,
        //   "close_rate_requested":null,
        //   "close_profit":null,
        //   "close_profit_pct":null,
        //   "close_profit_abs":null,
        //   "profit_ratio":null,
        //   "profit_pct":null,
        //   "profit_abs":null,
        //   "profit_fiat":null,
        //   "realized_profit":0.0,
        //   "realized_profit_ratio":null,
        //   "exit_reason":null,
        //   "exit_order_status":null,
        //   "stop_loss_abs":0.062,
        //   "stop_loss_ratio":-0.99,
        //   "stop_loss_pct":-99.0,
        //   "stoploss_order_id":null,
        //   "stoploss_last_update":null,
        //   "stoploss_last_update_timestamp":null,
        //   "initial_stop_loss_abs":0.062,
        //   "initial_stop_loss_ratio":-0.99,
        //   "initial_stop_loss_pct":-99.0,
        //   "min_rate":null,
        //   "max_rate":0.0,
        //   "open_order_id":null,
        //   "orders":[
        //     {
        //       "pair":"MASK/BUSD",
        //       "order_id":"dry_run_buy_1679645346.979509",
        //       "status":"closed",
        //       "remaining":0.0,
        //       "amount":3.2,
        //       "safe_price":6.148,
        //       "cost":19.6736,
        //       "filled":3.2,
        //       "ft_order_side":"buy",
        //       "order_type":"market",
        //       "is_open":false,
        //       "order_timestamp":1679645346000,
        //       "order_filled_timestamp":1679645346982
        //     }
        //   ],
        //   "leverage":1.0,
        //   "interest_rate":0.0,
        //   "liquidation_price":null,
        //   "funding_fees":0.0,
        //   "trading_mode":"spot"
        // }
        if (!empty($coin)) {
            $params = array();
            $params['pair'] = "$coin/$stake";
            $params['side'] = $direction;
            if ($rate > 0) {
                $params['price'] = $rate;
            }
            return $this->call('POST', 'forceenter', $params);
        }
    }

    public function forceexit($trade_id = '')
    {
        // {"result":"Created sell orders for all open trades."}
        $params = array();
        $params['tradeid'] = empty($trade_id) ? "all" : $trade_id;
        return $this->call('POST', 'forceexit', $params);
    }

    public function getStrategy($strategy = '')
    {
        if (!empty($strategy)) {
            return $this->call('GET', "strategy/$strategy");
        } else {
            return "Please input strategy.";
        }
    }

    public function health()
    {
        // {"last_process":"2023-03-24T15:11:49.163613+00:00","last_process_ts":1679670709}
        return $this->call('GET', 'health');
    }

    // not working
    public function listAvailablePairs($timeframe = '', $stake = '', $candle_type = '')
    {
        $params = array();
        if (!empty($timeframe)) {
            $params['timeframe'] = $timeframe;
        }

        if (!empty($stake)) {
            $params['stake_currency'] = $stake;
        }

        if (!empty($candle_type)) {
            $params['candletype'] = $candle_type;
        }
        return $this->call('GET', 'available_pairs', $params);
    }

    public function listFreqaimodels()
    {
        // {"freqaimodels":[]}
        return $this->call('GET', 'freqaimodels');
    }

    public function listStrategies()
    {
        // {"strategies":["ADXMomentum","ADXMomentumHO","ASDTSRockwellTrading","AdxSmas","AlphaTrend","Apollo11","AverageStrategy","AwesomeMacd","BBRSITV","BBRSITV","BBRSITV","BBRSITV1","BBRSITV2","BBRSITV3","BBRSITV4","BBRSITV5","BBRSITV5_TSL3D","BBRSITV_15m","BBRSITV_1a","BBRSITV_1b","BBRSITV_1c","BBRSITV_1d","BBRSITV_1e"]}
        return $this->call('GET', 'strategies');
    }

    public function locks($api_ip = '')
    {
        // {"lock_count":0,"locks":[]}
        return $this->call('GET', 'locks');
    }

    public function logs($limit = 0)
    {
        // {
        //  "log_count":888,
        //  "logs":[
        //      [
        //          "2023-12-06 13:43:38",
        //          1701870218203.7314,
        //          "freqtrade.worker",
        //          "INFO",
        //          "Bot heartbeat. PID=1977196, version='2023.11-dev-1430659c1', state='RUNNING'"
        //      ],
        //      ["2023-12-06 13:44:38",1701870278245.2314,"freqtrade.worker","INFO",
        //       "Bot heartbeat. PID=1977196, version='2023.11-dev-1430659c1', state='RUNNING'"]
        //  ]
        // }
        $params = array();
        if (intval($limit) > 0) {
            $params['limit'] = intval($limit);
        }
        return $this->call('GET', 'logs', $params);
    }

    public function message($ws_token)
    {
        if (empty($ws_token)) {
            return [];
        }

        $params = array();
        $params['token'] = $ws_token;

        return $this->call('GET', 'message/ws', $params);
    }

    public function mixTags()
    {
        // [
        //   {
        //     "mix_tag":"buy_1 fastk_profit_sell",
        //     "profit_ratio":3.195169621920022,
        //     "profit_pct":319.59,
        //     "profit_abs":66.42404872000002,
        //     "count":185
        //   },
        //   {
        //     "mix_tag":"buy_1 fastk_profit_sell_delay",
        //     "profit_ratio":0.09307955654235049,
        //     "profit_pct":9.25,
        //     "profit_abs":1.95517928,
        //     "count":11
        //   },
        //   {
        //     "mix_tag":"buy_1 fastk_loss_sell",
        //     "profit_ratio":-1.7537018783679206,
        //     "profit_pct":-165.43,
        //     "profit_abs":-37.61377635000001,
        //     "count":23
        //   },
        //   {
        //     "mix_tag":"buy_1 stop_loss",
        //     "profit_ratio":-0.3689323954583724,
        //     "profit_pct":-18.4,
        //     "profit_abs":-7.79176693,
        //     "count":2
        //   }
        // ]
        return $this->call('GET', 'mix_tags');
    }

    public function monthly($months = 7)
    {
        // {
        //   "data":[
        //     {
        //       "date":"2023-03",
        //       "abs_profit":-5.01943985,
        //       "rel_profit":-0.05112539,
        //       "starting_balance":98.17901064,
        //       "fiat_value":-5.01943985,
        //       "trade_count":8
        //     },
        //     {"date":"2023-02","abs_profit":2.8721815300000006,"rel_profit":0.03013616,"starting_balance":95.30682911,"fiat_value":2.8721815300000006,"trade_count":21},
        //     {"date":"2023-01","abs_profit":3.468135750000001,"rel_profit":0.03776334,"starting_balance":91.83869336,"fiat_value":3.468135750000001,"trade_count":15},
        //     {"date":"2022-12","abs_profit":0.8647536300000002,"rel_profit":0.00950551,"starting_balance":90.97393973,"fiat_value":0.8647536300000002,"trade_count":9},
        //     {"date":"2022-11","abs_profit":20.973939729999998,"rel_profit":0.29962771,"starting_balance":70.0,"fiat_value":20.973939729999998,"trade_count":37},
        //     {"date":"2022-10","abs_profit":0.0,"rel_profit":0.0,"starting_balance":70.0,"fiat_value":0.0,"trade_count":0},
        //     {"date":"2022-09","abs_profit":0.0,"rel_profit":0.0,"starting_balance":70.0,"fiat_value":0.0,"trade_count":0}
        //   ],
        //   "fiat_display_currency":"USD",
        //   "stake_currency":"BUSD"
        // }
        return $this->call('GET', 'monthly', array('timescale' => $months));
    }

    public function pairCandles($pair = '', $timeframe = '', $limit = 0)
    {
        // {
        // "strategy":"Cenderawasih_30m_prod2",
        // "pair":"BTC/BUSD",
        // "timeframe":"15m",
        // "timeframe_ms":900000,
        // "columns":["date","open","high","low","close","volume","date_1d","age_filter_ok_1d","btc_date_30m","btc_rsi_30m","date_2h","pct_change_2h","rsi","pct_change","live_data_ok","hma_offset_buy","hma_offset_buy2","hma_offset_buy3","hma_offset_buy4","ema_offset_sell","ema_offset_sell2","ema_offset_sell3","ema_offset_sell4","enter_tag","enter_long","exit_tag","exit_long","__date_ts","_enter_long_signal_close","_exit_long_signal_close"],
        // "data":[["2023-03-03 12:30:00",22350.05,22359.97,22328.9,22354.94,1473.59896,null,null,"2023-03-03 12:30:00",null,null,null,null,null,false,null,null,null,null,null,null,null,null,"",null,"",null,1677846600000,null,null]],
        // "length":0,
        // "buy_signals":0,
        // "sell_signals":0,
        // "enter_long_signals":0,
        // "exit_long_signals":0,
        // "enter_short_signals":0,
        // "exit_short_signals":0,
        // "last_analyzed":"1970-01-01 00:00:00",
        // "last_analyzed_ts":0,
        // "data_start_ts":0,
        // "data_start":"",
        // "data_stop":"",
        // "data_stop_ts":0
        // }
        if (!empty($pair) and !empty($timeframe)) {
            $params = array();
            $params['pair'] = $pair;
            $params['timeframe'] = $timeframe;
            if (intval($limit) > 0) {
                $params['limit'] = intval($limit);
            }
            return $this->call('GET', 'pairCandles', $params);
        } else {
            return "Please input coin, stake, and timeframe";
        }
    }

    public function pairHistory($coin = '', $stake = '', $timeframe = '', $timerange = '', $strategy = '')
    {
        if (!empty($coin) and !empty($stake) and !empty($timeframe) and !empty($timerange) and !empty($strategy)) {
            $params = array();
            $params['pair'] = "$coin/$stake";
            $params['timeframe'] = $timeframe;
            $params['timerange'] = $timerange;
            $params['strategy'] = $strategy;
            return $this->call('GET', 'pair_history', $params);
        } else {
            return "Please input coin, stake, timeframe, timerange, and strategy";
        }
    }

    public function performance()
    {
        // [
        //   {
        //     "pair":"BTC/BUSD",
        //     "profit":0.69,
        //     "profit_ratio":0.006916937411179613,
        //     "profit_pct":0.69,
        //     "profit_abs":0.1368898,
        //     "count":3
        //   },{
        //     "pair":"ETH/BUSD",
        //     "profit":-0.2,
        //     "profit_ratio":-0.0020035491463030633,
        //     "profit_pct":-0.2,
        //     "profit_abs":-0.04005113,
        //     "count":1
        //   },{
        //     "pair":"ALGO/BUSD",
        //     "profit":-0.68,
        //     "profit_ratio":-0.006775209125078577,
        //     "profit_pct":-0.68,
        //     "profit_abs":-0.134031,
        //     "count":1
        //   }
        // ]
        return $this->call('GET', 'performance');
    }

    public function ping()
    {
        // {"status": "pong"}
        return $this->call('GET', 'ping');
    }

    public function profit($days = 0)
    {
        // {
        //   "profit_closed_coin":7.53922199,
        //   "profit_closed_percent_mean":1.09,
        //   "profit_closed_ratio_mean":0.010895122349114342,
        //   "profit_closed_percent_sum":41.4,
        //   "profit_closed_ratio_sum":0.4140146492663451,
        //   "profit_closed_percent":10.77,
        //   "profit_closed_ratio":0.10770317128571429,
        //   "profit_closed_fiat":7.53753320427424,
        //   "profit_all_coin":7.53922199,
        //   "profit_all_percent_mean":1.09,
        //   "profit_all_ratio_mean":0.010895122349114342,
        //   "profit_all_percent_sum":41.4,
        //   "profit_all_ratio_sum":0.4140146492663451,
        //   "profit_all_percent":10.77,
        //   "profit_all_ratio":0.10770317128571429,
        //   "profit_all_fiat":7.53753320427424,
        //   "trade_count":38,
        //   "closed_trade_count":38,
        //   "first_trade_date":"2023-09-01 12:55:05",
        //   "first_trade_humanized":"a week ago",
        //   "first_trade_timestamp":1693572905889,
        //   "latest_trade_date":"2023-09-09 01:10:05",
        //   "latest_trade_humanized":"40 minutes ago",
        //   "latest_trade_timestamp":1694221805343,
        //   "avg_duration":"1:04:46",
        //   "best_pair":"FLM/USDT",
        //   "best_rate":12.66,
        //   "best_pair_profit_ratio":0.12659917936217996,
        //   "winning_trades":35,
        //   "losing_trades":3,
        //   "profit_factor":2.7183994377619123,
        //   "winrate":0.9210526315789473,
        //   "expectancy":0.1984005786842105,
        //   "expectancy_ratio":0.13566311350751925,
        //   "max_drawdown":0.024547848670654356,
        //   "max_drawdown_abs":1.8020319,
        //   "max_drawdown_start":"2023-09-04 22:20:05",
        //   "max_drawdown_start_timestamp":1693866005677,
        //   "max_drawdown_end":"2023-09-05 03:59:06",
        //   "max_drawdown_end_timestamp":1693886346591,
        //   "trading_volume":1393.2460070000006,
        //   "bot_start_timestamp":1693564196608,
        //   "bot_start_date":"2023-09-01 10:29:56"
        // }
        $params = array();
        if ($days > 0) {
            $params['timescale'] = $days;
        }
        return $this->call('GET', 'profit', $params);
    }

    public function reloadConfig()
    {
        return $this->call('POST', 'reload_config');
    }

    public function showConfig()
    {
        // {
        //   "version":"2023.3.dev-126a91314",
        //   "strategy_version":"Kasuari_v1a_1h",
        //   "api_version":2.25,
        //   "dry_run":false,
        //   "trading_mode":"futures",
        //   "short_allowed":true,
        //   "stake_currency":"USDT",
        //   "stake_amount":"unlimited",
        //   "available_capital":80,
        //   "stake_currency_decimals":3,
        //   "max_open_trades":4,
        //   "minimal_roi":[100],
        //   "stoploss":-0.99,
        //   "stoploss_on_exchange":false,
        //   "trailing_stop":true,
        //   "trailing_stop_positive":0.005,
        //   "trailing_stop_positive_offset":0.03,
        //   "trailing_only_offset_is_reached":true,
        //   "unfilledtimeout":{"entry":30,"exit":10,"unit":"minutes","exit_timeout_count":0},
        //   "order_types":{"entry":"limit","exit":"market","emergency_exit":"market","force_exit":"market","force_entry":"market","stoploss":"market","stoploss_on_exchange":false,"stoploss_on_exchange_interval":60},
        //   "use_custom_stoploss":false,
        //   "timeframe":"1h",
        //   "timeframe_ms":3600000,
        //   "timeframe_min":60,
        //   "exchange":"binance",
        //   "strategy":"Kasuari_1h_1a",
        //   "force_entry_enable":false,
        //   "exit_pricing":{"price_side":"other","use_order_book":true,"order_book_top":1},
        //   "entry_pricing":{"price_side":"same","use_order_book":true,"order_book_top":1,"price_last_balance":0,"check_depth_of_market":{"enabled":false,"bids_to_ask_delta":1}},
        //   "bot_name":"freqtrade_bot",
        //   "state":"running",
        //   "runmode":"live",
        //   "position_adjustment_enable":false,
        //   "max_entry_position_adjustment":-1
        // }
        return $this->call('GET', 'show_config');
    }

    public function start()
    {
        // {"status":"starting trader ..."}
        return $this->call('POST', 'start');
    }

    public function stats()
    {
        // {
        //   "exit_reasons":{
        //     "EMA_up ":{
        //       "wins":2,
        //       "losses":2,
        //       "draws":0
        //     },
        //     "force_exit":{
        //       "wins":0,
        //       "losses":1,
        //       "draws":0
        //     }
        //   },
        //   "durations":{
        //     "wins":null,
        //     "draws":null,
        //     "losses":null
        //   }
        // }
        return $this->call('GET', 'stats');
    }

    public function status()
    {
        // [
        //   {
        //     "trade_id":1,
        //     "pair":"BTC/BUSD:BUSD",
        //     "base_currency":"BTC",
        //     "quote_currency":"BUSD",
        //     "is_open":true,
        //     "is_short":false,
        //     "exchange":"binance",
        //     "amount":0.002,
        //     "amount_requested":0.00105,
        //     "stake_amount":52.228,
        //     "max_stake_amount":78.342,
        //     "strategy":"test_dca",
        //     "enter_tag":null,
        //     "timeframe":5,
        //     "fee_open":0.0004,
        //     "fee_open_cost":0.020891200000000002,
        //     "fee_open_currency":"BUSD",
        //     "fee_close":0.0004,
        //     "fee_close_cost":0.01044316,
        //     "fee_close_currency":"BUSD",
        //     "open_date":"2023-08-25 00:21:00",
        //     "open_timestamp":1692922860981,
        //     "open_rate":26114.0, //avg open rate
        //     "open_rate_requested":26118.1,
        //     "open_trade_value":52.2488912,
        //     "close_date":null,
        //     "close_timestamp":null,
        //     "close_rate":null,
        //     "close_rate_requested":26107.9, //last close rate
        //     "close_profit":null,
        //     "close_profit_pct":-0.1, //profit pct of last partial exit
        //     "close_profit_abs":-0.02698876, //profit from last partial exit
        //     "profit_ratio":-0.00064663, // profit % of leftover stake
        //     "profit_pct":-0.06,
        //     "profit_abs":-0.0337856, // profit of leftover stake
        //     "profit_fiat":-0.033761071654399996,
        //     "realized_profit":-0.02698876, //profit from all partial exit
        //     "realized_profit_ratio":-0.00103308,
        //     "exit_reason":"partial_exit",
        //     "exit_order_status":"",
        //     "stop_loss_abs":261.3,
        //     "stop_loss_ratio":-0.99,
        //     "stop_loss_pct":-99.0,
        //     "stoploss_order_id":null,
        //     "stoploss_last_update":null,
        //     "stoploss_last_update_timestamp":null,
        //     "initial_stop_loss_abs":261.3,
        //     "initial_stop_loss_ratio":-0.99,
        //     "initial_stop_loss_pct":-99.0,
        //     "min_rate":26103.0,
        //     "max_rate":26121.9,
        //     "min_profit":-0.00065467,
        //     "max_profit":0.00006891,
        //     "open_order_id":null,
        //     "orders":[
        //       {
        //         "pair":"BTC/BUSD:BUSD",
        //         "order_id":"dry_run_buy_1692922860.978088",
        //         "status":"closed",
        //         "remaining":0.0,
        //         "amount":0.001,
        //         "safe_price":26120.1,
        //         "cost":26.1201,
        //         "filled":0.001,
        //         "ft_order_side":"buy",
        //         "order_type":"limit",
        //         "is_open":false,
        //         "order_timestamp":1692922860978,
        //         "order_filled_timestamp":1692922860980,
        //         "ft_fee_base":null
        //       },{
        //         "pair":"BTC/BUSD:BUSD",
        //         "order_id":"dry_run_buy_1692923143.520144",
        //         "status":"closed",
        //         "remaining":0.0,
        //         "amount":0.001,
        //         "safe_price":26103.8,
        //         "cost":26.1038,
        //         "filled":0.001,
        //         "ft_order_side":"buy",
        //         "order_type":"limit",
        //         "is_open":false,
        //         "order_timestamp":1692923143520,
        //         "order_filled_timestamp":1692923143522,
        //         "ft_fee_base":null
        //       },{
        //         "pair":"BTC/BUSD:BUSD",
        //         "order_id":"dry_run_buy_1692923446.101724",
        //         "status":"closed",
        //         "remaining":0.0,
        //         "amount":0.001,
        //         "safe_price":26118.1,
        //         "cost":26.1181,
        //         "filled":0.001,
        //         "ft_order_side":"buy",
        //         "order_type":"limit",
        //         "is_open":false,
        //         "order_timestamp":1692923446101,
        //         "order_filled_timestamp":1692923446103,
        //         "ft_fee_base":null
        //       },{
        //         "pair":"BTC/BUSD:BUSD",
        //         "order_id":"dry_run_sell_1692923511.008276",
        //         "status":"closed",
        //         "remaining":0.0,
        //         "amount":0.001,
        //         "safe_price":26107.9,
        //         "cost":26.1079,
        //         "filled":0.001,
        //         "ft_order_side":"sell",
        //         "order_type":"market",
        //         "is_open":false,
        //         "order_timestamp":1692923511008,
        //         "order_filled_timestamp":1692923511010,
        //         "ft_fee_base":null
        //       }
        //     ],
        //     "leverage":1.0,
        //     "interest_rate":0.0,
        //     "liquidation_price":1305.7,
        //     "funding_fees":0.0,
        //     "trading_mode":"futures",
        //     "amount_precision":3.0,
        //     "price_precision":1.0,
        //     "precision_mode":2,
        //     "stoploss_current_dist":-25856.7,
        //     "stoploss_current_dist_pct":-99.0,
        //     "stoploss_current_dist_ratio":-0.98999541,
        //     "stoploss_entry_dist":-51.72650024,
        //     "stoploss_entry_dist_ratio":-0.99000187,
        //     "current_rate":26118.0,
        //     "total_profit_abs":-0.06077436,
        //     "total_profit_fiat":-0.06073023781464,
        //     "total_profit_ratio":-0.0007757570651757677,
        //     "open_orders":null
        //   }
        // ]
        return $this->call('GET', 'status');
    }

    public function stop()
    {
        // {"status":"stopping trader ..."}
        return $this->call('POST', 'stop');
    }

    public function stopbuy()
    {
        // {"status":"No more entries will occur from now. Run /reloadConfig to reset."}
        return $this->call('POST', 'stopbuy');
    }

    public function stopentry()
    {
        // {"status":"No more entries will occur from now. Run /reloadConfig to reset."}
        return $this->call('POST', 'stopentry');
    }

    public function sysinfo()
    {
        // {"cpu_pct":[9.1,1.0,11.9,1.0],"ram_pct":34.8}
        return $this->call('GET', 'sysinfo');
    }

    public function trade($trade_id = 0)
    {
        // {
        //   "trade_id":6,
        //   "pair":"BTC/BUSD",
        //   "base_currency":"BTC",
        //   "quote_currency":"BUSD",
        //   "is_open":false,
        //   "is_short":false,
        //   "exchange":"binance",
        //   "amount":0.00073,
        //   "amount_requested":0.00073246,
        //   "stake_amount":19.9236418,
        //   "max_stake_amount":19.9236418,
        //   "strategy":"Cenderawasih_30m_prod2",
        //   "enter_tag":"force_entry",
        //   "timeframe":30,
        //   "fee_open":0.001,
        //   "fee_open_cost":0.0199236418,
        //   "fee_open_currency":"BUSD",
        //   "fee_close":0.001,
        //   "fee_close_cost":0.019963952400000002,
        //   "fee_close_currency":"BUSD",
        //   "open_date":"2023-03-23 01:45:11",
        //   "open_timestamp":1679535911933,
        //   "open_rate":27292.66,
        //   "open_rate_requested":27292.65,
        //   "open_trade_value":19.94356544,
        //   "close_date":"2023-03-23 02:30:29",
        //   "close_timestamp":1679538629398,
        //   "close_rate":27347.88,
        //   "close_rate_requested":27347.88,
        //   "close_profit":0.000021210349836113422,
        //   "close_profit_pct":0.0,
        //   "close_profit_abs":0.00042301,
        //   "profit_ratio":0.000021210349836113422,
        //   "profit_pct":0.0,
        //   "profit_abs":0.00042301,
        //   "profit_fiat":0.00042427902999999994,
        //   "realized_profit":0.00042301,
        //   "realized_profit_ratio":0.000021210349836113422,
        //   "exit_reason":"EMA_up ",
        //   "exit_order_status":"closed",
        //   "stop_loss_abs":27293.19,
        //   "stop_loss_ratio":-0.002,
        //   "stop_loss_pct":-0.2,
        //   "stoploss_order_id":null,
        //   "stoploss_last_update":null,
        //   "stoploss_last_update_timestamp":null,
        //   "initial_stop_loss_abs":272.93,
        //   "initial_stop_loss_ratio":-0.99,
        //   "initial_stop_loss_pct":-99.0,
        //   "min_rate":27183.71,
        //   "max_rate":27354.07,
        //   "open_order_id":null,
        //   "orders":[
        //     {
        //       "pair":"BTC/BUSD",
        //       "order_id":"dry_run_buy_1679535911.930149",
        //       "status":"closed",
        //       "remaining":0.0,
        //       "amount":0.00073,
        //       "safe_price":27292.66,
        //       "cost":19.9236418,
        //       "filled":0.00073,
        //       "ft_order_side":"buy",
        //       "order_type":"market",
        //       "is_open":false,
        //       "order_timestamp":1679535911000,
        //       "order_filled_timestamp":1679535911932
        //     },
        //     {
        //       "pair":"BTC/BUSD",
        //       "order_id":"dry_run_sell_1679538628.793384",
        //       "status":"closed",
        //       "remaining":0.0,
        //       "amount":0.00073,
        //       "safe_price":27347.88,
        //       "cost":19.9639524,
        //       "filled":0.00073,
        //       "ft_order_side":"sell",
        //       "order_type":"market",
        //       "is_open":false,
        //       "order_timestamp":1679538628000,
        //       "order_filled_timestamp":1679538629377
        //     }
        //   ],
        //   "leverage":1.0,
        //   "interest_rate":0.0,
        //   "liquidation_price":null,
        //   "funding_fees":0.0,
        //   "trading_mode":"spot",
        //   "stoploss_current_dist":-54.69000000000233,
        //   "stoploss_current_dist_pct":-0.2,
        //   "stoploss_current_dist_ratio":-0.00199979,
        //   "stoploss_entry_dist":-0.03946077,
        //   "stoploss_entry_dist_ratio":-0.00197862,
        //   "current_rate":27347.88,
        //   "total_profit_abs":0.00084602,
        //   "total_profit_fiat":0.0008485580599999999,
        //   "total_profit_ratio":0.00004246312037189908,
        //   "open_order":null
        // }

        return $this->call('GET', "trade/$trade_id");
    }

    public function tradeDetail($trade_id = 0)
    {
        // {
        //  "trade_id":3,
        //  "pair":"BTC/USDT",
        //  "base_currency":"BTC",
        //  "quote_currency":"USDT",
        //  "is_open":false,
        //  "exchange":"binance",
        //  "amount":0.00226,
        //  "amount_requested":0.002268,
        //  "stake_amount":99.3162424,
        //  "max_stake_amount":99.3162424,
        //  "strategy":"always_enter",
        //  "enter_tag":null,
        //  "timeframe":5,
        //  "fee_open":0.001,
        //  "fee_open_cost":0.09931624239999999,
        //  "fee_open_currency":"USDT",
        //  "fee_close":0.001,
        //  "fee_close_cost":0.098145359,
        //  "fee_close_currency":"USDT",
        //  "open_date":"2023-12-07 06:40:57",
        //  "open_timestamp":1701931257855,
        //  "open_rate":43945.24,
        //  "open_rate_requested":43945.24,
        //  "open_trade_value":99.41555864,
        //  "close_date":"2023-12-08 05:57:13",
        //  "close_timestamp":1702015033390,
        //  "realized_profit":-1.368345,
        //  "realized_profit_ratio":-0.013763891876541857,
        //  "close_rate":43427.15,
        //  "close_rate_requested":43427.15,
        //  "close_profit":-0.013763891876541857,
        //  "close_profit_pct":-1.38,
        //  "close_profit_abs":-1.368345,
        //  "trade_duration_s":83775,
        //  "trade_duration":1396,
        //  "profit_ratio":-0.013763891876541857,
        //  "profit_pct":-1.38,
        //  "profit_abs":-1.368345,
        //  "exit_reason":"force_exit",
        //  "exit_order_status":"closed",
        //  "stop_loss_abs":439.46,
        //  "stop_loss_ratio":-0.99,
        //  "stop_loss_pct":-99,
        //  "stoploss_order_id":null,
        //  "stoploss_last_update":null,
        //  "stoploss_last_update_timestamp":null,
        //  "initial_stop_loss_abs":439.46,
        //  "initial_stop_loss_ratio":-0.99,
        //  "initial_stop_loss_pct":-99,
        //  "min_rate":42838,
        //  "max_rate":43945.24,
        //  "min_profit":-0.02714356,
        //  "max_profit":-0.001998,
        //  "leverage":1,
        //  "interest_rate":0,
        //  "liquidation_price":null,
        //  "is_short":false,
        //  "trading_mode":"spot",
        //  "funding_fees":0,
        //  "amount_precision":5,
        //  "price_precision":2,
        //  "precision_mode":2,
        //  "contract_size":1,
        //  "has_open_orders":false,
        //  "orders":[
        //      {
        //          "amount":0.00226,
        //          "safe_price":43945.24,
        //          "ft_order_side":"buy",
        //          "order_filled_timestamp":1701931257855,
        //          "ft_is_entry":true,
        //          "pair":"BTC/USDT",
        //          "order_id":"dry_run_buy_BTC/USDT_1701931257.853158",
        //          "status":"closed",
        //          "average":43945.24,
        //          "cost":99.3162424,
        //          "filled":0.00226,
        //          "is_open":false,
        //          "order_date":"2023-12-07 06:40:57",
        //          "order_timestamp":1701931257853,
        //          "order_filled_date":"2023-12-07 06:40:57",
        //          "order_type":"limit",
        //          "price":43945.24,
        //          "remaining":0,
        //          "ft_fee_base":null,
        //          "funding_fee":null
        //      },
        //      {
        //          "amount":0.00226,
        //          "safe_price":43427.15,
        //          "ft_order_side":"sell",
        //          "order_filled_timestamp":1702015033390,
        //          "ft_is_entry":false,
        //          "pair":"BTC/USDT",
        //          "order_id":"dry_run_sell_BTC/USDT_1702015033.388068",
        //          "status":"closed",
        //          "average":43427.15,
        //          "cost":98.145359,
        //          "filled":0.00226,
        //          "is_open":false,
        //          "order_date":"2023-12-08 05:57:13",
        //          "order_timestamp":1702015033388,
        //          "order_filled_date":"2023-12-08 05:57:13",
        //          "order_type":"market",
        //          "price":43427.15,
        //          "remaining":0,
        //          "ft_fee_base":null,
        //          "funding_fee":0
        //      }
        //  ],
        //  "current_rate":43427.15,
        //  "profit_fiat":-1.3697133449999999,
        //  "total_profit_abs":0,
        //  "total_profit_fiat":0,
        //  "total_profit_ratio":null,
        //  "stoploss_current_dist":-42987.69,
        //  "stoploss_current_dist_ratio":-0.98988052,
        //  "stoploss_current_dist_pct":-98.99,
        //  "stoploss_entry_dist":-98.42337222,
        //  "stoploss_entry_dist_ratio":-0.99001981,
        //  "open_orders":"",
        //  "position_adjustment_enable":false,
        //  "max_entry_position_adjustment":-1
        // }

        return $this->call('GET', "trade_detail/$trade_id");
    }

    public function trades($limit = null, $offset = null, $order_by_id = false)
    {
        // {
        //   "trades":[
        //     {
        //       "trade_id":6,
        //       "pair":"BTC/BUSD",
        //       "base_currency":"BTC",
        //       "quote_currency":"BUSD",
        //       "is_open":false,
        //       "exchange":"binance",
        //       "amount":0.00073,
        //       "amount_requested":0.00073246,
        //       "stake_amount":19.9236418,
        //       "max_stake_amount":19.9236418,
        //       "strategy":"Cenderawasih_30m_prod2",
        //       "enter_tag":"force_entry",
        //       "timeframe":30,
        //       "fee_open":0.001,
        //       "fee_open_cost":0.0199236418,
        //       "fee_open_currency":"BUSD",
        //       "fee_close":0.001,
        //       "fee_close_cost":0.019963952400000002,
        //       "fee_close_currency":"BUSD",
        //       "open_date":"2023-03-23 01:45:11",
        //       "open_timestamp":1679535911933,
        //       "open_rate":27292.66,
        //       "open_rate_requested":27292.65,
        //       "open_trade_value":19.94356544,
        //       "close_date":"2023-03-23 02:30:29",
        //       "close_timestamp":1679538629398,
        //       "realized_profit":0.00042301,
        //       "realized_profit_ratio":0.000021210349836113422,
        //       "close_rate":27347.88,
        //       "close_rate_requested":27347.88,
        //       "close_profit":0.000021210349836113422,
        //       "close_profit_pct":0.0,
        //       "close_profit_abs":0.00042301,
        //       "trade_duration_s":2717,
        //       "trade_duration":45,
        //       "profit_ratio":0.000021210349836113422,
        //       "profit_pct":0.0,
        //       "profit_abs":0.00042301,
        //       "exit_reason":"EMA_up ",
        //       "exit_order_status":"closed",
        //       "stop_loss_abs":27293.19,
        //       "stop_loss_ratio":-0.002,
        //       "stop_loss_pct":-0.2,
        //       "stoploss_order_id":null,
        //       "stoploss_last_update":null,
        //       "stoploss_last_update_timestamp":null,
        //       "initial_stop_loss_abs":272.93,
        //       "initial_stop_loss_ratio":-0.99,
        //       "initial_stop_loss_pct":-99.0,
        //       "min_rate":27183.71,
        //       "max_rate":27354.07,
        //       "min_profit":-0.00598194,
        //       "max_profit":0.00024756,
        //       "leverage":1.0,
        //       "interest_rate":0.0,
        //       "liquidation_price":null,
        //       "is_short":false,
        //       "trading_mode":"spot",
        //       "funding_fees":0.0,
        //       "open_order_id":null,
        //       "orders":[
        //         {
        //           "amount":0.00073,
        //           "safe_price":27292.66,
        //           "ft_order_side":"buy",
        //           "order_filled_timestamp":1679535911932,
        //           "ft_is_entry":true,
        //           "pair":"BTC/BUSD",
        //           "order_id":"dry_run_buy_1679535911.930149",
        //           "status":"closed",
        //           "average":27292.66,
        //           "cost":19.9236418,
        //           "filled":0.00073,
        //           "is_open":false,
        //           "order_date":"2023-03-23 01:45:11",
        //           "order_timestamp":1679535911000,
        //           "order_filled_date":"2023-03-23 01:45:11",
        //           "order_type":"market",
        //           "price":27292.65,
        //           "remaining":0.0
        //         },
        //         {
        //           "amount":0.00073,
        //           "safe_price":27347.88,
        //           "ft_order_side":"sell",
        //           "order_filled_timestamp":1679538629377,
        //           "ft_is_entry":false,
        //           "pair":"BTC/BUSD",
        //           "order_id":"dry_run_sell_1679538628.793384",
        //           "status":"closed",
        //           "average":27347.88,
        //           "cost":19.9639524,
        //           "filled":0.00073,
        //           "is_open":false,
        //           "order_date":"2023-03-23 02:30:28",
        //           "order_timestamp":1679538628000,
        //           "order_filled_date":"2023-03-23 02:30:29",
        //           "order_type":"market",
        //           "price":27347.88,
        //           "remaining":0.0
        //         }
        //       ]
        //     },
        //   ]
        //   ,
        //   "trades_count":6,
        //   "offset":0,
        //   "total_trades":6
        //   }
        $params = array();
        if (isset($limit)) {
            $params['limit'] = $limit;
        }
        if (isset($offset)) {
            $params['offset'] = $offset;
        }
        $params['order_by_id'] = $order_by_id;
        return $this->call('GET', 'trades', $params);
    }

    public function version()
    {
        // {"version":"2023.3.dev"}
        return $this->call('GET', 'version');
    }

    public function weekly($weeks = 7)
    {
        // {
        //   "data":[
        //     {
        //       "date":"2022-09-29",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.3167557951,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     },
        //     {
        //       "date":"2022-09-28",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.3167557951,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     },
        //     {
        //       "date":"2022-09-27",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.3167557951,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     },
        //     {
        //       "date":"2022-09-26",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.3167557951,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     },
        //     {
        //       "date":"2022-09-25",
        //       "abs_profit":0.31243412,
        //       "rel_profit":0.0027648,
        //       "starting_balance":113.00432167509999,
        //       "fiat_value":0.31243412,
        //       "trade_count":1
        //     },
        //     {
        //       "date":"2022-09-24",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.00432167509999,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     },
        //     {
        //       "date":"2022-09-23",
        //       "abs_profit":0.0,
        //       "rel_profit":0.0,
        //       "starting_balance":113.00432167509999,
        //       "fiat_value":0.0,
        //       "trade_count":0
        //     }
        //   ],
        //   "fiat_display_currency":"USD",
        //   "stake_currency":"USDT"
        // }
        return $this->call('GET', 'weekly', array('timescale' => $weeks));
    }

    public function whitelist()
    {
        // {"whitelist":["BTC/BUSD","ETH/BUSD","XRP/BUSD","SOL/BUSD","CFX/BUSD","BNB/BUSD","AGIX/BUSD","LTC/BUSD","MATIC/BUSD","MASK/BUSD","FTM/BUSD","ADA/BUSD","APT/BUSD","SHIB/BUSD","OP/BUSD","LUNC/BUSD","MAGIC/BUSD","LINK/BUSD","FIL/BUSD","TRX/BUSD","AVAX/BUSD","GALA/BUSD","STX/BUSD","LUNA/BUSD","LDO/BUSD","JOE/BUSD","HOOK/BUSD","ATOM/BUSD","GFT/BUSD","MINA/BUSD","DYDX/BUSD","NEAR/BUSD","CKB/BUSD","DOT/BUSD","KEY/BUSD","FTT/BUSD","BNX/BUSD","APE/BUSD","GMT/BUSD","LOOM/BUSD","ETC/BUSD","USTC/BUSD","FET/BUSD","UNI/BUSD","SNM/BUSD","RUNE/BUSD","SRM/BUSD","GRT/BUSD","MANA/BUSD","RNDR/BUSD"],"length":50,"method":["VolumePairList","AgeFilter"]}

        return $this->call('GET', 'whitelist');
    }

    public static function chartExpectancyData($chart_data, $min_timestamp = 0, $max_timestamp = 0)
    {

        $chartExpectancyData = [];

        for ($i = 0; $i < count($chart_data); $i++) {
            if ($max_timestamp > 0) {
                if ($chart_data[$i]['close_timestamp'] > $max_timestamp) {
                    continue;
                }
            }

            if ($chart_data[$i]['close_timestamp'] >= $min_timestamp) {
                $chartExpectancyData[] = $chart_data[$i];
            }
        }

        return $chartExpectancyData;
    }
}
