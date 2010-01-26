<?php

require_once 'DataModelerException.php';
require_once 'DataAdapter.php';
require_once 'DataAdapterResultPdo.php';

class DataAdapterPdo extends DataAdapter {
	/**
	 * Welcome, my friends. Connect to me.
	 * @param object $connection A valid PDO object that has already been connected to the data store.
	 */
	public function __construct(PDO $connection) {
		$this->setConnection($connection);
		$this->setConnected(true);
	}

	/**
	 * Execute a query against the data store.
	 * @param string $sql The query to execute.
	 * @param array $value_list An optional array of values to replace the ?'s with in $sql.
	 * @throw DataModelerException If a query fails.
	 * @retval DataAdapterResultPdo Returns a valid DataAdapterResult object to fetch data.
	 */
	public function query($sql, $value_list=array()) {
		if ( false === $this->getConnected() ) {
			throw new DataModelerException('Datastore does not currently have a valid connection, statement can not be executed.');
		}
		
		try {
			$statement = $this->getConnection()->prepare($sql);
			$result = $statement->execute($value_list);
			
			if ( false === $result ) {
				$error_info = $statement->errorInfo();
				throw new DataModelerException("Failed to execute query: {$sql}. Data store said {$error_info[2]}");
			}

			$pdo_result = new DataAdapterResultPdo($statement);
			return $pdo_result;
		} catch ( PDOException $e ) {
			throw new DataModelerException($e->getMessage());
		}
		return false;
	}

	/**
	 * Returns ID of the last inserted element.
	 * @retval integer Returns the ID of the last inserted element, 0 otherwise.
	 */
	public function insertId() {
		if ( true === $this->getConnected() ) {
			return $this->getConnection()->lastInsertId();
		}
		return 0;
	}
	
	/**
	 * Properly escapes a string for SQL insertion. Generally this method shouldn't be used. 
	 * ODBC drivers can't escape things properly, so addslashes() is used. If there is no
	 * database connection, addslashes() (which is very unsafe) is used.
	 * @param string $value The value to be escaped.
	 * @retval string Returns the escaped string.
	 */
	public function escape($value) {
		if ( true === $this->getConnected() ) {
			return $this->getConnection()->quote($value);
		}
		return addslashes($value);
	}
	
	/**
	 * Load the first found row from the database. If no rows are found, the
	 * object that was passed in is returned. If one or more rows are found,
	 * the first row is loaded into the DataObject and returned.
	 * @param DataObject $object The object to get data from and to load into.
	 * @retval DataObject Returns the DataObject for further info.
	 */
	public function loadFirst(DataObject $object) {
		
	}
	
	/**
	 * Executes the query and loads all results found. Returns everything as a DataIterator.
	 * Even if no rows are found, a DataIterator is still returned to ensure
	 * consistent results.
	 * @param DataObject $object The object to load the data into for each iterator element.
	 * @retval DataIterator Returns a DataIterator to loop through. Each element is a DataObject element.
	 */
	public function loadAll(DataObject $object) {
		
	}
	
	/**
	 * Insert a new DataObject record into the database.
	 * @param DataObject $object The object to get data from to insert.
	 * @retval integer Returns the auto-incremented ID of the inserted row, 0 if the query failed.
	 */
	public function insert(DataObject $object) {
		$date_create = $object->getDateCreate();
		if ( true === $object->hasDate() && true === empty($date_create) ) {
			$object->setDateCreate(time());
		}
		
		$table = $object->table();
		$pkey = $object->pkey();
		$model = $object->model();
		$model_length = count($model);
		
		$field_list = implode('`, `', array_keys($model));
		$value_list = implode(', ', array_fill(0, $model_length, '?'));
		
		$sql = "INSERT INTO `" . $table . "` (`" . $field_list . "`) VALUES(" . $value_list . ")";
		$result = $this->query($sql, array_values($model));

		$id = 0;
		if ( 1 === $result->getRowCount() ) {
			$id = $this->insertId();
		}
		
		return $id;
	}
	
	/**
	 * Update a DataObject in the database.
	 * @param DataObject $object The object to get data from to update.
	 * @retval integer Returns the ID of the updated row, always.
	 */
	public function update(DataObject $object) {
		$date_modify = $object->getDateModify();
		if ( true === $object->hasDate() && true === empty($date_modify) ) {
			$object->setDateModify(time());
		}
		
		$id = $object->id();
		$table = $object->table();
		$pkey = $object->pkey();
		$model = $object->model();
		
		$field_list = array();
		foreach ( $model as $field => $value ) {
			$field_list[] = "`" . $field . "` = ?";
		}
		$field_list_sql = implode(', ', $field_list);
		
		$sql = "UPDATE `" . $table . "` SET " . $field_list_sql . " WHERE `" . $pkey . "` = '" . $id . "' LIMIT 1";
		
		$result = $this->query($sql, array_values($model));
		
		$id = 0;
		if ( 1 === $result->getRowCount() ) {
			$id = $object->id();
		}
		
		return $id;
	}
	
	/**
	 * Deletes a DataObject from the database.
	 * @param DataObject $object The DataObject to delete.
	 * @retval bool Returns true on successful deletion, false otherwise.
	 */
	public function delete(DataObject $object) {
		$id = $object->id();
		$table = $object->table();
		$pkey = $object->pkey();
		
		$sql = "DELETE FROM `" . $table . "` WHERE `" . $pkey . "` = ? LIMIT 1";
		$result = $this->query($sql, array($id));
		
		if ( 1 === $result->getRowCount() ) {
			return true;
		}
		
		return false;
	}
}