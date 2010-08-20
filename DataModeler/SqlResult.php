<?php

declare(encoding='UTF-8');
namespace DataModeler;

use \DataModeler\Iterator;

class SqlResult {

	private $closureList = array();

	private $model = NULL;
	private $statement = NULL;

	public function __construct() {
	
	}
	
	public function __destruct() {
	
	}

	public function attachModel(\DataModeler\Model $model) {
		$this->model = clone $model;
		return $this;
	}
	
	public function attachPdoStatement(\PDOStatement $statement) {
		$this->statement = $statement;
		return $this;
	}

	public function find($parameters=array()) {
		$statement = $this->checkPdoStatement();
		$executed = $this->compile($parameters);
		
		$row = array();
		if ( $executed ) {
			$row = $statement->fetch(\PDO::FETCH_ASSOC);
		}
		
		if ( !$row || !is_array($row) ) {
			return false;
		}
		
		if ( $this->hasModel() ) {
			$model = clone $this->getModel();
			
			$model->load($row);
			$pkey = $model->pkey();
			if ( isset($row[$pkey]) ) {
				$model->id($row[$pkey]);
			}
			
			return $model;
		}
		
		return $row;
	}
	
	public function findAll($parameters=array()) {
		$statement = $this->checkPdoStatement();
		$executed = $this->compile($parameters);
		
		$rows = array();
		if ( $executed ) {
			$rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
		}
		
		$iterator = new Iterator(array());
		
		if ( $this->hasModel() ) {
			$modelList = array();

			foreach ( $rows as $row ) {
				$m = clone $this->getModel();
				$m->load($row);
				
				$pkey = $m->pkey();
				if ( isset($row[$pkey]) ) {
					$m->id($row[$pkey]);
				}
				
				array_push($modelList, $m);
			}
			
			$iterator->init($modelList);
		} else {
			$iterator->init($rows);
		}
		
		return $iterator;
	}
	
	
	public function free() {
		unset($this->statement);
		$this->statement = NULL;
	}

	public function getModel() {
		return $this->model;
	}

	private function checkPdoStatement() {
		if ( !($this->statement instanceof \PDOStatement) ) {
			throw new \DataModeler\Exception('pdostatement_not_attached');
		}
		return $this->statement;
	}
	
	private function hasModel() {
		return ( $this->model instanceof \DataModeler\Model );
	}
	
	private function compile($parameters) {
		$statement = $this->checkPdoStatement();
		
		if ( is_scalar($parameters) ) {
			$parameters = array($parameters);
		}
		
		$executed = $statement->execute($parameters);
		return $executed;
	}
}