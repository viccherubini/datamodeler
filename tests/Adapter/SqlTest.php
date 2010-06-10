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
	

	public function testAttachDb_CanAttachPdoObject() {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$this->assertTrue($sql->getDb() instanceof \PDO);
	}
	
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testQuery_RequiresQuery() {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$sql->query(NULL);
	}
	
	
	/**
	 * @dataProvider providerPreparedQuery
	 */
	public function testQuery_ReturnsPdoStatementObject($query, $input_parameters) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$query_result = $sql->query($query, $input_parameters);
		
		$this->assertTrue($query_result instanceof \PDOStatement);
	}
	
	/**
	 * @dataProvider providerInvalidPreparedQuery
	 * @expectedException \DataModeler\Exception
	 */
	public function testQuery_ThrowsErrorOnInvalidQuery($query) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$sql->query($query);
	}
	
	
	/**
	 * @dataProvider providerPreparedQueryWithInvalidInputParameters
	 * @expectedException \DataModeler\Exception
	 */
	public function testQuery_ThrowsErrorOnInvalidParameters($query, $input_parameters) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$sql->query($query, $input_parameters);
	}
	
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testRawExecute_RequiresPdo() {
		$sql = new Sql;
		
		$sql->rawExecute("SELECT * FROM `fake_table` WHERE id = 10");
	}
	
	
	/**
	 * @dataProvider providerRawExecuteQuery
	 */
	public function testRawExecute_ReturnsRowCount($sql_query, $expected_row_count) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$actual_row_count = $sql->rawExecute($sql_query);
		
		$this->assertEquals($actual_row_count, $expected_row_count);
	}
	

	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testRawExecute_DoesNotAllowSelectStatements() {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$sql->rawExecute("SELECT * FROM `products`");
	}


	public function testRawQuery_ReturnsPdoStatementObject() {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$result_pdo_statement = $sql->rawQuery("SELECT * FROM `products`");
		
		$this->assertTrue($result_pdo_statement instanceof \PDOStatement);
	}

	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testRawQuery_RequiresPdo() {
		$sql = new Sql;
		
		$result_pdo_statement = $sql->rawQuery("SELECT * FROM `fake_table` WHERE id = 10");
	}


	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testWrite_RequiresPdo() {
		$sql = new Sql;
		
		$model = $this->buildMockModel();
		$model->table('products');
		
		$sql->write($model);
	}
	
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testWrite_ModelRequiresTable() {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$model = $this->buildMockModel();
		
		$sql->write($model);
	}


	public function providerRawExecuteQuery() {
		return array(
			array("UPDATE `products` SET price = price + 10.50 WHERE sku = 'P1'", 1),
			array("DELETE FROM `products` WHERE sku = 'P1'", 1),
			array("INSERT INTO `products` VALUES(NULL, 'Product 4', 58.93, 'P4')", 1)
		);
	}
	
	
	public function providerInvalidPreparedQuery() {
		return array(
			array("SELECT * FROM `invalid_table` WHERE id = :id"),
			array("SELECT * FROM `products` WHERE not_id = :name"),
			array("UPDATE `products` SET invalid_name = :name WHERE id = :id")
		);
	}
	
	
	public function providerPreparedQueryWithInvalidInputParameters() {
		return array(
			array("SELECT * FROM `products` WHERE id = :id", array(':name' => 'Vic Cherubini')),
			array("SELECT * FROM `products` WHERE id = :name", array(':id' => 'Vic Cherubini')),
			array("UPDATE `products` SET name = :name WHERE id = :id", array(':xname' => 'Vic Cherubini'))
		);
	}
	
	
	public function providerPreparedQuery() {
		return array(
			array("SELECT * FROM `products` WHERE id = :id", array(':id' => 2)),
			array("SELECT * FROM `products` WHERE id = :id AND sku = :sku", array(':id' => 2, ':sku' => 'P2')),
			array("SELECT * FROM `products` WHERE name = :name", array(':name' => "Baba O'Reilly")),
			array("UPDATE `products` SET name = :name WHERE id = :id", array(':name' => "Baba O'Reilly", ':id' => 1))
		);
	}
}