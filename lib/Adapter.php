<?php

declare(encoding='UTF-8');
namespace DataModeler;

abstract class Adapter {
	private $id = NULL;
	private $priority = -1;
	
	public function __construct() {
		$this->setId(sha1(get_class($this)));
	}
	
	public function __destruct() {
		
	}
	
	
	public function getId() {
		return $this->id;
	}
	
	
	public function getPriority() {
		return $this->priority;
	}
	
	
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	
	public function setPriority($priority) {
		$priority = intval($priority);
		$this->priority = $priority;
		return $this;
	}
	
	
	abstract public function write(Model $model);
}