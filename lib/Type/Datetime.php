<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class Datetime extends Type {
	
	private $now = NULL;

	public function __construct() {
		parent::__construct();
		
		$this->now = date('Y-m-d G:i:s', time());
		$this->defaultValue = $this->now;
		$this->value = $this->now;
	}
	
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = ( !$this->isDate($defaultValue) ? $this->now : $defaultValue );
		return $this;
	}
	
	public function setValue($value) {
		$this->defaultValue = ( !$this->isDate($defaultValue) ? $this->now : $defaultValue );
		return $this;
	}
	
	private function isDate($value) {
		$parsedDate = date_parse($value);
		return ( count($parsedDate['errors']) > 0 ? false : true );
	}
	
}