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
	
	public function attachStatement(\PDOStatement $statement) {
		$this->statement = clone $statement;
		return $this;
	}

	public function findFirst($parameters=array()) {
		$this->checkModel();
		$this->checkStatement();
		
		$executed = $this->statement->execute($parameters);
		
		$model = clone $this->model;
		if ( $executed ) {
			$dataRow = $this->statement->fetch(\PDO::FETCH_ASSOC);
			if ( is_array($dataRow) ) {
				$model->load($dataRow);
			}
		}
		
		return $model;
	}
	
	public function findAll() {
		
	}
	
	public function map(\Closure $closure) {
		
	}
	
	public function free() {
		
	}


	private function checkModel() {
		if ( is_null($this->model) ) {
			throw new \DataModeler\Exception('model_not_attached');
		}
		return true;
	}
	
	public function checkStatement() {
		if ( is_null($this->statement) ) {
			throw new \DataModeler\Exception('pdostatement_not_attached');
		}
		return true;
	}
}