<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModelerTest\TestCase,
	\DataModeler\SqlResult;

require_once 'DataModeler/Model.php';
require_once 'DataModeler/SqlResult.php';

class SqlResultTest extends TestCase {
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testAttachModel_MustBeModelObject() {
		$sqlResult = new SqlResult;
		$sqlResult->attachModel(NULL);
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testAttachPdoStatement_MustBePdoStatementObject() {
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement(NULL);
	}
	
	/**
	 * @dataProvider providerFindParameters
	 */
	public function testFindFirst_ReturnsNvpArrayWithSingleParameter($parameters) {
		$this->buildPdo();
		$statement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($statement);

		$row = $sqlResult->findFirst($parameters);
		
		$this->assertTrue(is_array($row));
		$this->assertGreaterThan(0, count($row));
	}
	
	public function _testFindFirst_ReturnsNvpArrayWithMultipleParameters() {
		$this->buildPdo();
		$statement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ? AND price > ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($statement);

		$row = $sqlResult->findFirst(1, 8.00);
	
		$this->assertGreaterThan(0, count($row));
	}
	
	
	
	
	
	
	
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testFree_CanNotFind() {
		$this->buildPdo();
		$statement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($statement);
		
		$sqlResult->findFirst(1);
		$sqlResult->free();
		
		$sqlResult->findFirst(1);
	}
	
	public function providerFindParameters() {
		$obj = new \stdClass;
		$obj->product_id = 1;
		
		return array(
			array(1),
			array(array(1)),
			array($obj)
		);
	}
	
}