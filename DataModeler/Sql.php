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

	public function attachPdoStatement($statement) {
		$this->statement = $statement;
		return $this;
	}

	public function prepare(\DataModeler\Model $model, $where=NULL) {
		$this->checkPdo();
		$pdo = $this->getPdo();
		
		if ( !empty($where) ) {
			$where = "WHERE {$where}";
		}
		
		$sql = "SELECT * FROM {$model->table()} {$where}";
		$statement = $pdo->prepare($sql);
		
		$this->checkPdoStatement($statement);
		
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($model)
			->attachPdoStatement($statement);
		
		return $sqlResult;
	}
	
	public function preparePkey(\DataModeler\Model $model) {
		return $this->prepare($model, "{$model->pkey()} = ?");
	}
	
	public function save(\DataModeler\Model $model) {
		$this->checkPdo();
		$pdo = $this->getPdo();

		$table = $model->table();
		$modelData = $model->model();
		$parameters = array_values($modelData);
		
		$fieldList = array_map(function($v) use ($table) { return "{$table}.{$v}"; }, array_keys($modelData));
		
		if ( $model->exists() ) {
			$setList = implode(' = ?, ', $fieldList);
			
			$sql = "UPDATE {$table} SET {$setList} = ? WHERE {$model->pkey()} = ?";
			$parameters[] = $model->id();
		} else {
			$fieldList = implode(', ', $fieldList);
			$valueList = implode(', ', array_fill(0, count($modelData), '?'));
			
			$sql = "INSERT INTO {$table} ({$fieldList}) VALUES({$valueList})";
		}

		$hash = sha1($sql);
		if ( $hash !== $this->sqlHash ) {
			$this->attachPdoStatement($pdo->prepare($sql));
			
			$this->sqlHash = $hash;
			$this->prepareCount++;
		}

		$pdoStatement = $this->getPdoStatement();
		$this->checkPdoStatement($pdoStatement);

		$model = clone $model;
		$execute = $pdoStatement->execute($parameters);

		if ( $execute ) {
			if ( !$model->exists() ) {
				$model->id($pdo->lastInsertId());
			}
		}
		
		return $model;
	}
	
	public function delete(\DataModeler\Model $model) {
		$this->checkPdo();
		$pdo = $this->getPdo();
		
		if ( !$model->exists() ) {
			throw new \DataModeler\Exception('model_does_not_exist');
		}
		
		$id = $model->id();
		
		$sql = "DELETE FROM {$model->table()} WHERE {$model->pkey()} = ?";
		$statement = $pdo->prepare($sql);
		
		$this->checkPdoStatement($statement);
		
		$statement->execute(array($id));
		$affectedRows = $statement->rowCount();
		
		return ( $affectedRows > 0 ? true : false );
	}
	
	public function query($sql) {
		$this->checkPdo();
		$pdo = $this->getPdo();
		
		$statement = $pdo->prepare($sql);
		$this->checkPdoStatement($statement);
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($statement);
		
		return $sqlResult;
	}
	
	public function countOf(\DataModeler\Model $model, $where=NULL, $parameters=array()) {
		$this->checkPdo();
		$pdo = $this->getPdo();
		
		if ( !empty($where) ) {
			$where = "WHERE {$where}";
		}
		
		$sql = "SELECT COUNT(*) FROM {$model->table()} {$where}";
		$statement = $pdo->prepare($sql);
	
		$rowCount = 0;
		if ( $statement instanceof \PDOStatement ) {
			$statement->execute($parameters);
			
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

	public function getPdoStatement() {
		return $this->statement;
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
	
	private function checkPdoStatement($statement) {
		if ( !($statement instanceof \PDOStatement) ) {
			throw new \DataModeler\Exception('not_attached');
		}
		return true;
	}
	
}