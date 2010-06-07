<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Adapter;

use DataModelerTest\TestCase, DataModeler\Adapter\Sql;

require_once 'lib/Adapter/Sql.php';

class SqlTest extends TestCase {
	
	private $pdo = NULL;
	
	public function setUp() {
		$this->pdo = new \PDO('sqlite::memory:');
		
		$sql_file = DIRECTORY_DATA . 'SqlTest.sql';
		if ( true === is_file($sql_file) ) {
			$sql_data = file_get_contents($sql_file);
			
			$this->pdo->exec($sql_data);
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
	 * @expectedException \DataModeler\Exception
	 */
	public function testRawExecuteCanNotBeExecutedIfNoPdoAttached() {
		$sql = new Sql;
		
		$sql->rawExecute("SELECT * FROM `fake_table` WHERE id = 10");
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


	public function testRawQueryReturnsPdoStatementObject() {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$result_pdo_statement = $sql->rawQuery("SELECT * FROM `products`");
		
		$this->assertTrue($result_pdo_statement instanceof \PDOStatement);
	}

	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testRawQueryCanNotBeExecutedIfNoPdoAttached() {
		$sql = new Sql;
		
		$result_pdo_statement = $sql->rawQuery("SELECT * FROM `fake_table` WHERE id = 10");
	}




	public function providerRawExecuteQuery() {
		return array(
			array("UPDATE `products` SET price = price + 10.50 WHERE sku = 'P1'", 1),
			array("DELETE FROM `products` WHERE sku = 'P1'", 1),
			array("INSERT INTO `products` VALUES(NULL, 'Product 4', 58.93, 'P4')", 1)
		);
	}
	
	
	public function providerPreparedQuery() {
		return array(
			array("SELECT * FROM `products` WHERE id = :id", array(':id' => 2), 1),
			array("SELECT * FROM `products` WHERE id = :id AND sku = :sku", array(':id' => 2, ':sku' => 'P2'), 1),
			array("SELECT * FROM `products` WHERE id <> :id", array(':id' => 1), 3),
			array("SELECT * FROM `products` WHERE id = :id", array(':id' => 2), 1)
		);
		
		
	}
	
}