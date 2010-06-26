<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class Float extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->defaultValue = 0.0;
		$this->value = 0.0;
	}
	
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = $this->roundTo($defaultValue);
		return $this;
	}
	
	public function setValue($value) {
		$this->value = $this->roundTo($value);
		return $this;
	}
	
	private function roundTo($value) {
		$value = floatval($value);
		$precision = $this->getPrecision();
		if ( $precision > -1 ) {
			$value = round($value, $precision);
		}
		return $value;
	}
	
}