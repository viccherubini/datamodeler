<?php

require_once 'DataAdapterResult.php';

class DataAdapterResultMysqli extends DataAdapterResult {
	public function __construct(mysqli_result $result) {
		$this->setResult($result);
	}
	
	public function getRowCount() {
		return intval($this->getResult()->num_rows);
	}

	public function fetch($field=NULL) {
		$data = $this->getResult()->fetch_assoc();
		
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
		$data = array();
		while ( $row = $this->getResult()->fetch_assoc() ) {
			$data[] = $row;
		}
		return $data;
	}

	public function free() {
		$this->getResult()->free();
		return true;
	}
}