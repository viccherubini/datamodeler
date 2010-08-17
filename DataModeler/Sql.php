<?php

declare(encoding='UTF-8');
namespace DataModeler;

use \DataModeler\Model,
	\DataModeler\SqlResult;

class Sql {
	
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

	public function prepare(\DataModeler\Model $model, $where=NULL) {
		$this->checkPdo();
		
		if ( !empty($where) ) {
			$where = "WHERE {$where}";
		}
		
		$sql = "SELECT * FROM {$model->table()} {$where}";
		$statement = $this->pdo->prepare($sql);
		
		if ( !$statement ) {
			throw new \DataModeler\Exception("preparation_failed: {$sql}");
		}
		
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($model)
			->attachStatement($statement);
		
		return $sqlResult;
	}
	
	public function preparePkey(\DataModeler\Model $model) {
		return $this->prepare($model, "{$model->pkey()} = ?");
	}
	
	
	
	
	
	
	
	
	
	
	public function save(\DataModeler\Model $model) {
		$this->checkPdo();

		$nvp = $model->nvp();
		$parameters = array_values($nvp);
		
		if ( $model->exists() ) {
			$setList = implode(' = ?, ', array_keys($nvp)) . ' = ?';
			$sql = "UPDATE {$model->table()} SET {$setList} WHERE {$model->pkey()} = ?";
			$parameters[] = $model->id();
		} else {
			$fieldList = implode(', ', array_keys($nvp));
			$valueList = implode(', ', array_fill(0, count($nvp), '?'));
			$sql = "INSERT INTO {$model->table()} ({$fieldList}) VALUES({$valueList})";
		}

		$hash = sha1($sql);
		if ( $hash !== $this->sqlHash ) {
			$this->statement = $this->pdo->prepare($sql);
			$this->sqlHash = $hash;
			$this->prepareCount++;
		}
		
		if ( !$this->statement ) {
			throw new \DataModeler\Exception("preparation_failed: {$sql}");
		}

		$model = clone $model;
		$execute = $this->statement->execute($parameters);

		if ( $execute ) {
			if ( !$model->exists() ) {
				$model->id($this->pdo->lastInsertId());
			}
		}
		
		return $model;
	}
	
	public function delete(\DataModeler\Model $model) {
		$this->checkPdo();
		
		if ( !$model->exists() ) {
			throw new \DataModeler\Exception('model_does_not_exist');
		}
		
		$id = $model->id();
		$pdo = $this->getPdo();
		
		$sql = "DELETE FROM {$model->table()} WHERE {$model->pkey()} = ?";
		$statement = $pdo->prepare($sql);
		
		if ( !$statement) {
			throw new \DataModeler\Exception("preparation_failed: {$sql}");
		}
		
		$statement->execute(array($id));
		
		$affectedRows = $statement->rowCount();
		
		return ( $affectedRows > 0 ? true : false );
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
	
	public function now($time=0, $short=false) {
		$time = ( 0 === $time ? time() : $time );
		$format = ( false === $short ? 'Y-m-d H:i:s' : 'Y-m-d' );
		$date = date($format, $time); 
		
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
			throw new \DataModeler\Exception('not_attached');
		}
		return true;
	}
	
}