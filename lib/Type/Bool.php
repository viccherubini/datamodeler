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
	
	public function value($v) {
		return ( !is_bool($v) ? false : $v );
	}
}