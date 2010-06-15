<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Adapter;

use DataModelerTest\TestCase,
	DataModeler\Adapter\Sql,
	DataModeler\Iterator;

require_once 'lib/Adapter/Sql.php';

class SqlTest extends TestCase {
	
	private $pdo = NULL;
	
	public function setUp() {
		$this->pdo = new \PDO('sqlite::memory:');
		
		$sqlFile = DIRECTORY_DATA . 'SqlTest.sql';
		if ( true === is_file($sqlFile) ) {
			$sqlData = @file_get_contents($sqlFile);
			$this->pdo->exec($sqlData);
		}
	}


	public function tearDown() {
		$this->pdo = NULL;
	}
	

	public function testAttachDb_CanAttachPdoObject() {
		$sql = new Sql;
		$attached = $sql->attachDb($this->pdo);
	
		$this->assertTrue($attached instanceof Sql);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testRawExecute_RequiresPdo() {
		$sql = new Sql;
		$sql->rawExecute("SELECT * FROM `fake_table` WHERE id = 10");
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testRawExecute_RequiresSql() {
		$sql = new Sql;
		$sql->rawExecute(NULL);
	}
	
	/**
	 * @dataProvider providerRawExecuteQuery
	 */
	public function testRawExecute_ReturnsRowCount($sql_query, $expectedRowCount) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$this->assertEquals($sql->rawExecute($sql_query), $expectedRowCount);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testRawExecute_DoesNotAllowSelectStatements() {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$sql->rawExecute("SELECT * FROM `products`");
	}

	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testFind_RequiresDb() {
		$sql = new Sql;
		$sql->find($this->buildMockProduct());
	}
	
	/**
	 * @dataProvider providerInvalidWhere
	 * @expectedException \DataModeler\Exception
	 */
	public function testFind_CannotPrepareInvalidQuery($where) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$sql->find($this->buildMockProduct(), $where);
	}

	/**
	 * @dataProvider providerInvalidInputParameters
	 * @expectedException \DataModeler\Exception
	 */
	public function testFind_CannotExecuteInvalidInputParameters($where, $inputParameters) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$sql->find($this->buildMockProduct(), $where, $inputParameters);
	}
	
	/**
	 * @dataProvider providerValidWhereAndInputParameters
	 */
	public function testFind_ReturnsExistingModelObject($where, $inputParameters) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$product = $sql->find($this->buildMockProduct(), $where, $inputParameters);
		
		$this->assertModel($product);
		$this->assertTrue($product->exists());
	}

	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testFindAll_RequiresDb() {
		$sql = new Sql;
		$sql->findAll($this->buildMockProduct());
	}
	
	/**
	 * @dataProvider providerInvalidWhere
	 * @expectedException \DataModeler\Exception
	 */
	public function testFindAll_CannotPrepareInvalidQuery($where) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$sql->findAll($this->buildMockProduct(), $where);
	}

	/**
	 * @dataProvider providerInvalidInputParameters
	 * @expectedException \DataModeler\Exception
	 */
	public function testFindAll_CannotExecuteInvalidInputParameters($where, $inputParameters) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$sql->findAll($this->buildMockProduct(), $where, $inputParameters);
	}
	
	/**
	 * @dataProvider providerValidWhereAndInputParameters
	 */
	public function testFindAll_ReturnsIterator($where, $inputParameters) {
		$sql = new Sql;
		$sql->attachDb($this->pdo);
		
		$iterator = $sql->findAll($this->buildMockProduct(), $where, $inputParameters);
		
		$this->assertIterator($iterator);
	}
	
	
	
	
	
	
	
	


	public function providerRawExecuteQuery() {
		return array(
			array("UPDATE `products` SET price = price + 10.50 WHERE sku = 'P1'", 1),
			array("DELETE FROM `products` WHERE sku = 'P1'", 1),
			array("INSERT INTO `products` VALUES(NULL, 'Product 4', 58.93, 'P4')", 1),
			array("INSERT INTO `products` VALUES(NULL, 'Product''s 5', 56.24, 'P5')", 1)
		);
	}
	
	public function providerInvalidWhere() {
		return array(
			array("id = :i-d"),
			array("not_id = :name"),
			array("products = :Array AND face = :book OR tables.field = :table.field")
		);
	}
	
	public function providerInvalidInputParameters() {
		return array(
			array("id = :id", array(':invalid_field' => 'Field Value')),
			array("id = :id", array(':name' => 'Product 1')),
			array("sku = :sku AND id = :id", array(':sku' => 'P2', ':id' => 2, ':price' => 11.99))
		);
	}
	
	public function providerValidWhereAndInputParameters() {
		return array(
			array(NULL, array()),
			array("id = :id", array(':id' => 1)),
			array("id = :id", array('id' => 1)),
			array("name = :name", array(':name' => "Product 1")),
			array("name = :name", array('name' => "Product 1")),
			array("price = :price", array(':price' => 10.95)),
			array("price = :price", array('price' => 10.95)),
			array("price > :price AND sku != :sku", array(':price' => 8.95, ':sku' => 'P2')),
			array("price > :price AND sku != :sku", array(':price' => 8.95, 'sku' => 'P2')),
			array("price > :price AND sku != :sku", array('price' => 8.95, ':sku' => 'P2'))
		);
	}
	
	/*
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
	}*/
}