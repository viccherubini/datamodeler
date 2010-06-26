<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class Integer extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->defaultValue = 0;
		$this->value = 0;
	}
	
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = intval($defaultValue);
		return $this;
	}
	
	public function setValue($value) {
		$this->value = intval($value);
		return $this;
	}
	
}