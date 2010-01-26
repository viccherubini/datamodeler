<?php

abstract class DataAdapter {
	private $connection = NULL;
	private $connected = false;

	public function __destruct() {
		unset($this->connection);
	}

	public function setConnection($connection) {
		$this->connection = $connection;
		return $this;
	}
	
	public function setConnected($connected) {
		if ( false !== $connected && true !== $connected ) {
			$connected = false;
		}
		$this->connected = $connected;
		return $this;
	}
	
	public function getConnection() {
		return $this->connection;
	}
	
	public function getConnected() {
		return $this->connected;
	}
	
	/**
	 * Abstract method to return the ID of the last inserted element.
	 */
	abstract public function insertId();
	
	/**
	 * Abstract method to escape a string for database insertion.
	 */
	abstract public function escape($value);
	
	/**
	 * Abstract method to execute a query.
	 */
	abstract public function query($query);
	
	/**
	 * Load the first row from a data store into a DataObject.
	 */
	abstract public function loadFirst(DataObject $object);
	
	/**
	 * Load all rows from the data store with a given query into a DataIterator.
	 */
	abstract public function loadAll(DataObject $object);
	
	/**
	 * Insert a DataObject into the data store.
	 */
	abstract public function insert(DataObject $object);
	
	/**
	 * Update a DataObject in the data store.
	 */
	abstract public function update(DataObject $object);
	
	/**
	 * Delete a DataObject from the data store.
	 */
	abstract public function delete(DataObject $object);
}