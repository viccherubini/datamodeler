<?php

declare(encoding='UTF-8');
namespace DataModeler\Adapter;

class Sql {
	
	private $db;
	
	public function __construct() {
		
	}
	
	public function __destruct() {
		
		
	}
	
	public function attachDb(\PDO $db) {
		$this->db = $db;
		return $this;
	}
	
	
	
	
	
	public function getDb() {
		return $this->db;
	}
}