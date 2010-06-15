<?php

declare(encoding='UTF-8');
namespace DataModeler\Adapter;

use \DataModeler\Adapter,
	\DataModeler\Model,
	\DataModeler\Iterator;

class Sql extends Adapter {
	
	private $db = NULL;
	private $driverOptions = array();
	private $inputParameters = array();
	private $model = NULL;
	private $sql = NULL;
	private $statement = NULL;
	private $statementExecute = false;
	private $where = NULL;


	
	public function attachDb(\PDO $db) {
		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);

		$this->db = $db;
		return $this;
	}

	public function rawExecute($sql) {
		$this->sql = $sql;
		
		$this->hasSql();
		$this->hasDb();
		$this->preventSelectStatementExecution();
		
		$row_count = $this->db->exec($sql);
		
		return $row_count;
	}

	public function find(Model $model, $where = NULL, array $inputParameters = array()) {
		$this->hasDb();
		
		$this->prepareSqlParameters($model, $where, $inputParameters);
		$this->executeFind();
		
		$matchedModel = clone $this->model;
		$rowData = $this->statement->fetch(\PDO::FETCH_ASSOC);
		if ( false !== $rowData && true === is_array($rowData) ) {
			$matchedModel->model($rowData);
		}
		
		return $matchedModel;
	}
	
	public function findAll(Model $model, $where = NULL, array $inputParameters = array()) {
		$this->hasDb();
		
		$this->prepareSqlParameters($model, $where, $inputParameters);
		$this->executeFind();
		
		$iteratorData = array();
		$rowData = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
		if ( false !== $rowData && true === is_array($rowData) ) {
			foreach ( $rowData as $row ) {
				$clonedModel = clone $this->model;
				$clonedModel->model($row);
			
				$iteratorData[] = $clonedModel;
			}
		}
		
		$iterator = new Iterator($iteratorData);
		
		return $iterator;
	}
	
	public function insert(Model $object, array $inputParameters = array()) {
		
		
	}
	
	public function query($sql, array $inputParameters, Model $model = NULL) {
		
	}
	
	public function queryFirst($sql, array $inputParameters, Model $model = NULL) {
		
	}
	
	public function update(Model $model, $where = NULL, array $inputParameters = array()) {
		
	}
	
	
	
	
	private function executeFind() {
		$this->buildSelectSqlStatement();
		$this->prepareSqlStatement();
		$this->executePreparedStatement();
		return true;
	}
	
	private function prepareSqlParameters(Model $model, $where, array $inputParameters) {
		$this->model = $model;
		$this->inputParameters = $inputParameters;
		$this->where = NULL;
		
		if ( false === empty($where) ) {
			$this->where = "WHERE {$where}";
		}
		
		return true;
	}

	private function buildSelectSqlStatement() {
		$this->sql = "SELECT * FROM {$this->model->table()} {$this->where}";
		return true;
	}
	
	private function prepareSqlStatement() {
		$this->statement = $this->db->prepare($this->sql);
		$this->handlePreparedStatement();
		return true;
	}
	
	private function executePreparedStatement() {
		$this->statementExecute = $this->statement->execute($this->inputParameters);
		$this->handlePreparedStatementExecution();
		return true;
	}

	private function handlePreparedStatement() {
		if ( false === $this->statement ) {
			$error_info = $this->db->errorInfo();
			throw new \DataModeler\Exception("An error occurred when preparing {$this->sql}. Driver said {$error_info[2]}.");
		}
		return true;
	}

	private function handlePreparedStatementExecution() {
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