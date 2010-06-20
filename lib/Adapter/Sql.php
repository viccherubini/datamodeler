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
	private $sqlHash = NULL;
	private $statement = NULL;
	
	public function attachPdo(\PDO $pdo) {
		$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
		$this->pdo = $pdo;
		return $this;
	}
	
	public function getPdo() {
		return $this->pdo;
	}
	
	public function setModel(Model $model) {
		$this->model = clone $model;
		return $this;
	}
	
	public function getModel() {
		return $this->model;
	}
	
	public function setStatement($statement) {
		$this->statement = $statement;
		return $this;
	}
	
	public function getStatement() {
		return $this->statement;
	}
	
	public function setPrepareCount($prepareCount) {
		$this->prepareCount = intval($prepareCount);
		return $this;
	}
	
	public function getPrepareCount() {
		return $this->prepareCount;
	}
	
	public function getQueryString() {
		if ( $this->hasStatement() ) {
			return $this->getStatement()->queryString;
		}
		return NULL;
	}
	
	public function setSqlHash($sqlHash) {
		$this->sqlHash = $sqlHash;
		return $this;
	}
	
	public function getSqlHash() {
		return $this->sqlHash;
	}

	public function prepare(Model $model, $where = NULL) {
		$this->hasPdo();
		
		if ( empty($where) ) {
			$where = "{$model->pkey()} = ? LIMIT 1";
		}

		$this->prepareQuery("SELECT * FROM {$model->table()} WHERE {$where}");
		$this->setModel($model);
		
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
			$this->getStatement()->execute($inputParameters);
			$rowDataList = $this->getStatement()->fetchAll(\PDO::FETCH_ASSOC);
			
			if ( is_array($rowDataList) ) {
				foreach ( $rowDataList as $rowData) {
					$model = clone $this->getModel();
					$model->model($rowData);
					$modelList[] = $model;
				}
			}
		}
		
		return $modelList;
	}
	
	public function save(Model $model) {
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
	}
	
	public function query($sql, array $inputParameters = array()) {
		$this->prepareQuery($sql);
		
		if ( $this->hasStatement() ) {
			$statementExecute = $this->getStatement()->execute($inputParameters);
		}
		
		return $this;
	}
	
	private function executeFindStatement(array $inputParameters) {
		$model = clone $this->model;
		
		if ( $this->hasStatement() ) {
			$this->getStatement()->execute($inputParameters);
			$rowData = $this->getStatement()->fetch(\PDO::FETCH_ASSOC);
			
			if ( is_array($rowData) ) {
				$model->model($rowData);
			}
		}
		
		return $model;
	}
	
	private function hasStatement() {
		return ( $this->getStatement() instanceof \PDOStatement );
	}
	
	private function hasPdo() {
		if ( !($this->getPdo() instanceof \PDO) ) {
			throw new \DataModeler\Exception("Database object has not yet been attached to Sql Adapter.");
		}
		return true;
	}
	
	private function prepareQuery($sql) {
		if ( $this->shouldPrepareStatement($sql) ) {
			$this->setStatement($this->getPdo()->prepare($sql));
			$this->updatePrepareCount();
		}
		return true;
	}
	
	private function shouldPrepareStatement($sql) {
		$sqlSha1 = sha1($sql);
		if ( $sqlSha1 != $this->getSqlHash() ) {
			$this->setSqlHash($sqlSha1);
			return true;
		}
		return false;
	}
	
	private function updatePrepareCount() {
		$this->prepareCount++;
		return true;
	}
}