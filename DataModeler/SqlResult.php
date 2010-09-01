<?php

declare(encoding='UTF-8');
namespace DataModeler;

use \DataModeler\Iterator;

class SqlResult {

	private $closureList = array();

	private $model = NULL;
	private $pdoStatement = NULL;

	public function __construct() {
	
	}
	
	public function __destruct() {
	
	}

	public function attachModel(\DataModeler\Model $model) {
		$this->model = clone $model;
		return $this;
	}
	
	public function attachPdoStatement(\PDOStatement $pdoStatement) {
		$this->pdoStatement = $pdoStatement;
		return $this;
	}

	public function find($parameters=array()) {
		$pdoStatement = $this->checkPdoStatement();
		$executed = $this->compile($parameters);
		
		$dbRow = array();
		if ( $executed ) {
			$dbRow = $pdoStatement->fetch(\PDO::FETCH_ASSOC);
		}
		
		if ( !$executed || !$dbRow || !is_array($dbRow) ) {
			return false;
		}
		
		if ( $this->hasModel() ) {
			$model = $this->getClonedModel();
			$model->load($dbRow);
			
			$pkey = $model->pkey();
			if ( isset($dbRow[$pkey]) ) {
				$model->id($dbRow[$pkey]);
			}
			
			return $model;
		}
		
		return $dbRow;
	}
	
	public function findAll($parameters=array()) {
		$pdoStatement = $this->checkPdoStatement();
		$executed = $this->compile($parameters);
		
		$dbRows = array();
		if ( $executed ) {
			$dbRows = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
		}
		
		$iterator = new Iterator(array());
		
		if ( $this->hasModel() ) {
			$dbModels = array();

			foreach ( $dbRows as $dbRow ) {
				$model = $this->getClonedModel();
				$model->load($dbRow);
				
				$pkey = $model->pkey();
				if ( isset($dbRow[$pkey]) ) {
					$model->id($dbRow[$pkey]);
				}
				
				array_push($dbModels, $model);
			}
			
			$iterator->init($dbModels);
		} else {
			$iterator->init($dbRows);
		}
		
		return $iterator;
	}
	
	public function free() {
		$this->pdoStatement = NULL;
	}

	public function getModel() {
		return $this->model;
	}
	
	public function getClonedModel() {
		return (clone $this->getModel());
	}


	// ##################################################
	// PRIVATE METHODS
	// ##################################################

	private function checkPdoStatement() {
		if ( !($this->pdoStatement instanceof \PDOStatement) ) {
			throw new \DataModeler\Exception('pdostatement_not_attached');
		}
		return $this->pdoStatement;
	}
	
	private function hasModel() {
		return ( $this->model instanceof \DataModeler\Model );
	}
	
	private function compile($parameters) {
		$pdoStatement = $this->checkPdoStatement();
		
		if ( is_scalar($parameters) || is_null($parameters) ) {
			$parameters = array($parameters);
		}

		// Attempt to determine how to bind the parameters
		foreach ( $parameters as $column => $pValue ) {
			$type = \PDO::PARAM_STR;
			
			if ( is_int($pValue) ) {
				$type = \PDO::PARAM_INT;
			} elseif ( is_bool($pValue) ) {
				$pValue = ( false === $pValue ? 0 : 1 );
				$type = \PDO::PARAM_INT;
			}
			
			// Parameters are 1-based, not 0-based
			if ( is_numeric($column) ) {
				$column++;
			}
			
			$pdoStatement->bindValue($column, $pValue, $type);
		}
		
		$executed = $pdoStatement->execute();
		return $executed;
	}
	
}