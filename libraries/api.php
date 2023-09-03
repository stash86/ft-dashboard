<?php
class Api
{
  public $ip_address = '';
  public $username = '';
  public $password = '';
  public function __construct($ip_address, $username, $password) {
    $this->ip_address = $ip_address;
    $this->username = $username;
    $this->password = $password;
  }

  function balance($fiat_display = "USD")
  {
    // {
    //   "currencies":
    //   [
    //     {
    //     "currency":"USDT",
    //     "free":114.46136949,
    //     "balance":114.46136949,
    //     "used":0.0,
    //     "est_stake":114.46136949,
    //     "stake":"USDT",
    //     "side":"long",
    //     "leverage":1.0,
    //     "is_position":false,
    //     "position":0.0
    //     }
    //   ],
    //   "total":114.46136949,
    //   "symbol":"USD",
    //   "value":114.46136949,
    //   "stake":"USDT",
    //   "note":"Simulated balances",
    //   "starting_capital":70.0,
    //   "starting_capital_ratio":0.6351624212857143,
    //   "starting_capital_pct":63.52,
    //   "starting_capital_fiat":70.0,
    //   "starting_capital_fiat_ratio":0.6351624212857143,
    //   "starting_capital_fiat_pct":63.52
    // }
    return $this->call('GET', 'balance', array('fiat_display_currency'=> $fiat_display));
  }

  function blacklist($coin='', $stake='.*')
  {
    // {
    //  "blacklist":[".*(_PREMIUM|BEAR|BULL|DOWN|HALF|HEDGE|UP|[1235][SL]|2021)/.*",".*(USDT_|USDT-).*","(AUD|BRZ|CAD|CHF|EUR|GBP|HKD|IDRT|JPY|NGN|RUB|SGD|TRY|UAH|USD|ZAR)/.*","(BUSD|CUSD|CUSDT|DAI|PAX|PAXG|SUSD|TUSD|USDC|USDN|USDP|USDT|UST|VAI|USDD|USDJ)/.*","(ACM|AFA|ALA|ALL|ALPINE|APL|ASR|ATM|BAR|CAI|CITY|FOR|GAL|GOZ|IBFK|JUV|LAZIO|LEG|LOCK-1|NAVI|NMR|NOV|OG|PFL|PORTO|PSG|ROUSH|SANTOS|STV|TH|TRA|UCH|UFC|YBO)/.*"],
    //  "blacklist_expanded":["BULL/USDT","BULL/BUSD","BEAR/USDT","BEAR/BUSD","ETHBULL/USDT","ETHBULL/BUSD","ETHBEAR/USDT","ETHBEAR/BUSD","EOSBULL/USDT","EOSBULL/BUSD","EOSBEAR/USDT","EOSBEAR/BUSD","XRPBULL/USDT","XRPBULL/BUSD","XRPBEAR/USDT","XRPBEAR/BUSD","BNBBULL/USDT","BNBBULL/BUSD","BNBBEAR/USDT","BNBBEAR/BUSD","BTCUP/USDT","BTCDOWN/USDT","ETHUP/USDT","ETHDOWN/USDT","ADAUP/USDT","ADADOWN/USDT","LINKUP/USDT","LINKDOWN/USDT","BNBUP/USDT","BNBDOWN/USDT","XTZUP/USDT","XTZDOWN/USDT","EOSUP/USDT","EOSDOWN/USDT","TRXUP/USDT","TRXDOWN/USDT","XRPUP/USDT","XRPDOWN/USDT","DOTUP/USDT","DOTDOWN/USDT","LTCUP/USDT","LTCDOWN/USDT","UNIUP/USDT","UNIDOWN/USDT","SXPUP/USDT","SXPDOWN/USDT","FILUP/USDT","FILDOWN/USDT","YFIUP/USDT","YFIDOWN/USDT","BCHUP/USDT","BCHDOWN/USDT","AAVEUP/USDT","AAVEDOWN/USDT","SUSHIUP/USDT","SUSHIDOWN/USDT","XLMUP/USDT","XLMDOWN/USDT","1INCHUP/USDT","1INCHDOWN/USDT","EUR/BUSD","EUR/USDT","GBP/BUSD","GBP/USDT","AUD/BUSD","AUD/USDT","AUD/USDC","TUSD/BTC","TUSD/ETH","TUSD/BNB","TUSD/USDT","PAX/BTC","PAX/BNB","PAX/USDT","PAX/ETH","USDC/BNB","USDC/USDT","PAX/TUSD","USDC/TUSD","USDC/PAX","BUSD/USDT","BUSD/NGN","BUSD/RUB","BUSD/TRY","USDT/TRY","USDT/RUB","USDT/ZAR","BUSD/ZAR","USDT/IDRT","BUSD/IDRT","USDT/UAH","BUSD/BIDR","USDT/BIDR","DAI/BNB","DAI/BTC","DAI/USDT","DAI/BUSD","USDT/BKRW","BUSD/BKRW","USDT/DAI","BUSD/DAI","PAXG/BNB","PAXG/BTC","PAXG/BUSD","PAXG/USDT","USDT/NGN","USDT/BRL","BUSD/BRL","SUSD/BTC","SUSD/ETH","SUSD/USDT","BUSD/BVND","USDT/BVND","USDC/BUSD","TUSD/BUSD","PAX/BUSD","BUSD/VAI","BUSD/UAH","USDP/BUSD","USDP/USDT","UST/BTC","UST/BUSD","UST/USDT","BUSD/PLN","BUSD/RON","NMR/BTC","NMR/BUSD","NMR/USDT","FOR/BTC","FOR/BUSD","JUV/BTC","JUV/BUSD","JUV/USDT","PSG/BTC","PSG/BUSD","PSG/USDT","OG/BTC","OG/USDT","ATM/BTC","ATM/USDT","ASR/BTC","ASR/USDT","ACM/BTC","ACM/BUSD","ACM/USDT","BAR/BTC","BAR/BUSD","BAR/USDT","ATM/BUSD","FOR/USDT","LAZIO/TRY","LAZIO/EUR","LAZIO/BTC","LAZIO/USDT","LAZIO/BUSD","CITY/BTC","CITY/BNB","CITY/BUSD","CITY/USDT","PORTO/BTC","PORTO/USDT","PORTO/TRY","PORTO/EUR","SANTOS/BTC","SANTOS/USDT","SANTOS/BRL","SANTOS/TRY","FOR/BNB","ALPINE/EUR","ALPINE/TRY","ALPINE/USDT","ALPINE/BTC","ALPINE/BUSD","SANTOS/BUSD","PORTO/BUSD","GAL/USDT","GAL/BUSD","GAL/BNB","GAL/BTC","GAL/EUR","GAL/TRY","GAL/ETH","GAL/BRL","OG/BUSD","ASR/BUSD"],
    //  "errors":{},
    //  "length":5,
    //  "method":["VolumePairList","AgeFilter"]}
    
    if(!empty($coin)) {
      $params = array();
      $params['blacklist'] = ["$coin/$stake"];
      return $this->call('POST', 'blacklist', $params);  
    } else {
      
      return $this->call('GET', 'blacklist');  
    }
  }

  function call($method = 'GET', $command='ping', $data = False, $contentType= False, $token = False)
  {
    $url = "http://{$this->ip_address}/api/v1/{$command}";
    // return $url;

    $client = new \GuzzleHttp\Client(); 

    $array_option = array();
    $array_option['auth'] = [$this->username, $this->password];

    switch (strtoupper($method))
    {
      case "POST":
      case "DELETE":
        if ($data){
          $array_option['json'] = $data;
        }
        break;
      case "PUT":
        break;
      default:
        if ($data)
          $array_option['query'] = $data;
    }

    try {
      $res = $client->request(strtoupper($method), $url, $array_option);
    }
    catch (GuzzleHttp\Exception\ConnectException $e) {
      return "";
    }
    catch (Exception $e) {
      return $e->getResponse()->getBody()->getContents();
    }

    
    // return json_encode($array_option);
    // return $res->getStatusCode();
    // "200"
    // echo $res->getHeader('content-type')[0];
    // 'application/json; charset=utf8'
    return $res->getBody();
  }

  function call_post($command)
  {
    return $this->call('POST', $command);
  }

  function cancel_open_order($trade_id=0)
  {
    return $this->call('DELETE', "trades/$trade_id/open-order");
  }

  function count()
  {
    // {"current":0,"max":0,"total_stake":0.0}
    return $this->call('GET', 'count');
  }
  
  function daily($days=7)
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
    return $this->call('GET', 'daily', array('timescale'=> $days));
  }

  // Not working
  function delete_blacklist($coin='', $stake='.*')
  {
    // {
    //  "blacklist":[".*(_PREMIUM|BEAR|BULL|DOWN|HALF|HEDGE|UP|[1235][SL]|2021)/.*",".*(USDT_|USDT-).*","(AUD|BRZ|CAD|CHF|EUR|GBP|HKD|IDRT|JPY|NGN|RUB|SGD|TRY|UAH|USD|ZAR)/.*","(BUSD|CUSD|CUSDT|DAI|PAX|PAXG|SUSD|TUSD|USDC|USDN|USDP|USDT|UST|VAI|USDD|USDJ)/.*","(ACM|AFA|ALA|ALL|ALPINE|APL|ASR|ATM|BAR|CAI|CITY|FOR|GAL|GOZ|IBFK|JUV|LAZIO|LEG|LOCK-1|NAVI|NMR|NOV|OG|PFL|PORTO|PSG|ROUSH|SANTOS|STV|TH|TRA|UCH|UFC|YBO)/.*"],
    //  "blacklist_expanded":["BULL/USDT","BULL/BUSD","BEAR/USDT","BEAR/BUSD","ETHBULL/USDT","ETHBULL/BUSD","ETHBEAR/USDT","ETHBEAR/BUSD","EOSBULL/USDT","EOSBULL/BUSD","EOSBEAR/USDT","EOSBEAR/BUSD","XRPBULL/USDT","XRPBULL/BUSD","XRPBEAR/USDT","XRPBEAR/BUSD","BNBBULL/USDT","BNBBULL/BUSD","BNBBEAR/USDT","BNBBEAR/BUSD","BTCUP/USDT","BTCDOWN/USDT","ETHUP/USDT","ETHDOWN/USDT","ADAUP/USDT","ADADOWN/USDT","LINKUP/USDT","LINKDOWN/USDT","BNBUP/USDT","BNBDOWN/USDT","XTZUP/USDT","XTZDOWN/USDT","EOSUP/USDT","EOSDOWN/USDT","TRXUP/USDT","TRXDOWN/USDT","XRPUP/USDT","XRPDOWN/USDT","DOTUP/USDT","DOTDOWN/USDT","LTCUP/USDT","LTCDOWN/USDT","UNIUP/USDT","UNIDOWN/USDT","SXPUP/USDT","SXPDOWN/USDT","FILUP/USDT","FILDOWN/USDT","YFIUP/USDT","YFIDOWN/USDT","BCHUP/USDT","BCHDOWN/USDT","AAVEUP/USDT","AAVEDOWN/USDT","SUSHIUP/USDT","SUSHIDOWN/USDT","XLMUP/USDT","XLMDOWN/USDT","1INCHUP/USDT","1INCHDOWN/USDT","EUR/BUSD","EUR/USDT","GBP/BUSD","GBP/USDT","AUD/BUSD","AUD/USDT","AUD/USDC","TUSD/BTC","TUSD/ETH","TUSD/BNB","TUSD/USDT","PAX/BTC","PAX/BNB","PAX/USDT","PAX/ETH","USDC/BNB","USDC/USDT","PAX/TUSD","USDC/TUSD","USDC/PAX","BUSD/USDT","BUSD/NGN","BUSD/RUB","BUSD/TRY","USDT/TRY","USDT/RUB","USDT/ZAR","BUSD/ZAR","USDT/IDRT","BUSD/IDRT","USDT/UAH","BUSD/BIDR","USDT/BIDR","DAI/BNB","DAI/BTC","DAI/USDT","DAI/BUSD","USDT/BKRW","BUSD/BKRW","USDT/DAI","BUSD/DAI","PAXG/BNB","PAXG/BTC","PAXG/BUSD","PAXG/USDT","USDT/NGN","USDT/BRL","BUSD/BRL","SUSD/BTC","SUSD/ETH","SUSD/USDT","BUSD/BVND","USDT/BVND","USDC/BUSD","TUSD/BUSD","PAX/BUSD","BUSD/VAI","BUSD/UAH","USDP/BUSD","USDP/USDT","UST/BTC","UST/BUSD","UST/USDT","BUSD/PLN","BUSD/RON","NMR/BTC","NMR/BUSD","NMR/USDT","FOR/BTC","FOR/BUSD","JUV/BTC","JUV/BUSD","JUV/USDT","PSG/BTC","PSG/BUSD","PSG/USDT","OG/BTC","OG/USDT","ATM/BTC","ATM/USDT","ASR/BTC","ASR/USDT","ACM/BTC","ACM/BUSD","ACM/USDT","BAR/BTC","BAR/BUSD","BAR/USDT","ATM/BUSD","FOR/USDT","LAZIO/TRY","LAZIO/EUR","LAZIO/BTC","LAZIO/USDT","LAZIO/BUSD","CITY/BTC","CITY/BNB","CITY/BUSD","CITY/USDT","PORTO/BTC","PORTO/USDT","PORTO/TRY","PORTO/EUR","SANTOS/BTC","SANTOS/USDT","SANTOS/BRL","SANTOS/TRY","FOR/BNB","ALPINE/EUR","ALPINE/TRY","ALPINE/USDT","ALPINE/BTC","ALPINE/BUSD","SANTOS/BUSD","PORTO/BUSD","GAL/USDT","GAL/BUSD","GAL/BNB","GAL/BTC","GAL/EUR","GAL/TRY","GAL/ETH","GAL/BRL","OG/BUSD","ASR/BUSD"],
    //  "errors":{},
    //  "length":5,
    //  "method":["VolumePairList","AgeFilter"]}
    
    if(!empty($coin)) {
      $params = array();
      $params['pairs_to_delete'] = ["$coin/$stake"];

      return $this->call('DELETE', 'blacklist', $params);  
    } else {
      
      return "Please provide pair(s) to be removed from blacklist.";  
    }
  }

  function delete_lock($lockid=0)
  {
    return $this->call('DELETE', "locks/$lockid");
  }

  function delete_lock_pair($coin='', $stake='', $lockid=0)
  {
    $params = array();
    if(!empty($coin)) {
      $params['pair'] = "$coin/$stake";
    }

    if(intval($lockid) > 0) {
      $params['lockid'] = intval($lockid);
    }

    return $this->call('POST', "locks/delete", $params);
  }

  function delete_trade($trade_id=0)
  {
    // {"cancel_order_count":0,"result":"success","result_msg":"Deleted trade 6. Closed 0 open orders.","trade_id":6}
    return $this->call('DELETE', "trades/$trade_id");
  }

  function forceenter($coin='', $stake='', $direction='long', $rate=0)
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
    if(!empty($coin)) {
      $params = array();
      $params['pair'] = "$coin/$stake";
      $params['side'] = $direction;
      if($rate > 0){
        $params['price'] = $rate;
      }
      return $this->call('POST', 'forceenter', $params);  
    }
    
  }

  function forceexit($trade_id='')
  {
    // {"result":"Created sell orders for all open trades."}
    $params = array();
    $params['tradeid'] = empty($trade_id)?"all":$trade_id;
    return $this->call('POST', 'forceexit', $params);
  }

  function get_strategy($strategy='')
  {
    if(!empty($strategy)) {
      return $this->call('GET', "strategy/$strategy");  
    } else {
      return "Please input strategy.";
    }
    
  }

  function health()
  {
    // {"last_process":"2023-03-24T15:11:49.163613+00:00","last_process_ts":1679670709}
    return $this->call('GET', 'health');
  }

  // not working
  function list_available_pairs($timeframe='', $stake='', $candle_type='')
  {
    $params=array();
    if(!empty($timeframe)) {
      $params['timeframe'] = $timeframe;
    }

    if(!empty($stake)) {
      $params['stake_currency'] = $stake;
    }

    if(!empty($candle_type)) {
      $params['candletype'] = $candle_type;
    }
    return $this->call('GET', 'available_pairs', $params);  
    
  }

  function list_freqaimodels()
  {
    // {"freqaimodels":[]}
    return $this->call('GET', 'freqaimodels');  
    
  }

  function list_strategies()
  {
    // {"strategies":["ADXMomentum","ADXMomentumHO","ASDTSRockwellTrading","AdxSmas","AlphaTrend","Apollo11","AverageStrategy","AwesomeMacd","BBRSITV","BBRSITV","BBRSITV","BBRSITV1","BBRSITV2","BBRSITV3","BBRSITV4","BBRSITV5","BBRSITV5_TSL3D","BBRSITV_15m","BBRSITV_1a","BBRSITV_1b","BBRSITV_1c","BBRSITV_1d","BBRSITV_1e"]}
    return $this->call('GET', 'strategies');  
    
  }

  function locks($api_ip='')
  {
    // {"lock_count":0,"locks":[]}
    return $this->call('GET', 'locks');  
  }

  function logs($limit=0)
  {
    $params = array();
    if(intval($limit) > 0) {
      $params['limit'] = intval($limit);
    }
    return $this->call('GET', 'logs', $params);
  }

  function monthly($months=7)
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
    return $this->call('GET', 'monthly', array('timescale'=> $months));
  }

  function pair_candles($coin='', $stake='', $timeframe='', $limit=0)
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
    if(!empty($coin) and !empty($stake) and !empty($timeframe)) {
      $params = array();
      $params['pair'] = "$coin/$stake";
      $params['timeframe'] = $timeframe;
      if(intval($limit) > 0) {
        $params['limit'] = intval($limit);
      }
      return $this->call('GET', 'pair_candles', $params);  
    } else {
      return "Please input coin, stake, and timeframe";
    }
    
  }

  function pair_history($coin='', $stake='', $timeframe='', $timerange='', $strategy='')
  {
    if(!empty($coin) and !empty($stake) and !empty($timeframe) and !empty($timerange) and !empty($strategy)) {
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

  function performance()
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

  function ping(){
    // {"status": "pong"}
    return $this->call('GET', 'ping');
  }

  function profit($days=0)
  {
    // {
    //   "profit_closed_coin":22.33144659,
    //   "profit_closed_percent_mean":1.09,
    //   "profit_closed_ratio_mean":0.010933443791902269,
    //   "profit_closed_percent_sum":115.89,
    //   "profit_closed_ratio_sum":1.1589450419416403,
    //   "profit_closed_percent":31.9,
    //   "profit_closed_ratio":0.31902066557142855,
    //   "profit_closed_fiat":22.327985215778547,
    //   "profit_all_coin":22.33144659,
    //   "profit_all_percent_mean":1.09,
    //   "profit_all_ratio_mean":0.010933443791902269,
    //   "profit_all_percent_sum":115.89,
    //   "profit_all_ratio_sum":1.1589450419416403,
    //   "profit_all_percent":31.9,
    //   "profit_all_ratio":0.31902066557142855,
    //   "profit_all_fiat":22.327985215778547,
    //   "trade_count":106,
    //   "closed_trade_count":106,
    //   "first_trade_date":"2022-11-11 03:30:52",
    //   "first_trade_humanized":"8 months ago",
    //   "first_trade_timestamp":1668137452771,
    //   "latest_trade_date":"2023-07-04 20:00:24",
    //   "latest_trade_humanized":"3 hours ago",
    //   "latest_trade_timestamp":1688500824794,
    //   "avg_duration":"2:59:09",
    //   "best_pair":"CREAM/BUSD",
    //   "best_rate":37.21,
    //   "best_pair_profit_ratio":0.3721359188638739,
    //   "winning_trades":68,
    //   "losing_trades":38,
    //   "profit_factor":1.6792847657671186,
    //   "expectancy":0.24351718018066526,
    //   "max_drawdown":0.11879296052534748,
    //   "max_drawdown_abs":12.031613539999995,
    //   "trading_volume":4740.130720400002,
    //   "bot_start_timestamp":1668137452771,
    //   "bot_start_date":"2022-11-11 03:30:52"
    // }
    $params = array();
    if($days>0) {
      $params['timescale'] = $days;
    }
    return $this->call('GET', 'profit', $params);
  }

  function reload_config()
  {
    return $this->call('POST', 'reload_config');
  }

  function show_config()
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

  function start()
  {
    // {"status":"starting trader ..."}
    return $this->call('POST', 'start');
  }

  function stats()
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

  function status()
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
    //     "open_order":null
    //   }
    // ]
    return $this->call('GET', 'status');
  }

  function stop()
  {
    // {"status":"stopping trader ..."}
    return $this->call('POST', 'stop');
  }

  function stopbuy()
  {
    // {"status":"No more entries will occur from now. Run /reload_config to reset."}
    return $this->call('POST', 'stopbuy');
  }

  function stopentry()
  {
    // {"status":"No more entries will occur from now. Run /reload_config to reset."}
    return $this->call('POST', 'stopentry');
  }

  function sysinfo()
  {
    // {"cpu_pct":[9.1,1.0,11.9,1.0],"ram_pct":34.8}
    return $this->call('GET', 'sysinfo');
  }

  function trade($trade_id=0)
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

  function trades($limit=NULL, $offset=NULL, $order_by_id = False)
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
    if (isset($limit)){
      $params['limit'] = $limit;
    }
    if (isset($offset)){
      $params['offset'] = $offset;
    }
    $params['order_by_id'] = $order_by_id;
    return $this->call('GET', 'trades', $params);
  }

  function version(){
    // {"version":"2023.3.dev"}
    return $this->call('GET', 'version');
  }

  function weekly($weeks=7)
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
    return $this->call('GET', 'weekly', array('timescale'=> $weeks));
  }

  function whitelist($api_ip='')
  {
    // {"whitelist":["BTC/BUSD","ETH/BUSD","XRP/BUSD","SOL/BUSD","CFX/BUSD","BNB/BUSD","AGIX/BUSD","LTC/BUSD","MATIC/BUSD","MASK/BUSD","FTM/BUSD","ADA/BUSD","APT/BUSD","SHIB/BUSD","OP/BUSD","LUNC/BUSD","MAGIC/BUSD","LINK/BUSD","FIL/BUSD","TRX/BUSD","AVAX/BUSD","GALA/BUSD","STX/BUSD","LUNA/BUSD","LDO/BUSD","JOE/BUSD","HOOK/BUSD","ATOM/BUSD","GFT/BUSD","MINA/BUSD","DYDX/BUSD","NEAR/BUSD","CKB/BUSD","DOT/BUSD","KEY/BUSD","FTT/BUSD","BNX/BUSD","APE/BUSD","GMT/BUSD","LOOM/BUSD","ETC/BUSD","USTC/BUSD","FET/BUSD","UNI/BUSD","SNM/BUSD","RUNE/BUSD","SRM/BUSD","GRT/BUSD","MANA/BUSD","RNDR/BUSD"],"length":50,"method":["VolumePairList","AgeFilter"]}
    
    return $this->call('GET', 'whitelist');  
  }
}