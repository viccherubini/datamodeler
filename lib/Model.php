<?php

declare(encoding='UTF-8');
namespace DataModeler;

abstract class Model {
	private $id = NULL;
	private $pkey = NULL;
	private $datetype = NULL;
	private $table = NULL;
	private $model = array();
	
	const DATETYPE_TIMESTAMP = 2;
	const DATETYPE_NOW = 4;
	
	public function __construct() {
		
	}
	
	
	public function __destruct() {
		
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

	
	private function removeBackticks($value) {
		return str_replace('`', NULL, $value);
	}
	
	
	private function isValidField($field) {
		if ( 1 === preg_match('/[a-z0-9_\-.]+/i', $field) ) {
			return true;
		}
		return false;
	}
}