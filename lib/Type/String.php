<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class String extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->defaultValue = '';
		$this->value = '';
	}
	
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = $this->truncate($defaultValue);
		return $this;
	}
	
	public function setValue($value) {
		$this->value = $this->truncate($value);
		return $this;
	}
	
	private function truncate($string) {
		$string = strval($string);
		$maxlength = $this->getMaxlength();
		if ( $maxlength > 0 ) {
			$string = substr($string, 0, $maxlength);
		}
		return $string;
	}
	
}