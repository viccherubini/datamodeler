<?php

declare(encoding='UTF-8');
namespace DataModeler;

abstract class Type {

	public $field = NULL;
	public $maxlength = -1;
	public $precision = -1;

	protected $data = array();

	const VALUE_NULL = 'NULL';

	public function __construct() {
		$this->data = array('default' => NULL, 'value' => NULL);
	}
	
	public function __destruct() {
		
	}
	
	public function __set($k, $v) {
		$k = strtolower($k);
		
		if ( array_key_exists($k, $this->data) ) {
			if ( self::VALUE_NULL == strtoupper($v) ) {
				$v = NULL;
			} else {
				$v = $this->value($v);
			}
			
			$this->data[$k] = $v;
		}
		
		return true;
	}
	
	public function __get($k) {
		if ( array_key_exists($k, $this->data) ) {
			return $this->data[$k];
		}
		return NULL;
	}

	abstract public function value($v);
}