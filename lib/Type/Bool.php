<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class Bool extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->defaultValue = false;
		$this->value = false;
	}
	
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = ( !is_bool($defaultValue) ? false : $defaultValue );
		return $this;
	}
	
	public function setValue($value) {
		$this->value = ( !is_bool($value) ? false : $value );
		return $this;
	}
	
}