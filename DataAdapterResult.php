<?php

abstract class DataAdapterResult {
	private $result = NULL;

	public function __construct($result) {
		$this->setResult($result);
	}
	
	public function __destruct() {
		unset($this->result);
	}
	
	public function setResult($result) {
		$this->result = $result;
		return $this;
	}
	
	public function getResult() {
		return $this->result;
	}
	
	abstract public function getRowCount();

	abstract public function fetch($field=NULL);

	abstract public function fetchAll();

	abstract public function free();
}