<?php

declare(encoding='UTF-8');
namespace DataModeler\Adapter;

use \DataModeler\Adapter, \DataModeler\Model;

class Sql extends Adapter {
	
	private $db = NULL;
	private $driverOptions = array();
	private $sql = NULL;
	private $statement = NULL;
	private $statementExecute = false;
	private $where = NULL;


	
	public function attachDb(\PDO $db) {
		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);

		$this->db = $db;
		return $this;
	}


	public function find() {
		
	}
	
	public function findAll() {
		
	}
	
	public function insert(Model $object, array $input_parameters) {
		
		
	}
	
	
	public function query($sql, array $input_parameters, Model $model = NULL) {
		
	}
	
	public function queryFirst($sql, array $input_parameters, Model $model = NULL) {
		
	}
	
	
	
	
	
	
	
	
	
	public function rawExecute($sql) {
		$this->sql = $sql;
		
		$this->hasSql();
		$this->hasDb();
		$this->preventSelectStatementExecution();
		
		$row_count = $this->db->exec($sql);
		
		return $row_count;
	}
	
	
	
	
	public function update(Model $model, $where = NULL, array $input_parameters = array()) {
		
	}
	
	
	
	public function where($where) {
		$this->where = $where;
		return $this;
	}
	
	
	private function handlePreparedStatementResult() {
		if ( false === $this->statement ) {
			$error_info = $this->db->errorInfo();
			throw new \DataModeler\Exception("An error occurred when preparing {$this->sql}. Driver said {$error_info[2]}.");
		}
		return true;
	}

	
	private function handlePreparedStatementExecute() {
		if ( false === $this->statementExecute ) {
			$error_info = $this->statement->errorInfo();
			throw new \DataModeler\Exception("An error occurred when executing {$this->sql}. The prepared statement failed to execute. Driver said {$error_info[2]}.");
		}
		return true;
	}
	
	
	private function hasDb() {
		if ( true === empty($this->db) ) {
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