<?php

require_once 'DataAdapterResult.php';

/**
 * This class contains a result statement after a successful query from
 * a PDO execution.
 * @author vmc <vmc@leftnode.com>
 */
class DataAdapterResultPdo extends DataAdapterResult {
	
	/**
	 * I am results!
	 * @param PDOStatement The result statement to get rows from a SELECT statement.
	 */
	public function __construct(PDOStatement $result) {
		$this->setResult($result);
	}
	
	/**
	 * Returns the number of rows.
	 * @retval integer The number of rows in the result.
	 */
	public function getRowCount() {
		return intval($this->getResult()->rowCount());
	}

	/**
	 * Fetches a single row from the result set. 
	 * @param string $field An optional field to return. If this field is set in the data
	 * row, that field's value is returned.
	 * @retval mixed Returns false if there is no more data, an array if $field is not set,
	 * and the value of $data[$field] if $field is set.
	 */
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

	/**
	 * Returns all data from the result set.
	 * @retval array Returns an array of arrays of data.
	 */
	public function fetchAll() {
		$data = (array)$this->getResult()->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}

	/**
	 * Frees the memory for the current result set.
	 * @retval bool Returns true.
	 */
	public function free() {
		$this->getResult()->closeCursor();
		return true;
	}
}