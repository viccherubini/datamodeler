<?php

declare(encoding='UTF-8');
namespace DataModeler;

abstract class Type {

	private $fieldName = NULL;
	private $maxlength = -1;
	
	public function __construct() {
		
	}
	
	public function __destruct() {
		
	}
	
	public function setFieldName($fieldName) {
		$this->fieldName = $fieldName;
		return $this;
	}
	
	public function getFieldName() {
		return $this->fieldName;
	}
	
	public function setMaxlength($maxlength) {
		$this->maxlength = intval($maxlength);
		return $this;
	}
	
	public function getMaxlength() {
		return $this->maxlength;
	}
}