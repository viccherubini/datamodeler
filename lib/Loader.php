<?php

declare(encoding='UTF-8');
namespace DataModeler;

use \DataModeler\Adapter,
	\DataModeler\Model;

class Loader {
	
	private $adapterList = array();
	
	public function __construct() {
		
	}
	
	public function __destruct() {
		
	}
	
	public function attachAdapter(Adapter $adapter) {
		$this->adapterList[] = $adapter;
		return $this;
	}
	
	public function getAdapterList() {
		return $this->adapterList;
	}
	
	
	public function load(Model $model) {
		$this->checkAdapterListLength();
		
		
	}
	
	
	
	
	private function hasAdapterList() {
		return ( count($this->getAdapterList()) > 0 ? true : false );
	}
	
	private function checkAdapterListLength() {
		if ( !$this->hasAdapterList() ) {
			throw new \DataModeler\Exception("At least one Adapter must be attached before any models can be loaded.");
		}
		return true;
	}
}