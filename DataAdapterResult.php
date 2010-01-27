<?php

/**
 * Abstract class for result sets from a successful query. This class
 * provides a common interface for fetching rows (or data) from a 
 * successful query.
 * @author vmc <vmc@leftnode.com>
 */
abstract class DataAdapterResult {
	/**
	 * The result set object (generally) or resource.
	 */
	private $result = NULL;

	/**
	 * Results happen!
	 * @param mixed $result The result set object or resource.
	 */
	public function __construct($result) {
		$this->setResult($result);
	}
	
	/**
	 * Go and die. Unsets the result.
	 */
	public function __destruct() {
		unset($this->result);
	}
	
	/**
	 * Sets the result object or resource.
	 * @param mixed $result The result object or resource.
	 * @retval DataAdapterResult This for chaining.
	 */
	public function setResult($result) {
		$this->result = $result;
		return $this;
	}
	
	/**
	 * Get the result object or resource.
	 * @retval mixed The result object or resource.
	 */
	public function getResult() {
		return $this->result;
	}
	
	/**
	 * Returns the number of rows from the query.
	 */
	abstract public function getRowCount();

	/**
	 * Fetch a single row, or a single field from that row.
	 */
	abstract public function fetch($field=NULL);

	/**
	 * Fetch all rows in a 2D array.
	 */
	abstract public function fetchAll();

	/**
	 * Frees the memory associated with the result set.
	 */
	abstract public function free();
}