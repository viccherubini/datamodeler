<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class TypelessType extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->default = '';
		$this->value = '';
	}
	
	public function setDefault($default) {
		$this->default = $default;
		return $this;
	}
	
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}
	
}
