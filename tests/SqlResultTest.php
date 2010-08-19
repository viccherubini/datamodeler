<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModelerTest\TestCase,
	\DataModeler\SqlResult;

require_once 'DataModeler/Model.php';
require_once 'DataModeler/SqlResult.php';

require_once 'Product.php';

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

	public function testFind_ArrayWithParameter() {
		$this->buildPdo();
		$statement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($statement);

		$row = $sqlResult->find(1);
		
		$this->assertTrue(is_array($row));
		$this->assertGreaterThan(0, count($row));
	}
	
	public function testFind_ArrayWithParameters() {
		$this->buildPdo();
		$statement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ? AND price > ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($statement);

		$row = $sqlResult->find(array(2, 0.50));
		
		$this->assertTrue(is_array($row));
		$this->assertGreaterThan(0, count($row));
	}
	
	public function testFind_RowExists() {
		$this->buildPdo();
		$statement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($statement);
		
		$row = $sqlResult->find(1000);
		$this->assertFalse($row);
		
		$row = $sqlResult->find(array(1000));
		$this->assertFalse($row);
	}
	
	public function testFind_ModelWithParameter() {
		$this->buildPdo();
		$statement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$product = new Product;
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($statement)
			->attachModel($product);

		$productFound = $sqlResult->find(1);
		
		$this->assertTrue($productFound->exists());
	}
	
	public function testFind_ModelWithParameters() {
		$this->buildPdo();
		$statement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ? AND price > ?');
		
		$product = new Product;
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($statement)
			->attachModel($product);

		$productFound = $sqlResult->find(array(2, 0.50));
		
		$this->assertTrue($productFound->exists());
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testFree_CanNotFind() {
		$this->buildPdo();
		$statement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($statement);
		
		$sqlResult->find(1);
		$sqlResult->free();
		
		$sqlResult->find(1);
	}
	
}