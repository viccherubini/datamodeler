<?php

declare(encoding='UTF-8');
namespace DataModeler;

use \DataModeler\Model,
	\DataModeler\Iterator;

class Sql {
	
	private $model = NULL;
	private $pdo = NULL;
	private $sqlHash = NULL;
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
	
	public function attachModel(\DataModeler\Model $model) {
		$this->model = clone $model;
		
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
	
	public function singleQuery(\DataModeler\Model $model, $where=NULL, array $parameters=array()) {
		$this->checkPdo();
		
		if ( !empty($where) ) {
			$where = "WHERE {$where}";
		}
		
		$sql = "SELECT * FROM {$model->table()} {$where}";
		$statement = $this->pdo->prepare($sql);
		
		if ( $statement instanceof \PDOStatement ) {
			$statement->execute($parameters);
			$rowData = $statement->fetch(\PDO::FETCH_ASSOC);
			unset($statement);
			
			if ( is_array($rowData) ) {
				$model->load($rowData);
			}
		}
		
		return $model;
	}
	
	public function multiQuery(\DataModeler\Model $model, $where=NULL) {
		$this->checkPdo();
		$this->attachModel($model);
		
		if ( !empty($where) ) {
			$where = "WHERE {$where}";
		}
		
		$sql = "SELECT * FROM {$model->table()} {$where}";
		$this->prepare($sql);
		
		return $this;
	}
	
	public function fetch(array $parameters=array()) {
		$this->checkPdo();
		$this->checkModel();
		
		$model = clone $this->model;
		if ( $this->hasStatement() ) {
			$this->statement->execute($parameters);
			$rowData = $this->statement->fetch(\PDO::FETCH_ASSOC);
			
			if ( is_array($rowData) ) {
				$model->load($rowData);
			}
		}
		
		return $model;
	}
	
	public function countOf(\DataModeler\Model $model, $where=NULL, $parameters=array()) {
		$this->checkPdo();
		
		if ( !empty($where) ) {
			$where = "WHERE {$where}";
		}
		
		$sql = "SELECT COUNT(*) FROM {$model->table()} {$where}";
		$statement = $this->pdo->prepare($sql);
		$statement->execute($parameters);
		
		$rowCount = 0;
		if ( $statement instanceof \PDOStatement ) {
			$rowCount = $statement->fetchColumn(0);
			$rowCount = intval($rowCount);
		}
		
		return $rowCount;
	}
	
	public function save(\DataModeler\Model $model) {
		$this->checkPdo();

		$modelNvp = $model->nvp();
		$parameters = array_values($modelNvp);
		
		if ( $model->exists() ) {
			$setList = implode(' = ?, ', array_keys($modelNvp)) . ' = ?';
			$sql = "UPDATE {$model->table()} SET {$setList} WHERE {$model->pkey()} = ?";
			$parameters[] = $model->id();
		} else {
			$fieldList = implode(', ', array_keys($modelNvp));
			$valueList = implode(', ', array_fill(0, count($modelNvp), '?'));
			$sql = "INSERT INTO {$model->table()} ({$fieldList}) VALUES({$valueList})";
		}

		$this->prepare($sql);
		
		$updatedModel = clone $model;
		if ( $this->hasStatement() ) {
			$execute = $this->statement->execute($parameters);

			if ( $execute ) {
				if ( !$updatedModel->exists() ) {
					$updatedModel->id($this->pdo->lastInsertId());
				}
			}
		}
		
		return $updatedModel;
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
	
	private function prepare($sql) {
		$hash = sha1($sql);
		
		if ( $hash != $this->sqlHash ) {
			$this->statement = $this->pdo->prepare($sql);
			
			$this->sqlHash = $hash;
			$this->prepareCount++;
		}
		
		return true;
	}
	
	private function checkPdo() {
		if ( !($this->pdo instanceof \PDO) ) {
			throw new \DataModeler\Exception('sql_pdo_not_attached');
		}
		return true;
	}
	
	private function checkModel() {
		if ( !($this->model instanceof \DataModeler\Model) ) {
			throw new \DataModeler\Exception('sql_model_not_attached');
		}
		return true;
	}
	
	private function hasStatement() {
		return ( $this->statement instanceof \PDOStatement );
	}
	
}