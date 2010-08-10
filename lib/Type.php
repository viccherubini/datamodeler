<?php

declare(encoding='UTF-8');
namespace DataModeler;

abstract class Type {

	public $field = NULL;
	public $maxlength = -1;
	public $preicision = -1;

	public function __construct() {
		
	}
	
	public function __destruct() {
		
	}
	
	public function __set($k, $v) {
		$k = strtolower($k);
		
		switch ( $k ) {
			case 'default': {
				$this->setDefault($v);
				break;
			}
			
			case 'value': {
				$this->setValue($v);
				break;
			}
		}
		
		return true;
	}
	
	public function __get($k) {
		if ( property_exists($this, $k) ) {
			return $this->$k;
		}
		return NULL;
	}

	abstract public function setDefault($default);
	abstract public function setValue($value);
	
}