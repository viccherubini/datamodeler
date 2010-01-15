<?php

abstract class DataModel {
	private $adapter = NULL;
	
	public function __construct(DataAdapter $adapter) {
		
	}
	
	public function __destruct() {
		
	}
	
	public function setDataAdapter(DataAdapter $adapter) {
		$this->adapter = $adapter;
		return $this;
	}
	
	public function load(DataObject $object, $pkey) {
		$this->hasDataAdapter();
		
		
	}
	
	public function save(DataObject $object) {
		$this->hasDataAdapter();
		
	}
	
	private function hasDataAdapter() {
		if ( NULL === $this->adapter ) {
			throw new Exception('No DataAdapter has been set. Please set one first.');
		}
		return true;
	}
}