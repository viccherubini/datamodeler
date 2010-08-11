<?php

declare(encoding='UTF-8');
namespace DataModeler;

use \DataModeler\Model,
	\DataModeler\Iterator;

class Sql {
	
	private $model = NULL;
	private $pdo = NULL;
	private $statement = NULL;
	
	private $prepareCount = 0;
	
	public function __construct() {
		
	}
	
	public function __destruct() {
		
	}
	
	public function attachPdo(\PDO $pdo) {
		$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
		$this->pdo = $pdo;
		
		return $this;
	}
	
	public function begin() {
		$this->checkPdo();
		$this->getPdo()->beginTransaction();
		
		return true;
	}

	public function commit() {
		$this->checkPdo();
		$this->getPdo()->commit();
		
		return true;
	}

	public function rollback() {
		$this->checkPdo();
		$this->getPdo()->rollBack();
		
		return true;
	}
	
	public function singleQuery(\DataModeler\Model $model, $sql, $parameters) {
		
	}
	
	public function multiQuery(\DataModeler\Model $model, $sql) {
		
	}
	
	public function fetch($parameters) {
		
	}
	
	public function countOf(\DataModeler\Model $model, $where=NULL, $parameters=array()) {
		$this->checkPdo();
		
		$statement = $this->pdo->prepare("SELECT COUNT(*) FROM {$model->table()} {$where}");
		$statement->execute($parameters);
		
		$rowCount = 0;
		if ( $statement instanceof \PDOStatement ) {
			$rowCount = $statement->fetchColumn(0);
			$rowCount = intval($rowCount);
		}
		
		return $rowCount;
	}
	
	public function save(\DataModeler\Model $model) {
/*
		$inputParameters = array_values($model->model());
		
		if ( $model->exists() ) {
			$setList = implode(' = ?, ', array_keys($model->model())) . ' = ?';
			$this->prepareQuery("UPDATE {$model->table()} SET {$setList} WHERE {$model->pkey()} = ?");
			$inputParameters[] = $model->id();
		} else {
			$fieldList = implode(', ', array_keys($model->model()));
			$valueList = implode(', ', array_fill(0, count($model->model()), '?'));
			$this->prepareQuery("INSERT INTO {$model->table()} ({$fieldList}) VALUES({$valueList})");
		}
		
		$updatedModel = clone $model;
		if ( $this->hasStatement() ) {
			$statementExecute = $this->getStatement()->execute($inputParameters);
			
			if ( $statementExecute ) {
				if ( !$updatedModel->exists() ) {
					$updatedModel->id($this->pdo->lastInsertId());
				}
			}
		}
		
		return $updatedModel;
*/
	}
	
	public function now($time = -1) {
		$time = ( -1 == $time ? time() : $time );
		$date = date('Y-m-d H:i:s', $time); 
		
		return $date;
	}
	
	
	
	
	
	
	
	
	
	
	
	public function getPdo() {
		return $this->pdo;
	}

	public function getPrepareCount() {
		return $this->prepareCount;
	}
	
	private function checkPdo() {
		if ( !($this->pdo instanceof \PDO) ) {
			throw new \DataModeler\Exception('sql_pdo_not_attached');
		}
		return true;
	}
}