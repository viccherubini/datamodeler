<?php

declare(encoding='UTF-8');
namespace DataModeler;

use \DataModeler\Iterator,
	\DataModeler\is_scalar_array,
	\DataModeler\object_to_array;

require_once 'DataModeler/Lib.php';

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

	public function find($parameters) {
		$statement = $this->checkPdoStatement();
		
		if ( is_scalar($parameters) ) {
			$parameters = array($parameters);
		}
		
		$executed = $statement->execute($parameters);
		
		$row = array();
		if ( $executed ) {
			$row = $statement->fetch(\PDO::FETCH_ASSOC);
		}
		
		if ( !$row ) {
			return false;
		}
		
		if ( $this->hasModel() ) {
			$model = clone $this->getModel();
			if ( is_array($row) ) {
				$model->load($row);
			}
			
			return $model;
		}
		
		return $row;
	}
	
	public function free() {
		unset($this->statement);
		$this->statement = NULL;
	}

	public function getModel() {
		return $this->model;
	}

	public function checkPdoStatement() {
		if ( !($this->statement instanceof \PDOStatement) ) {
			throw new \DataModeler\Exception('pdostatement_not_attached');
		}
		return $this->statement;
	}
	
	private function hasModel() {
		return ( $this->model instanceof \DataModeler\Model );
	}
	
	
}