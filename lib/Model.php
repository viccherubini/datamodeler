<?php

declare(encoding='UTF-8');
namespace DataModeler;

/**
 * Abstract Model class for building FAT, intelligent models. The model is
 * your primary in memory data store. This class should be extended to another
 * class that is a 1:1 relationship with a table or document.
 * 
 * @author vmc <vmc@leftnode.com>
 * @version 0.0.10
 */
abstract class Model {
	private $datetype = NULL;
	private $hasdate = NULL;
	private $id = NULL;
	private $model = array();
	private $pkey = NULL;
	private $table = NULL;
	
	const DATETYPE_TIMESTAMP = 2;
	const DATETYPE_NOW = 4;
	
	
	public function __construct() {
		
	}
	
	
	public function __destruct() {
		$this->model = array();
	}
	
	
	public function __call($method, $argv) {
		$argc = count($argv);
		
		$k = $this->convertCamelCaseToUnderscores($method);
		
		if ( 0 === $argc ) {
			/* If the length is 0, assume this is a get() */
			$v = $this->__get($k);
			return $v;
		} else {
			$v = current($argv);
			
			/* If the key is the pkey of the object, don't allow that to be set. */
			$pkey = $this->pkey();
			if ( $k === $pkey ) {
				$this->id($v);
			} else {
				/* Else assume its a set with the first element of $argv. */
				$this->__set($k, $v);
			}
			
			return $this;
		}
	}
	
	
	public function __get($key) {
		$pkey = $this->pkey();
		
		if ( $pkey === $key ) {
			return $this->id();
		} else {
			$model = $this->model();
			if ( true === isset($model[$key]) ) {
				return $model[$key];
			}
		}
		return NULL;
	}
	
	
	public function __set($key, $value) {
		$pkey = $this->pkey();
		
		if ( $key === $pkey ) {
			$this->id($value);
		} else {
			if ( true === $this->isValidField($key) ) {
				$this->model[$key] = $value;
			}
		}
		return true;
	}
	
	
	public function datetype($datetype = 0) {
		$datetype = intval($datetype);
		if ( $datetype > 0 ) {
			if ( $datetype != self::DATETYPE_TIMESTAMP && $datetype != self::DATETYPE_NOW ) {
				$datetype = self::DATETYPE_TIMESTAMP;
			}
			$this->datetype = $datetype;
		}
		return $this->datetype;
	}


	public function hasdate($hasdate = NULL) {
		if ( true === $hasdate || false === $hasdate ) {
			$this->hasdate = $hasdate;
		}
		return $this->hasdate;
	}
	

	public function id($id = NULL) {
		if ( false === empty($id) ) {
			$this->id = $id;
		}
		return $this->id;
	}


	public function model(array $model = array()) {
		if ( false !== current($model) || ( count($model) > 0 ) ) {
			$pkey = $this->pkey();
			if ( true === isset($model[$pkey]) ) {
				unset($model[$pkey]);
			}
			$this->model = $model;
		}
		return $this->model;
	}
	
	
	public function pkey($pkey = NULL) {
		$pkey = trim($pkey);
		if ( false === empty($pkey) ) {
			$pkey = $this->removeBackticks($pkey);
			$this->pkey = $pkey;
		}
		return $this->pkey;
	}


	public function table($table = NULL) {
		$table = trim($table);
		if ( false === empty($table) ) {
			$table = $this->removeBackticks($table);
			$this->table = $table;
		}
		return $this->table;
	}

	
	private function convertCamelCaseToUnderscores($v) {
		$v = substr($v, 3);
		$v = strtolower(substr($v, 0, 1)) . substr($v, 1);
		$v = preg_replace('/[A-Z]/', '_\\0', $v);
		$v = strtolower($v);
		
		return $v;
	}
	
	private function isValidField($field) {
		if ( 1 === preg_match('/^[a-z0-9_\-.]+$/i', $field) ) {
			return true;
		}
		return false;
	}
	
	
	private function removeBackticks($value) {
		return str_replace('`', NULL, $value);
	}
	
	
	
}