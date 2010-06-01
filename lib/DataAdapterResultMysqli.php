<?php

require_once 'DataAdapterResult.php';

/**
 * Handles a result set from a PHP mysqli_result query.
 * @author vmc <vmc@leftnode.com>
 */
class DataAdapterResultMysqli extends DataAdapterResult {
	
	/**
	 * I am results!
	 * @param mysqli_result $result The result after a successful query through mysqli.
	 */
	public function __construct(mysqli_result $result) {
		$this->setResult($result);
	}
	
	/**
	 * Returns the number of rows.
	 * @retval integer The number of rows in the result.
	 */
	public function getRowCount() {
		return intval($this->getResult()->num_rows);
	}

	/**
	 * Fetches a single row from the result set. 
	 * @param string $field An optional field to return. If this field is set in the data
	 * row, that field's value is returned.
	 * @retval mixed Returns false if there is no more data, an array if $field is not set,
	 * and the value of $data[$field] if $field is set.
	 */
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

	/**
	 * Returns all data from the result set.
	 * @retval array Returns an array of arrays of data.
	 */
	public function fetchAll() {
		$data = array();
		while ( $row = $this->getResult()->fetch_assoc() ) {
			$data[] = $row;
		}
		return $data;
	}

	/**
	 * Frees the memory for the current result set.
	 * @retval bool Returns true.
	 */
	public function free() {
		$this->getResult()->free();
		return true;
	}
}