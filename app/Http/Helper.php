<?php
namespace App\Http;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Ixudra\Curl\Facades\Curl;

use App\Http\models\template;

final class Helper{

	private static $instance;
	private $keys = ['crypo', 'fiat'];
	public $currencyList = [];
	private function __construct(){
		//echo "init...\n";
		$this->getCurrencyList();
	}

	public static function getInstance() : Helper {
    	if(self::$instance == null)
    		self::$instance = new Helper();
    	return self::$instance;
  	}


  	public function getCurrencyList() : void {
  		//Cache::delete("currency");
        $getValue = Cache::get('currency');

        
        if(!$getValue){
        	$expiresAt = now()->addDays(10);
            $select = DB::table("currencyTemplate")->get();
            foreach($select as $value)
            	$this->currencyList[$this->keys[$value->target]][$value->id] = $value->title;
           	Cache::add('currency', $this->currencyList, $expiresAt);
        }else
     		$this->currencyList = $getValue;
        
    }

    public function getCurrencyBlockChain(string $crypo) : object {
    	return json_decode(Curl::to(sprintf('https://www.bitgo.com/api/v2/%s/market/latest/', $crypo))->get())
    			->latest->currencies;
    }

    public function rates() : ?array {

    	$getValue = Cache::get("rates");
    	if(!$getValue){
    		$expiresAt = now()->addDays(10);
    		$response = json_decode(Curl::to('https://api.exchangeratesapi.io/latest?base=USD')->get())->rates;
    		$gel = $this->getCurrencyGEL();
    		$getValue = [];
    		foreach($this->currencyList['fiat'] as $value)
    			$getValue[$value] = $response->{strtoupper($value)}??0;
    		$getValue['gel'] = $this->getCurrencyGEL();
    		Cache::add('rates', $getValue, $expiresAt);
    	}
        return $getValue;
        
    }

    public function getCurrencyGEL() : ?float {
		$wsdl = 'http://nbg.gov.ge/currency.wsdl';
		$client = new \SoapClient($wsdl, ['cache_wsdl' => WSDL_CACHE_NONE]);
		return $client->GetCurrency('USD');
    		


    }
  	

  	
}