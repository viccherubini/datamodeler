<?php

declare(encoding='UTF-8');
namespace DataModeler;

use \DataModeler\Model,
	\DataModeler\SqlResult;

class Sql {
	
	private $pdo = NULL;
	private $sqlHash = NULL;
	private $pdoStatement = NULL;
	
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

	public function attachPdoStatement(\PDOStatement $pdoStatement) {
		$this->pdoStatement = $pdoStatement;
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

		$pkey = $model->pkey();
		$table = $model->table();
		$modelData = $model->model();
		
		$fieldList = array_map(function($v) { return "`{$v}`"; }, array_keys($modelData));
		
		if ( $model->exists() ) {
			$setList = implode(' = ?, ', $fieldList);
			
			$sql = "UPDATE {$table} SET {$setList} = ? WHERE {$model->pkey()} = ?";
			$modelData[] = $model->id();
		} else {
			$fieldList = implode(', ', $fieldList);
			$valueList = implode(', ', array_fill(0, count($modelData), '?'));
			
			$sql = "INSERT INTO {$table} ({$fieldList}) VALUES({$valueList})";
			if ( isset($modelData[$pkey]) ) {
				$modelData[$pkey] = NULL;
			}
		}

		$hash = sha1($sql);
		if ( $hash !== $this->sqlHash ) {
			$pdoStatement = $pdo->prepare($sql);
			$this->attachPdoStatement($pdoStatement);
			
			$this->sqlHash = $hash;
			$this->prepareCount++;
		}
		
		$parameters = array_values($modelData);
		
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
		
		$pdoStatement = $pdo->prepare($sql);
		$this->checkPdoStatement($pdoStatement);
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);
		
		return $sqlResult;
	}
	
	public function countOf(\DataModeler\Model $model, $where=NULL, $parameters=array()) {
		$this->checkPdo();
		$pdo = $this->getPdo();
		
		if ( !empty($where) ) {
			$where = "WHERE {$where}";
		}
		
		$sql = "SELECT COUNT(*) FROM {$model->table()} {$where}";
		$pdoStatement = $pdo->prepare($sql);
	
		$rowCount = 0;
		if ( $pdoStatement instanceof \PDOStatement ) {
			$pdoStatement->execute($parameters);
			
			$rowCount = $pdoStatement->fetchColumn(0);
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
		return $this->pdoStatement;
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