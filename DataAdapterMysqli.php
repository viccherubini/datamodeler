<?php

require_once 'DataAdapterAbstract.php';

class DataAdapterMysqli extends DataAdapterAbstract {
	
	public function connect() {
		$server = $this->config['server'];
		$username = $this->config['username'];
		$password = $this->config['password'];
		$database = $this->config['database'];

		$port = 3306;
		if ( true === @isset($this->config['port']) && intval($this->config['port']) > 0 ) {
			$port = intval($this->config['port']);
		}

		// Although generally against supressing errors, the @ is
		// to supress a misconnection error
		// to allow the framework to handle it gracefully
		$connection = @new mysqli($server, $username, $password, $database, $port);
		$this->setConnection($connection);

		if ( 0 != mysqli_connect_errno() || false === $connection ) {
			$this->setConnected(false);
			throw new DataModelerException(mysqli_connect_error());
		}

		$this->setConnected(true);
		return $connection;
	}
	
	public function close() {
		return $this->disconnect();
	}
	
	public function disconnect() {
		if ( true === $this->getConnected() ) {
			$this->getConnection()->close();
			$this->setConnection(NULL)->setConnected(false);
		}
		return true;
	}

	public function query($sql) {
		$sql = trim($sql);
		
		if ( true === empty($sql) ) {
			throw new DataModelerException('The SQL statement is empty.');
		}
		
		if ( true === $this->getConnected() ) {
			$result = $this->getConnection()->query($sql);
			
			if ( true === $result instanceof mysqli_result ) {
				if ( true === $this->debug ) {
					$this->query_list['success'][] = $sql;
				}
				
				//return new Artisan_Db_Result($result);
			}
		}
		
		if ( false === $result ) {
			if ( true === $this->debug ) {
				$this->queryList['error'][] = $sql;
			}
			
			$error_string = $this->getConnection()->error;
			throw new DataModelerException("Failed to execute query: {$sql}. Datastore said: {$error_string}");
		}
		
		return $result;
	}
	
	public function multiQuery($sql) {
		$sql = trim($sql);
		
		if ( true === empty($sql) ) {
			throw new DataModelerException('The SQL statement is empty.');
		}
		
		if ( true === $this->getConnected() ) {
			$result = $this->getConnection()->multi_query($sql);
			
			if ( true === $result ) {
				if ( true === $this->debug ) {
					$this->queryList['success'][] = $sql;
				}
				
				/* Discard other results. */
				do {
					$result = $this->getConnection()->use_result();
					if ( false !== $result ) {
						$result->close();
					}
				} while ( $this->getConnection()->next_result() );
				
				return true;
			} else {
				$error_string = $this->getConnection()->error;
				throw new DataModelerException("Failed to execute query: {$sql}. Datastore said: {$error_string}");
			}
		}
		
		return false;
	}

/*
	public function select() {
		if ( NULL === $this->select ) {
			$this->select = new Artisan_Sql_Select($this);
		}
		return $this->select;
	}
	
	public function insert() {
		if ( NULL === $this->insert ) {
			$this->insert = new Artisan_Sql_Insert($this);
		}
		$this->insert->setReplace(false);
		return $this->insert;
	}
	
	public function update() {
		if ( NULL === $this->update ) {
			$this->update = new Artisan_Sql_Update($this);
		}
		return $this->update;
	}

	public function delete() {
		if ( NULL === $this->delete ) {
			$this->delete = new Artisan_Sql_Delete($this);
		}
		return $this->delete;
	}

	public function replace() {
		if ( NULL == $this->insert ) {
			$this->insert = new Artisan_Sql_Insert($this);
		}
		$this->insert->setReplace(true);
		return $this->insert;
	}
*/

	public function insertId() {
		if ( true === $this->getConnection() instanceof mysqli ) {
			return mysqli_insert_id($this->getConnection());
		}
		return 0;
	}
	
	public function affectedRows() {
		if ( true === $this->getConnection() instanceof mysqli ) {
			return $this->getConnection()->affected_rows;
		}
		return 0;
	}
	
	public function escape($value) {
		if ( true === $this->getConnection() instanceof mysqli ) {
			return $this->getConnection()->real_escape_string($value);
		}
		return addslashes($value);
	}
	
	
	
}