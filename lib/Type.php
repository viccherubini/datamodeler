<?php

declare(encoding='UTF-8');
namespace DataModeler;

abstract class Type {

	private $fieldName = NULL;
	private $maxlength = -1;
	private $preicision = -1;
	
	protected $defaultValue = NULL;
	protected $value = NULL;
	
	public function __construct() {
		
	}
	
	public function __destruct() {
		
	}

	public function getDefaultValue() {
		return $this->defaultValue;
	}
	
	public function getValue() {
		return $this->value;
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
	
	public function setPrecision($precision) {
		$this->precision = intval($precision);
		return $this;
	}
	
	public function getPrecision() {
		return $this->precision;
	}
	
	abstract public function setDefaultValue($defaultValue);
	abstract public function setValue($value);
	
}