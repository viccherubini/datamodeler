<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class Text extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->defaultValue = '';
		$this->value = '';
	}
	
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = strval($defaultValue);
		return $this;
	}
	
	public function setValue($value) {
		$this->value = strval($value);
		return $this;
	}
	
}