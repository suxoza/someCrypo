<?php

use Illuminate\Http\Request;

Route::get('exchangeRates/{from}/{to?}', 'exchangeController@getRates')->where(['from' => '[a-zA-Z]{3,4}','to' => '[a-zA-Z]{2,}']);
Route::post('saveExchangeRates', 'exchangeController@saveRates');
Route::get('getChangeInfo/{cryptoCurrency}/{fromDate}/{toDate?}', 'exchangeController@getChangeInfo')->where(
	['fromDate' => '[0-9]{10,13}+', 'toDate' => "[0-9]{10,13}+"]
);
