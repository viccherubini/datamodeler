<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Adapter;

use DataModelerTest\TestCase, DataModeler\Adapter\Sql;

require_once 'lib/Adapter/Sql.php';

class SqlTest extends TestCase {
	
	private $pdo = NULL;
	
	private $sql_create_table = NULL;
	private $sql_list = array();
	
	
	
	public function setUp() {
		$this->pdo = new \PDO('sqlite::memory:');
		
		$this->sql_create_table = "CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT, price REAL, sku TEXT)";
		
		$this->sql_list = array(
			"INSERT INTO products VALUES (NULL, 'Product 1', 10.95, 'P1')",
			"INSERT INTO products VALUES (NULL, 'Product 2', 18.95, 'P2')",
			"INSERT INTO products VALUES (NULL, 'Product 3', 22.97, 'P3')"
		);
		
		$this->pdo->exec($this->sql_create_table);
		
		foreach ( $this->sql_list as $sql ) {
			$this->pdo->exec($sql);
		}
	}


	public function tearDown() {
		$this->pdo = NULL;
	}
	

	public function testPdoCanBeAttached() {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$this->assertTrue($sql->getDb() instanceof \PDO);
	}
	
	
	/**
	 * @dataProvider providerRawExecuteQuery
	 */
	public function testRawExecuteReturnsTheRowCountOfTheQueryExecuted($sql_query, $expected_row_count) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$actual_row_count = $sql->rawExecute($sql_query);
		
		$this->assertEquals($actual_row_count, $expected_row_count);
	}
	

	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testRawExecuteDoesNotAllowSelectStatements() {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$sql->rawExecute("SELECT * FROM `products`");
	}


	public function providerRawExecuteQuery() {
		return array(
			array("UPDATE `products` SET price = price + 10.50 WHERE sku = 'P1'", 1),
			array("DELETE FROM `products` WHERE sku = 'P1'", 1),
			array("INSERT INTO `products` VALUES(NULL, 'Product 4', 58.93, 'P4')", 1)
		);
	}
	
}