<?php

/**
 * Abstract data adapter that can be used for any data store, including
 * databases and NoSQL data stores.
 * @author vmc <vmc@leftnode.com>
 */
abstract class DataAdapter {
	
	/**
	 * The connection object to interface with the data store.
	 */
	private $connection = NULL;
	
	/**
	 * Whether or not a valid connection exists.
	 */
	private $connected = false;

	/**
	 * Hello, Dave.
	 */
	public function __destruct() {
		unset($this->connection);
	}

	/**
	 * Sets the connection object. This is generally an object, but not always.
	 * @param mixed $connection The connection object made outside of the class.
	 * @retval DataAdapter This for chaining.
	 */
	public function setConnection($connection) {
		$this->connection = $connection;
		return $this;
	}
	
	/**
	 * Sets the connection status, true == connected, false == unconnected.
	 * @param bool $connected Whether the data store has a current connection.
	 * @retval DataAdapter Returns this for chaining. 
	 */
	public function setConnected($connected) {
		if ( false !== $connected && true !== $connected ) {
			$connected = false;
		}
		$this->connected = $connected;
		return $this;
	}
	
	/**
	 * Returns the connection object.
	 * @retval mixed The connection object, or NULL when no object has been set.
	 */
	public function getConnection() {
		return $this->connection;
	}
	
	/**
	 * Returns the connection status. True if connected, false otherwise. False by default.
	 * @retval mixed The connection status.
	 */
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