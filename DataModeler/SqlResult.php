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

	public function findFirst() {
		$statement = $this->checkPdoStatement();
		
		$parameters = array();
		$argv = func_get_args();
		$argc = func_num_args();
		
		if ( is_scalar_array($argv) ) {
			$parameters = $argv;
		} else {
			if ( $argc > 0 ) {
				if ( is_scalar_array($argv[0]) ) {
					$parameters = $argv[0];
				} elseif ( is_object($argv[0]) ) {
					
				}
			}
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
	
	public function findAll() {
		
	}
	
	public function map(\Closure $closure) {
		
	}
	
	public function free() {
		unset($this->statement);
		$this->statement = NULL;
	}

	public function getModel() {
		return $this->model;
	}
	
	public function getPdoStatement() {
		return $this->statement;
	}

	private function checkModel() {
		if ( !($this->model instanceof \DataModeler\Model) ) {
			throw new \DataModeler\Exception('model_not_attached');
		}
		return true;
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