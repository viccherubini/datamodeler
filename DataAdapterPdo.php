<?php

require_once 'DataAdapterAbstract.php';

class DataAdapterPdo extends DataAdapterAbstract {
	private $param_list = array();
	private $driver = NULL;
	
	const DRIVER_MYSQL = 'mysql';
	const DRIVER_SQLITE = 'sqlite';
	const DRIVER_PGSQL = 'pgsql';

	public function setDriver($driver) {
		if ( false === in_array($driver, array(self::DRIVER_MYSQL, self::DRIVER_SQLITE, self::DRIVER_PGSQL)) ) {
			$driver = self::DRIVER_MYSQL;
		}
		
		$this->driver = $driver;
		return $this;
	}
	
	public function setParamList(array $param_list) {
		$this->param_list = $param_list;
		return $this;
	}
	
	
	
	public function getDriver() {
		return $this->driver;
	}
	
	public function getParamList() {
		return (array)$this->param_list;
	}




	public function factory($driver) {
		return $this->setDriver($driver);
	}
	
	public function connect() {
		$this->hasDriver();
		
		$this->setConnected(false);
		
		$driver = $this->getDriver();
		$param_list = $this->getParamList();
		$server = $this->config['server'];
		$username = $this->config['username'];
		$password = $this->config['password'];
		$database = $this->config['database'];
		
		$dsn = "{$driver}:dbname={$database};host={$server}";
		
		try {
			$connection = new PDO($dsn, $username, $password, $param_list);
			
			$this->setConnection($connection);
			$this->setConnected(true);

		} catch ( PDOException $e ) {
			throw new DataModelerException($e->getMessage());
		}
	}
	
	public function close() {
		
	}
	
	public function disconnect() {
		
	}
	
	public function query() {
		
	}
	
	public function multiQuery() {
		
	}
	
	public function insertId() {
		
	}
	
	public function affectedRows() {
		
	}
	
	public function escape() {
		
	}
	
	private function hasDriver() {
		$driver = $this->getDriver();
		if ( true === empty($driver) ) {
			throw new DataModelerException('No driver was specified, PDO can not be used until a driver is specified.');
		}
		return true;
	}
}