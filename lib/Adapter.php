<?php

declare(encoding='UTF-8');
namespace DataModeler;

abstract class Adapter {
	private $id = NULL;
	
	public function __construct() {
		$this->setId(sha1(get_class($this)));
	}
	
	public function __destruct() {
		
	}
	
	
	public function getId() {
		return $this->id;
	}
	
	
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
}