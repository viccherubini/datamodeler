<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class FloatType extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->default = 0.0;
		$this->value = 0.0;
	}
	
	public function value($v) {
		return $this->roundTo($v);
	}
	
	private function roundTo($value) {
		$value = floatval($value);
		if ( $this->precision > -1 ) {
			$value = round($value, $this->precision);
		}
		return $value;
	}
	
}