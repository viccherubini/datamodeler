<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class StringType extends Type {
	
	public function __construct() {
		parent::__construct();
		
		$this->default = '';
		$this->value = '';
	}
	
	public function value($v) {
		return $this->truncate($v);
	}
	
	private function truncate($string) {
		$string = strval($string);
		if ( $this->maxlength > 0 ) {
			$string = substr($string, 0, $this->maxlength);
		}
		return $string;
	}
	
}