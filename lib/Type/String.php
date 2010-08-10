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
	
	public function setDefault($default) {
		$this->default = $this->truncate($default);
		return $this;
	}
	
	public function setValue($value) {
		$this->value = $this->truncate($value);
		return $this;
	}
	
	private function truncate($string) {
		$string = strval($string);
		if ( $this->maxlength > 0 ) {
			$string = substr($string, 0, $this->maxlength);
		}
		return $string;
	}
	
}