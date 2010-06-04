<?php

declare(encoding='UTF-8');
namespace DataModeler;

class Writer {
	
	private $adapterList = array();
	
	public function __construct() {
		
		
	}
	
	
	public function __destruct() {
		
	}
	
	public function getAdapterList() {
		return $this->adapterList;
	}
	
}