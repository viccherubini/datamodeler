<?php

abstract class DataObject {
	private $id = 0;
	private $pkey = NULL;
	private $table = NULL;
	private $model = array();
	private $method_cache = array();
	private $children = array();
	private $has_date = true;
	
	const TABLE_ROOT = '';
	
	
	public function __construct() {
		$this->init();
	}
	
	public function __destruct() {
		
	}
	
	public function __call($method, $argv) {
		$argc = count($argv);
		
		$k = substr($method, 3);
		$k = strtolower(substr($k, 0, 1)) . substr($k, 1);
		$k = preg_replace('/[A-Z]/', '_\\0', $k);
		$k = strtolower($k);
		
		if ( 0 === $argc ) {
			/* If the length is 0, assume this is a get() */
			$v = $this->__get($k);
			return $v;
		} else {
			$v = current($argv);
			
			/* If the key is the pkey of the object, don't allow that to be set. */
			$pkey = $this->pkey();
			if ( $k == $pkey ) {
				$this->id($v);
			} else {
				/* Else assume its a set with the first element of $argv. */
				$this->__set($k, $v);
			}
			
			return $this;
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
	 * ANONYMOUS GETTERS AND SETTERS
	 */
	

	public function model() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->model;
		} else {
			$model = func_get_arg(0);
			if ( false === is_array($model) ) {
				$model = array();
			}
			
			$pkey = $this->pkey();
			if ( true === isset($model[$pkey]) ) {
				$id = $model[$pkey];
				$this->id($id);
				unset($model[$pkey]);
			}
			
			$this->model = $model;
			return $this;
		}
	}
	
	public function id() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->id;
		} else {
			$id = func_get_arg(0);
			$this->id = $id;
			return $this;
		}
	}
	
	public function table() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->table;
		} else {
			$table = func_get_arg(0);
			$this->table = self::TABLE_ROOT . $table;
			return $this;
		}
	}
	
	public function pkey() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->pkey;
		} else {
			$pkey = func_get_arg(0);
			$this->pkey = $pkey;
			return $this;
		}
	}
	
	public function methodCache() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->method_cache;
		} else {
			$method_cache = func_get_arg(0);
			if ( false === is_array($method_cache) ) {
				$method_cache = array();
			}
			$this->method_cache = $method_cache;
			return $this;
		}
	}
	
	public function children() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->children;
		} else {
			$children = func_get_arg(0);
			if ( false === is_array($children) ) {
				$children = array();
			}
			$this->children = $children;
			return $this;
		}
	}

	public function hasDate() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->has_date;
		} else {
			$has_date = func_get_arg(0);
			if ( false !== $has_date && true !== $has_date ) {
				$has_date = true;
			}
			$this->has_date = true;
			return $this;
		}
	}
	
	
	
	/**
	 * CLASS MODIFIERS
	 */
	
	public function init() {
		$class_name = strtolower(get_class($this));
		$this->id(0)
			->model(array())
			->methodCache(array())
			->children(array())
			->hasDate(true)
			->table($class_name)
			->pkey($class_name . '_id');
		
		return true;
	}
}