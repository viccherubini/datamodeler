<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class IntegerType extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->default = 0;
		$this->value = 0;
	}
	
	public function value($v) {
		return intval($v);
	}
}