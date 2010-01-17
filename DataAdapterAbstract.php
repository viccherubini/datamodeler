<?php

abstract class DataAdapterAbstract {
	protected $connection = NULL;
	protected $config = NULL;
	protected $connected = false;
	protected $select = NULL;
	protected $update = NULL;
	protected $insert = NULL;
	protected $delete = NULL;
	protected $query_list = array();
	protected $debug = false;
	
	public function __construct($config = array()) {
		$this->setConfig($config);
	}

	public function __destruct() {
		unset($this->config, $this->select, $this->insert, $this->delete, $this->query_list);
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
	
	public function setConfig($config) {
		$this->config = $config;
		return $this;
	}
	
	public function getConnection() {
		return $this->connection;
	}
	
	public function getConnected() {
		return $this->connected;
	}

	public function getConfig() {
		return $this->config;
	}

	public function getQueryList() {
		return $this->query_list;
	}
	
	
	abstract public function connect();
	abstract public function close();
	abstract public function disconnect();
	abstract public function query($sql);
	abstract public function multiQuery($sql);
	abstract public function insertId();
	abstract public function affectedRows();
	abstract public function escape($value);
}