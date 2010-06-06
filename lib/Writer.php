<?php

declare(encoding='UTF-8');
namespace DataModeler;

use DataModeler\Adapter;

class Writer {
	
	private $adapterList = array();
	
	public function __construct() {
		
		
	}
	
	
	public function __destruct() {
		
	}
	
	
	public function addAdapter(Adapter $adapter) {
		$adapter_id = $adapter->getId();
		if ( false === isset($this->adapterList[$adapter_id]) ) {
			$this->adapterList[$adapter_id] = $adapter;
		}
		return $this;
	}
	
	
	public function getAdapterList() {
		return $this->adapterList;
	}
}