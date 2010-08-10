<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class BoolType extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->default = false;
		$this->value = false;
	}
	
	public function setDefault($default) {
		$this->default = ( !is_bool($default) ? false : $default );
		return $this;
	}
	
	public function setValue($value) {
		$this->value = ( !is_bool($value) ? false : $value );
		return $this;
	}
	
}