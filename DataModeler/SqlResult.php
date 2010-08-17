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

	public function findFirst() {
		
	}
	
	public function findAll() {
		
	}
	
	public function map(\Closure $closure) {
		
	}
	
	public function free() {
		
	}


}