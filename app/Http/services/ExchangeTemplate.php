<?php
namespace App\Http\models;

final class ExchangeTemplate{

	private $id, $target, $title, $fullName;
	public function __construct(Object $params){
		list($this->id, $this->target, $this->title, $this->fullName) = array_values(get_object_vars($params));
	} 

	public function __toString(){
		return [
			'title' => $this->title,
			'fullName' => $this->fullName
		];
	}
}