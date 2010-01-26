<?php

require_once 'DataAdapterResult.php';

class DataAdapterResultPdo extends DataAdapterResult {
	public function __construct(PDOStatement $result) {
		$this->setResult($result);
	}
	
	public function getRowCount() {
		return intval($this->getResult()->rowCount());
	}

	public function fetch($field=NULL) {
		$data = $this->getResult()->fetch(PDO::FETCH_ASSOC);
		
		if ( false === $data ) {
			$this->free();
			return false;
		}
		
		if ( false === empty($field) && true === isset($data[$field]) ) {
			$data = $data[$field];
		}
		
		return $data;
	}

	public function fetchAll() {
		$data = (array)$this->getResult()->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}

	public function free() {
		$this->getResult()->closeCursor();
		return true;
	}
}