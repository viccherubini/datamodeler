<?php

abstract class DataObject {
	protected $id = 0;
	protected $pkey = NULL;
	protected $table = NULL;
	protected $model = array();
	protected $method_cache = array();
	protected $has_date = true;
	
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
				$this->method_cache[$k] = $v;
				return $v;
			} else {
				/* Else assume its a set with the first element of $argv. */
				$v = current($argv);
				$this->__set($k, $v);
				$this->method_cache[$k] = $v;
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
	
	public function getHasDate() {
		return $this->has_date;
	}
	
	/**
	 * SETTERS
	 */
	
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * CLASS MODIFIERS
	 */
	
	public function init() {
		$this->id = 0;
		$this->model = array();
		$this->method_cache = array();
	}
}