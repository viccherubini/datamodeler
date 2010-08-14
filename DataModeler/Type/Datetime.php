<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class DatetimeType extends Type {
	
	private $now = NULL;

	public function __construct() {
		parent::__construct();
		
		$this->now = date('Y-m-d G:i:s', time());
		$this->default = $this->now;
		$this->value = $this->now;
	}
	
	public function value($v) {
		return ( !$this->isDate($v) ? $this->now : $v );
	}
	
	private function isDate($value) {
		$parsedDate = date_parse($value);
		return ( count($parsedDate['errors']) > 0 ? false : true );
	}
	
}