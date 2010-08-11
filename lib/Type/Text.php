<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class TextType extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->default = '';
		$this->value = '';
	}
	
	public function value($v) {
		return strval($v);
	}
}