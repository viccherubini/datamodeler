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
			$this->model = $model;
		}
		return $this->model;
	}
	
	
	public function pkey($pkey = NULL) {
		
	}


	public function table($table = NULL) {
		$table = trim($table);
		if ( false === empty($table) ) {
			$table = str_replace('`', NULL, $table);
			$this->table = $table;
		}
		return $this->table;
	}


	
}