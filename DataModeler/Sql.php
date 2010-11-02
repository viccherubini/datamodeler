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
		$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		$this->pdo = $pdo;

		return $this;
	}

	public function attachPdoStatement(\PDOStatement $pdoStatement) {
		$this->pdoStatement = $pdoStatement;
		return $this;
	}

	public function begin() {
		$pdo = $this->checkPdo();
		$pdo->beginTransaction();

		return $this;
	}

	public function commit() {
		$pdo = $this->checkPdo();
		$pdo->commit();

		return $this;
	}

	public function rollback() {
		$pdo = $this->checkPdo();
		$pdo->rollBack();

		return $this;
	}

	public function prepare(\DataModeler\Model $model, $fields='*', $where=NULL) {
		$pdo = $this->checkPdo();

		if ( !empty($where) ) {
			$where = "WHERE {$where}";
		}

		if ( empty($fields) ) {
			$fields = '*';
		}

		$sql = "SELECT {$fields} FROM {$model->table()} {$where}";
		$statement = $pdo->prepare($sql);

		$this->checkPdoStatement($statement);

		$sqlResult = new SqlResult;
		$sqlResult->attachModel($model)
			->attachPdoStatement($statement);

		return $sqlResult;
	}

	public function preparePkey(\DataModeler\Model $model, $fields='*') {
		return $this->prepare($model, $fields, "{$model->pkey()} = ?");
	}

	public function save(\DataModeler\Model $model) {
		$pdo = $this->checkPdo();

		$pkey = $model->pkey();
		$table = $model->table();
		$modelNvp = $model->model();
		$modelMeta = $model->modelMeta();

		$saveNames = array();
		$saveUpdates = array();
		$saveFields = array_keys($modelNvp);

		foreach ( $saveFields as $field ) {
			if ( $field != $pkey ) {
				$namedField = ":{$field}";

				$saveNames[$field] = $namedField;
				$saveUpdates[] = "{$field} = {$namedField}";
			}
		}

		if ( $model->exists() ) {
			$namedPkeyField = ":{$pkey}";
			$updateFields = implode(', ', $saveUpdates);
			$saveNames[$pkey] = $namedPkeyField;

			$sql = "UPDATE {$table} SET {$updateFields} WHERE {$pkey} = {$namedPkeyField}";
		} else {
			if ( array_key_exists($pkey, $modelNvp) ) {
				unset($modelNvp[$pkey]);
				$saveFields = array_keys($modelNvp);
			}

			$insertFields = implode(', ', $saveFields);
			$bindList = implode(', ', $saveNames);

			$sql = "INSERT INTO {$table} ({$insertFields}) VALUES({$bindList})";
		}

		$sqlHash = sha1($sql);
		if ( $sqlHash !== $this->sqlHash ) {
			$pdoStatement = $pdo->prepare($sql);

			$this->checkPdoStatement($pdoStatement);
			$this->attachPdoStatement($pdoStatement);

			$this->sqlHash = $sqlHash;
			$this->prepareCount++;
		}

		$pdoStatement = $this->getPdoStatement();

		foreach ( $saveNames as $field => $namedField ) {
			$pdoStatement->bindValue($namedField, $modelNvp[$field], $modelMeta[$field][\DataModeler\Model::SCHEMA_TYPE_PDO]);
		}

		$model = clone $model;
		$execute = $pdoStatement->execute();

		if ( !$execute ) {
			$errorInfo = $pdoStatement->errorInfo();
			throw new \DataModeler\Exception("driver: {$errorInfo[2]}");
		}

		if ( !$model->exists() ) {
			$model->id($pdo->lastInsertId());
		}

		return $model;
	}

	public function delete(\DataModeler\Model $model) {
		$pdo = $this->checkPdo();

		if ( !$model->exists() ) {
			throw new \DataModeler\Exception('model_does_not_exist');
		}

		$id = $model->id();

		$sql = "DELETE FROM {$model->table()} WHERE {$model->pkey()} = ?";
		$pdoStatement = $pdo->prepare($sql);

		$this->checkPdoStatement($pdoStatement);

		$pdoStatement->execute(array($id));
		$affectedRows = $pdoStatement->rowCount();

		return ( $affectedRows > 0 ? true : false );
	}

	public function drop(\DataModeler\Model $model) {
		$pdo = $this->checkPdo();

		$table = $model->table();
		$dropped = $pdo->exec("DROP TABLE {$table}");

		if ( false === $dropped ) {
			$errorInfo = $pdo->errorInfo();
			throw new \DataModeler\Exception("driver: {$errorInfo[2]}");
		}

		return true;
	}

	public function query($sql) {
		$pdo = $this->checkPdo();

		$pdoStatement = $pdo->prepare($sql);
		$this->checkPdoStatement($pdoStatement);

		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		return $sqlResult;
	}

	public function countOf(\DataModeler\Model $model, $where=NULL, $parameters=array()) {
		$pdo = $this->checkPdo();

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

	public function insertId() {
		$pdo = $this->checkPdo();
		return $pdo->lastInsertId();
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


	// ##################################################
	// PRIVATE METHODS
	// ##################################################

	private function checkPdo() {
		if ( !($this->pdo instanceof \PDO) ) {
			throw new \DataModeler\Exception('PDO driver not attached');
		}
		return $this->pdo;
	}

	private function checkPdoStatement($statement) {
		if ( !($statement instanceof \PDOStatement) ) {
			$driverError = NULL;
			if ( $this->pdo instanceof \PDO ) {
				$errorInfo = $this->pdo->errorInfo();
				$driverError = $errorInfo[2];
			}
			throw new \DataModeler\Exception("Failed to create PDOStatement object: {$driverError}");
		}
		return true;
	}

}
