<?php

declare(encoding='UTF-8');
namespace DataModeler\Adapter;

require_once 'lib/Exception.php';

class Sql {
	
	private $db;
	
	public function __construct() {
		
	}
	
	public function __destruct() {
		
		
	}
	
	public function attachDb(\PDO $db) {
		$this->db = $db;
		return $this;
	}
	
	
	public function rawQuery($sql) {
		$this->hasDb();
		
		$result = $this->getDb()->query($sql);
		
		
		$this->getDb()->closeCursor();
	}
	
	
	public function rawExecute($sql) {
		$this->hasDb();
		
		$row_count = $this->getDb()->exec($sql);
		return $row_count;
	}
	
	
	public function getDb() {
		return $this->db;
	}
	
	
	
	private function hasDb() {
		if ( NULL === $this->getDb() ) {
			throw new DataModeler\Exception("Database object has not yet been attached to Sql Adapter.");
		}
		
		return true;
	}
}