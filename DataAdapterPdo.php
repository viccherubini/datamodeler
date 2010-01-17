<?php


abstract class DataAdapterPdo {
	private $param_list = array();
	private $driver = NULL;
	
	private $server = NULL;
	private $database = NULL;
	private $username = NULL;
	private $password = NULL;
	private $port = 0;
	
	private $affected_rows = 0;
	
	const DRIVER_MYSQL = 'mysql';
	const DRIVER_SQLITE = 'sqlite';
	const DRIVER_PGSQL = 'pgsql';
	const DRIVER_ODBC = 'odbc';

	public function __construct($driver, $server=NULL, $database=NULL, $username=NULL, $password=NULL, $port=0, array $param_list=array()) {
		$this->setDriver($driver);
		$this->setServer($server);
		$this->setDatabase($database);
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setPort($port);
		$this->setParamList($param_list);
	}

	public function __destruct() {
		
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
	
	public function setDriver($driver) {
		if ( false === in_array($driver, array(self::DRIVER_MYSQL, self::DRIVER_SQLITE, self::DRIVER_PGSQL)) ) {
			$driver = self::DRIVER_MYSQL;
		}
		
		$this->driver = $driver;
		return $this;
	}

	public function setServer($server) {
		$this->server = $server;
		return $this;
	}
	
	public function setDatabase($database) {
		$this->database = $database;
		return $this;
	}
	
	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}
	
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}
	
	public function setPort($port) {
		$this->port = intval($port);
		return $this;
	}
	
	public function setParamList(array $param_list) {
		$this->param_list = $param_list;
		return $this;
	}
	
	public function setAffectedRows($affected_rows) {
		$affected_rows = abs(intval($affected_rows));
		$this->affected_rows = $affected_rows;
		return $this;
	}
	



	public function getConnection() {
		return $this->connection;
	}
	
	public function getConnected() {
		return $this->connected;
	}
	
	public function getDriver() {
		return $this->driver;
	}
	
	public function getServer() {
		return $this->server;
	}
	
	public function getDatabase() {
		return $this->database;
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	public function getPassword() {
		return $this->password;
	}
	
	public function getPort() {
		return $this->port;
	}
	
	public function getParamList() {
		return (array)$this->param_list;
	}

	public function getAffectedRows() {
		return $this->affected_rows;
	}


	
	abstract public function connect();
	
	
	public function insertId() {
		if ( true === $this->getConnected() ) {
			return $this->getConnection()->lastInsertId();
		}
		return 0;
	}
	
	public function escape($value) {
		if ( true === $this->getConnected() && $this->getDriver() != self::DRIVER_ODBC ) {
			return $this->getConnection()->quote($value);
		}
		return addslashes($value);
	}
	
	protected function hasDriver() {
		$driver = $this->getDriver();
		if ( true === empty($driver) ) {
			throw new DataModelerException('No driver was specified, PDO can not be used until a driver is specified.');
		}
		return true;
	}
}