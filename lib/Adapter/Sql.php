<?php

declare(encoding='UTF-8');
namespace DataModeler\Adapter;

use \DataModeler\Adapter,
	\DataModeler\Model,
	\DataModeler\Iterator;

class Sql extends Adapter {
	
	private $pdo = NULL;
	private $model = NULL;
	private $prepareCount = 0;
	private $previousQuery = NULL;
	private $statement = NULL;
	
	const QUERY_INSERT = 2;
	const QUERY_UPDATE = 4;

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
			$this->prepareQuery("SELECT * FROM {$model->table()} WHERE {$where}");
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
	
	public function findAll(array $inputParameters) {
		$modelList = array();
		if ( $this->hasStatement() ) {
			$this->statement->execute($inputParameters);
			$rowDataList = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
			
			if ( is_array($rowDataList) ) {
				foreach ( $rowDataList as $rowData) {
					$model = clone $this->model;
					$model->model($rowData);
					$modelList[] = $model;
				}
			}
		}
		
		return $modelList;
	}
	
	public function save(Model $model) {
		$rowCount = 0;
		
		if ( $model->exists() ) {
			if ( $this->shouldPrepareUpdateStatement($model) ) {
				$setList = implode(' = ?, ', array_keys($model->model())) . ' = ?';
				$this->prepareQuery("UPDATE {$model->table()} SET {$setList} WHERE {$model->pkey()} = ? LIMIT 1");
				$this->previousQuery = self::QUERY_UPDATE;
			}
		} else {
			if ( $this->shouldPrepareInsertStatement($model) ) {
				$fieldList = implode(', ', array_keys($model->model()));
				$valueList = implode(', ', array_fill(0, count($model->model()), '?'));
				
				$this->prepareQuery("INSERT INTO {$model->table()} ({$fieldList}) VALUES({$valueList})");
				$this->previousQuery = self::QUERY_INSERT;
			}
		}
		
		$this->model = $model;
		if ( $this->hasStatement() ) {
			$statementExecute = $this->statement->execute(array_values($model->model()));
			
			if ( $statementExecute ) {
				if ( !$model->exists() ) {
					$model->id($this->pdo->lastInsertId());
				}
			}
		}
		
		return $model;
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
		return ( $this->model instanceof Model && $this->model->equalTo($model) );
	}
	
	private function hasSimilarModel(Model $model) {
		return ( $this->model instanceof Model && $this->model->similarTo($model) );
	}
	
	private function hasStatement() {
		return ( $this->statement instanceof \PDOStatement );
	}
	
	private function hasPdo() {
		if ( !($this->pdo instanceof \PDO) ) {
			throw new \DataModeler\Exception("Database object has not yet been attached to Sql Adapter.");
		}
		return true;
	}
	
	private function prepareQuery($sql) {
		$this->statement = $this->pdo->prepare($sql);
		$this->prepareCount++;
	}
	
	private function shouldPrepareInsertStatement(Model $model) {
		return ( $this->previousQuery === self::QUERY_UPDATE || !$this->hasSimilarModel($model) );
	}
	
	private function shouldPrepareUpdateStatement(Model $model) {
		return ( $this->previousQuery === self::QUERY_INSERT || !$this->hasSimilarModel($model) );
	}
}