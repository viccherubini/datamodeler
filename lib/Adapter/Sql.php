<?php

declare(encoding='UTF-8');
namespace DataModeler\Adapter;

use DataModeler\Adapter;

class Sql extends Adapter {
	
	private $db = NULL;
	private $sql = NULL;
	private $statement = NULL;
	private $statement_execute = false;
	
	
	public function attachDb(\PDO $db) {
		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
		
		$this->db = $db;
		return $this;
	}
	
	
	public function query($sql, $input_parameters = array(), $driver_options = array()) {
		$this->sql = $sql;
		
		$this->hasSql();
		$this->hasDb();
		
		$this->statement = $this->db->prepare($sql, $driver_options);
		$this->handlePreparedStatementResult();
		
		$this->statement_execute = $this->statement->execute($input_parameters);
		$this->handlePreparedStatementExecute();
		
		return (clone $this->statement);
	}
	
	
	public function rawQuery($sql) {
		$this->sql = $sql;
		
		$this->hasSql();
		$this->hasDb();
		
		$result = $this->getDb()->query($sql, \PDO::FETCH_ASSOC);
		
		return $result;
	}
	
	
	public function rawExecute($sql) {
		$this->sql = $sql;
		
		$this->hasSql();
		$this->hasDb();
		$this->preventSelectStatementExecution();
		
		$row_count = $this->getDb()->exec($sql);
		
		return $row_count;
	}
	
	
	public function getDb() {
		return $this->db;
	}
	
	
	
	private function handlePreparedStatementResult() {
		if ( false === $this->statement ) {
			$error_info = $this->db->errorInfo();
			$error_string = $error_info[2];
			throw new \DataModeler\Exception("An error occurred when executing {$this->sql}. Driver said {$error_string}.");
		}
		return true;
	}

	
	private function handlePreparedStatementExecute() {
		if ( false === $this->statement_execute ) {
			$error_info = $this->statement->errorInfo();
			$error_string = $error_info[2];
			throw new \DataModeler\Exception("An error occurred when executing {$this->sql}. The prepared statement failed to execute. Driver said {$error_string}.");
		}
		return true;
	}
	
	
	private function hasDb() {
		if ( NULL === $this->getDb() ) {
			throw new \DataModeler\Exception("Database object has not yet been attached to Sql Adapter.");
		}
		return true;
	}
	
	
	private function hasSql() {
		if ( true === empty($this->sql) ) {
			throw new \DataModeler\Exception("The SQL query is empty and can not be executed.");
		}
		return true;
	}
	
	
	private function isSelectStatement() {
		if ( 0 === stripos($this->sql, 'SELECT') ) {
			return true;
		}
		return false;
	}


	private function preventSelectStatementExecution() {
		if ( true === $this->isSelectStatement() ) {
			throw new \DataModeler\Exception("SELECT statements will return an incorrect number of rows, use query() or a prepared statement instead.");
		}
		return true;
	}
}