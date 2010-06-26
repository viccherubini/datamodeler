<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class Date extends Type {

	private $today = NULL;

	public function __construct() {
		parent::__construct();
		
		$this->today = date('Y-m-d', time());
		$this->defaultValue = $this->today;
		$this->value = $this->today;
	}
	
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = ( !$this->isDate($defaultValue) ? $this->today : $defaultValue );
		return $this;
	}
	
	public function setValue($value) {
		$this->defaultValue = ( !$this->isDate($defaultValue) ? $this->today : $defaultValue );
		return $this;
	}
	
	private function isDate($value) {
		$parsedDate = date_parse($value);
		return ( count($parsedDate['errors']) > 0 ? false : true );
	}
	
}