<?php

require_once 'DataAdapterPdo.php';
require_once 'DataAdapterPdoResult.php';

class DataAdapterPdoMysql extends DataAdapterPdo {
	public function connect() {
		$this->setConnected(false);
		
		$param_list = $this->getParamList();
		$server = $this->getServer();
		$username = $this->getUsername();
		$password = $this->getPassword();
		$database = $this->getDatabase();
		$port = $this->getPort();
		
		if ( 0 === $port ) {
			$port = 3306;
		}
		
		$mysql_dsn = "mysql:host={$server};port={$port};dbname={$database}";
		
		try {
			$connection = new PDO($mysql_dsn, $username, $password, $param_list);
			
			$this->setConnection($connection);
			$this->setConnected(true);

		} catch ( PDOException $e ) {
			throw new DataModelerException($e->getMessage());
		}
	}
	
	
	public function query($sql, array $value_list=array()) {
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

			$pdo_result = new DataAdapterPdoResult($statement);
			return $pdo_result;
		} catch ( PDOException $e ) {
			throw new DataModelerException($e->getMessage());
		}
		return false;
	}

}