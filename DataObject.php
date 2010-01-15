<?php

abstract class DataObject {
	protected $id = 0;
	protected $pkey = NULL;
	protected $table = NULL;
	protected $model = array();
	protected $method_cache = array();
	
	const TABLE_ROOT = '';
	
	public function __construct() {
		$this->init();
	}
	
	public function __destruct() {
		
	}
	
	public function __call($method, $argv) {
		$argc = count($argv);
		if ( 0 === $argc && true === isset($this->method_cache[$method]) ) {
			return $this->method_cache[$method];
		} else {
			$k = substr($method, 3);
			$k = strtolower(substr($k, 0, 1)) . substr($k, 1);
			$k = preg_replace('/[A-Z]/', '_\\0', $k);
			$k = strtolower($k);
			
			if ( 0 === $argc ) {
				/* If the length is 0, assume this is a get() */
				$v = $this->__get($k);
				$this->method_cache[$method] = $v;
				return $v;
			} else {
				/* Else assume its a set with the first element of $argv. */
				$this->__set($k, current($argv));
				return $this;
			}
		}
	}
	
	public function __set($k, $v) {
		$this->model[$k] = $v;
		return true;
	}

	public function __get($k) {
		if ( true === isset($this->model[$k]) ) {
			return $this->model[$k];
		}
		return NULL;
	}
	
	
	/**
	 * GETTERS
	 */
	
	public function get() {
		return $this->model;
	}

	public function getId() {
		return $this->id;
	}
	
	public function getPkey() {
		return $this->pkey;
	}
	
	public function getTable() {
		return $this->table;
	}
	
	
	/**
	 * SETTERS
	 */
	
	public function setPkey($pkey) {
		$this->pkey = $pkey;
		return $this;
	}
	
	public function setTable($table) {
		$this->table = $table;
		return $this;
	}
	
	
	/**
	 * CLASS MODIFIERS
	 */
	
	public function init() {
		$class = strtolower(get_class($this));
		$this->id = 0;
		$this->model = array();
		$this->method_cache = array();
		$this->setTable(self::TABLE_ROOT . $class);
		$this->setPkey($class . '_id');
	}
}