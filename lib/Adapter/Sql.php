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
		$result = $this->getDb()->query($sql, \PDO::FETCH_ASSOC);
		
		return $result;
	}
	
	
	public function rawExecute($sql) {
		$this->hasDb();
		$this->preventSelectStatementExecution($sql);
		
		$row_count = $this->getDb()->exec($sql);
		
		return $row_count;
	}
	
	
	public function getDb() {
		return $this->db;
	}
	
	
	
	private function hasDb() {
		if ( NULL === $this->getDb() ) {
			throw new \DataModeler\Exception("Database object has not yet been attached to Sql Adapter.");
		}
		
		return true;
	}
	
	
	private function isSelectStatement($sql) {
		if ( 0 === stripos($sql, 'SELECT') ) {
			return true;
		}
		
		return false;
	}


	private function preventSelectStatementExecution($sql) {
		if ( true === $this->isSelectStatement($sql) ) {
			throw new \DataModeler\Exception("SELECT statements will return an incorrect number of rows, use query() or a prepared statement instead.");
		}
		
		return true;
	}
}