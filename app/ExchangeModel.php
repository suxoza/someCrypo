<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Http\Helper; 
use Illuminate\Support\Facades\DB;

class ExchangeModel extends Model{


	public $service;
	public function __construct(){
		$this->service = Helper::getInstance();
	}

	public function getRates(string $from, array $to) : ?Array {
		$rates = $this->service->rates();
		$currencyAsKey = strtoupper($from);
		$result = [];
		foreach($to as $value){
			$cripto = strtolower($value);
			$vls = $this->service->getCurrencyBlockChain($cripto);
			if(isset($vls->{$currencyAsKey}))
				$result[$cripto] = $vls->{$currencyAsKey}->last;
			else // get currency from your api
				$result[$cripto] = $vls->USD->last * $rates[$from];
		}
		return $result;
		
	}

	public function saveRate(int $fiatID, array $rates){
		$insertArray = [];
		foreach($rates as $key => $value){
			$criptoID = array_search($key, $this->service->currencyList['crypo']);
			$insertArray[] = [
				'cripoID' => $criptoID,
				'fiatID' => $fiatID,
				'price' => $value
			];
		}
		DB::table('exchangeRates')->insert($insertArray);
		$this->pre($rates);
	}

	public function getChangeInfo(string $cryptoCurrency, int $fromDate, int $toDate){
		$this->criptoID = array_search($cryptoCurrency, $this->service->currencyList['crypo']);
		$from = $this->selectTemp($fromDate, 'from');
		if(!$from)
			throw new \Exception("can`t find records!");
		$to = $this->selectTemp($toDate, 'to');
		if(!$to)
			throw new \Exception("can`t find records in this range!");
	
		$desc = ($from->price < $to->price)?'increase':(($from->price > $to->price)?'decrease':'same');
		echo $desc.' -> '.abs(round((1 - $from->price / $to->price) * 100, 4)).'%';
		$this->pre($from);
		$this->pre($to);
	}

	private function selectTemp(int $timestamp, string $action = 'from') : ?object {
		$result = DB::table("exchangeRates")
					  ->select("id", "price", "insert_date")
					  ->where("cripoID", DB::Raw($this->criptoID))
					  ->where("fiatID", DB::Raw(6));
		if($action == 'from')
			$result = $result->whereRaw("insert_date >= from_unixtime($timestamp)")->orderByRaw("id asc");
		else
			$result = $result->whereRaw("insert_date <= from_unixtime($timestamp)")->orderByRaw("id desc");
		//echo $this->criptoID."<br>".$result->toSql();
		return $result->first();
	}

	private function getQuery(){
		DB::enableQueryLog();
		dd(DB::getQueryLog()); 
	}

	public function pre($args){
		echo "<pre>";
		print_r($args);
		echo "</pre>";
	}
	
}