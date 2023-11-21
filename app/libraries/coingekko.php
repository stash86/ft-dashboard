<?php
require_once(__DIR__.'/../../vendor/autoload.php');
use Codenixsv\CoinGeckoApi\CoinGeckoClient;

class Coingekko
{
  private $coingekko;
  
  private $coingecko_mapping = [
    'btc'=> 'bitcoin',
    'eth'=> 'ethereum',
    'bnb'=> 'binancecoin',
    'sol'=> 'solana',
    'usdt'=> 'tether',
    'busd'=> 'binance-usd',
    'tusd'=> 'true-usd',
    'usdc'=> 'usd-coin',
  ];

  private $list_stake = 'bitcoin,ethereum,binancecoin,solana,tether,binance-usd,true-usd,usd-coin';
	
  public function __construct() {
    $this->coingekko = new CoinGeckoClient();
	}

  function coin_list() {
    // Just an example. The list would be long
    // [{"id":"bitcoin","symbol":"btc","name":"Bitcoin"},{"id":"tether","symbol":"usdt","name":"Tether"}]
    
    return $this->coingekko->coins()->getList();
  }

  function getPrice($coin_ids, $fiats) {
    // {"binancecoin":{"usd":257.94},"binance-usd":{"usd":0.998985},"bitcoin":{"usd":37359},"ethereum":{"usd":2012.44},"solana":{"usd":55.95},"tether":{"usd":1},"true-usd":{"usd":0.999369},"usd-coin":{"usd":1}}
    
    return $this->coingekko->simple()->getPrice($coin_ids, $fiats);
  }

  function getPriceProgrammed() {
    // {"btc":37356,"eth":2013.1,"bnb":258.02,"sol":56.07,"usdt":1.001,"busd":0.999826,"tusd":0.999283,"usdc":0.999853}

    $data = $this->getPrice($this->list_stake, 'usd');
    $new_data = [];

    foreach ($this->coingecko_mapping as $key => $value) {
      $new_data[$key] = $data[$value]['usd'];
    }

    return $new_data;
  }

}