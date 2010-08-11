<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class DateType extends Type {

	private $today = NULL;

	public function __construct() {
		parent::__construct();
		
		$this->today = date('Y-m-d', time());
		$this->default = $this->today;
		$this->value = $this->today;
	}
	
	public function value($v) {
		return ( !$this->isDate($v) ? $this->today : $v );
	}
	
	private function isDate($value) {
		$parsedDate = date_parse($value);
		return ( count($parsedDate['errors']) > 0 ? false : true );
	}
	
}