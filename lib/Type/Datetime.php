<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class DatetimeType extends Type {
	
	private $now = NULL;

	public function __construct() {
		parent::__construct();
		
		$this->now = date('Y-m-d G:i:s', time());
		$this->default = NULL;//$this->now;
		$this->value = NULL;//$this->now;
	}
	
	public function setDefault($default) {
		$this->default = ( !$this->isDate($default) ? $this->now : $default );
		return $this;
	}
	
	public function setValue($value) {
		$this->value = ( !$this->isDate($value) ? $this->now : $value );
		return $this;
	}
	
	private function isDate($value) {
		$parsedDate = date_parse($value);
		return ( count($parsedDate['errors']) > 0 ? false : true );
	}
	
}