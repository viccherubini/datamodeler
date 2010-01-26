<?php

require_once 'DataAdapter.php';

abstract class DataAdapterPdo extends DataAdapter {

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
		if ( true === $this->getConnected() && $this->getDriver() != self::DRIVER_ODBC ) {
			return $this->getConnection()->quote($value);
		}
		return addslashes($value);
	}
}