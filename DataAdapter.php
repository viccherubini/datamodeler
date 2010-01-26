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
	
	abstract public function loadFirst(DataObject $object);
	
	abstract public function loadAll(DataObject $object);
	
	abstract public function insert(DataObject $object);
	
	abstract public function update(DataObject $object);
	
	abstract public function delete(DataObject $object);
}