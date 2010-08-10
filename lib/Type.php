<?php

declare(encoding='UTF-8');
namespace DataModeler;

abstract class Type {

	public $field = NULL;
	public $maxlength = -1;
	public $precision = -1;

	protected $data = array();

	public function __construct() {
		$this->data = array('default' => NULL, 'value' => NULL);
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
		if ( array_key_exists($k, $this->data) ) {
			return $this->data[$k];
		}
		return NULL;
	}

	abstract public function setDefault($default);
	abstract public function setValue($value);
	
}