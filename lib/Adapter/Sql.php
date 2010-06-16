<?php

declare(encoding='UTF-8');
namespace DataModeler\Adapter;

use \DataModeler\Adapter,
	\DataModeler\Model,
	\DataModeler\Iterator;

class Sql extends Adapter {
	
	private $pdo = NULL;
	private $model = NULL;
	private $statement = NULL;

	private $prepareCount = 0;

	public function getPrepareCount() {
		return $this->prepareCount;
	}

	public function getStatement() {
		return $this->statement;
	}
	
	public function getQueryString() {
		if ( $this->hasStatement() ) {
			return $this->statement->queryString;
		}
		return NULL;
	}

	public function attachPdo(\PDO $pdo) {
		$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
		$this->pdo = $pdo;
		
		return $this;
	}

	public function prepare(Model $model, $where = NULL) {
		$this->hasPdo();
		
		if ( !$this->hasSameModel($model) ) {
			$this->model = $model;
			if ( empty($where) ) {
				$where = "{$model->pkey()} = ? LIMIT 1";
			}

			$this->statement = $this->pdo->prepare("SELECT * FROM {$model->table()} WHERE {$where}");
			$this->prepareCount++;
		}
		
		return $this;
	}

	public function get($id) {
		$model = $this->executeFindStatement(array($id));
		return $model;
	}

	public function find(array $inputParameters) {
		$model = $this->executeFindStatement($inputParameters);
		return $model;
	}
	
	public function save(Model $model) {
		$rowCount = 0;
		
		if ( $model->exists() ) {
			if ( !$this->hasSameModel($model) ) {
				$setList = implode(' = ?, ', array_keys($model->model())) . ' = ?';
				$this->statement->prepare("UPDATE {$model->table()} SET {$setList} WHERE {$model->pkey()} = ? LIMIT 1");
			}
		} else {
			if ( !$this->hasSameModel($model) ) {
				$fieldList = implode(', ', array_keys($model->model()));
				$valueList = implode(', ', array_fill(0, count($model->model()), '?'));
				
				$this->statement->prepare("INSERT INTO {$model->table()} ({$fieldList}) VALUES({$valueList})");
			}
		}
		
		$this->model = $model;
		if ( $this->hasStatement() ) {
			$inputParameters = array_values($model->model());
			$this->statement->execute($inputParameters);
			$rowCount = $this->statement->rowCount();
		}
		
		return ( $rowCount > 0 ? true : false );
	}
	

	
	private function executeFindStatement(array $inputParameters) {
		$model = clone $this->model;
		if ( $this->hasStatement() ) {
			$this->statement->execute($inputParameters);
			$rowData = $this->statement->fetch(\PDO::FETCH_ASSOC);
			
			if ( is_array($rowData) ) {
				$model->model($rowData);
			}
		}
		
		return $model;
	}
	
	private function hasSameModel(Model $model) {
		return ( $this->model instanceof Model && $this->model->isA($model) );
	}
	
	private function hasStatement() {
		return ( $this->statement instanceof \PDOStatement );
	}
	
	private function hasPdo() {
		if ( empty($this->pdo) || !($this->pdo instanceof \PDO) ) {
			throw new \DataModeler\Exception("Database object has not yet been attached to Sql Adapter.");
		}
		return true;
	}

}