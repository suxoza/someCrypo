<?php

namespace App\Http\Controllers;

use App\ExchangeModel;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class exchangeController extends Controller{
    
	public $model;
    public function __construct(){
    	try{
    		$this->model = new ExchangeModel();

    		$this->fiat = $this->model->service->currencyList['fiat'];
    		$this->crypo = $this->model->service->currencyList['crypo'];
    	}catch(\Exception $ex){
    		$this->model = new Model();
    	}    	
    }

    private function callInterface(callable $callBack) : void {
    	try{
    		$callBack();
    	}catch(\Exception $ex){
    		echo $ex->getMessage();
   			//return response()->json($ex);
    	}
    }

    //callable - exchangeRates
    public function getRates(string $from, string $to = null) : void {
    	$this->callInterface(function() use ($from, $to){
    		

    		if(!in_array($from, $this->fiat))
    			throw new \Exception(sprintf("parameter 'from' must be between %s ", join(',', $this->fiat)));


    		if($to){
    			$to = (strpos($to, ',') !== false)?explode(',', $to):[$to];
    			foreach($to as $vls){
    				if(!in_array($vls, $this->crypo))
    					throw new \Exception(sprintf("parameter 'to' must be between %s ", join(',', $this->crypo)));
    			}
    		}

    		$rates = $this->model->getRates($from, $to?$to:$this->crypo);
    		$this->model->pre($rates);
    		
    	});
    }

    //callable - saveExchangeRates
    public function saveRates() {
    	$this->callInterface(function() {
    		foreach($this->fiat as $fiatID => $fiatName){
	    		$rates = $this->model->getRates($fiatName, $this->crypo);
	    		$this->model->saveRate($fiatID, $rates);
	    	}
	    	echo '-----------Saved----------------';
    	});
    	
    }

    //callable - getChangeInfo
    public function getChangeInfo(string $cryptoCurrency, int $fromDate, int $toDate = null){
    	$toDate = $toDate?:time();
    	$this->callInterface(function() use ($cryptoCurrency, $fromDate, $toDate) {
    		if($fromDate >= $toDate)
    			throw new \Exception('"fromDate" must by less then "toDate"!');

    		$diff = $this->model->getChangeInfo($cryptoCurrency, $fromDate, $toDate);
    		$this->model->pre($diff);
    	});
    }
   
}
