<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModelerTest\TestCase,
	\DataModeler\SqlResult;

require_once 'DataModeler/Model.php';
require_once 'DataModeler/Iterator.php';
require_once 'DataModeler/SqlResult.php';

require_once 'Product.php';

class SqlResultTest extends TestCase {

	public function setUp() {
		$this->buildPdo();
	}
	
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testAttachModel_MustBeModelObject() {
		$sqlResult = new SqlResult;
		$sqlResult->attachModel(NULL);
	}
	
	public function testAttachModel_AttachesModel() {
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($this->buildMockModel());
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testAttachPdoStatement_MustBePdoStatementObject() {
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement(NULL);
	}
	
	
	/**
	 * ##################################################
	 * find() with Arrays
	 * ##################################################
	 */
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testFind_RequiresPdoStatement() {
		$sqlResult = new SqlResult;
		
		$dbRow = $sqlResult->find(1);
	}
	
	public function testFind_FalseWithNoResults() {
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbRow = $sqlResult->find(10000);
		
		$this->assertFalse($dbRow);
	}

	public function testFind_ArrayWithNullParameter() {
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE date_updated IS NULL');

		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);
		
		$dbRow = $sqlResult->find();
		
		$this->assertTrue(is_array($dbRow));
		$this->assertGreaterThan(0, count($dbRow));
	}
	
	public function testFind_ArrayWithAnonymousParameter() {
		$productId = 1;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbRow = $sqlResult->find($productId);
		
		$this->assertTrue(is_array($dbRow));
		$this->assertEquals($productId, $dbRow['product_id']);
	}
	
	public function testFind_ArrayWithAnonymousParameters() {
		$productId = 1;
		$price = 0.99;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ? AND price > ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbRow = $sqlResult->find(array($productId, $price));
		
		$this->assertTrue(is_array($dbRow));
		$this->assertEquals($productId, $dbRow['product_id']);
		$this->assertGreaterThan($price, (float)$dbRow['price']);
	}
	
	public function testFind_ArrayWithNamedParameter() {
		$productId = 1;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = :product_id');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbRow1 = $sqlResult->find(array('product_id' => $productId));
		$dbRow2 = $sqlResult->find(array(':product_id' => $productId));
		
		$this->assertTrue(is_array($dbRow1));
		$this->assertTrue(is_array($dbRow2));
		
		$this->assertEquals($productId, $dbRow1['product_id']);
		$this->assertEquals($productId, $dbRow2['product_id']);
		
		$this->assertEquals($dbRow1, $dbRow2);
	}
	
	public function testFind_ArrayWithNamedParameters() {
		$productId = 1;
		$price = 0.99;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = :product_id AND price > :price');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbRow1 = $sqlResult->find(array('product_id' => $productId, 'price' => $price));
		$dbRow2 = $sqlResult->find(array(':product_id' => $productId, 'price' => $price));
		
		$this->assertTrue(is_array($dbRow2));
		$this->assertTrue(is_array($dbRow1));
		
		$this->assertEquals($productId, $dbRow1['product_id']);
		$this->assertEquals($productId, $dbRow2['product_id']);
		
		$this->assertGreaterThan($price, (float)$dbRow1['price']);
		$this->assertGreaterThan($price, (float)$dbRow2['price']);
		
		$this->assertEquals($dbRow1, $dbRow2);
	}
	
	
	/**
	 * ##################################################
	 * find() with Models
	 * ##################################################
	 */
	public function testFind_ModelWithNullParameter() {
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE date_updated IS NULL');

		$product = new Product;
		$this->assertFalse($product->exists());
		
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($product)->attachPdoStatement($pdoStatement);
		
		$product = $sqlResult->find();
		
		$this->assertTrue($product->exists());
	}
	
	public function testFind_ModelWithAnonymousParameter() {
		$productId = 1;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$product = new Product;
		$this->assertFalse($product->exists());
		
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($product)->attachPdoStatement($pdoStatement);

		$product = $sqlResult->find($productId);
		
		$this->assertTrue($product->exists());
		$this->assertEquals($productId, $product->id());
	}
	
	public function testFind_ModelWithAnonymousParameters() {
		$productId = 1;
		$price = 0.99;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ? AND price > ?');
		
		$product = new Product;
		$this->assertFalse($product->exists());
		
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($product)->attachPdoStatement($pdoStatement);

		$product = $sqlResult->find(array($productId, $price));
		
		$this->assertTrue($product->exists());
		$this->assertEquals($productId, $product->id());
		$this->assertGreaterThan($price, $product->getPrice());
	}
	
	public function testFind_ModelWithNamedParameter() {
		$productId = 1;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = :product_id');
		
		$product = new Product;
		$this->assertFalse($product->exists());
		
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($product)->attachPdoStatement($pdoStatement);

		$product1 = $sqlResult->find(array('product_id' => $productId));
		$product2 = $sqlResult->find(array(':product_id' => $productId));
		
		$this->assertTrue($product1->exists());
		$this->assertTrue($product2->exists());
		
		$this->assertEquals($productId, $product1->id());
		$this->assertEquals($productId, $product2->id());
		
		$this->assertTrue($product1->equalTo($product2));
		$this->assertTrue($product2->equalTo($product1));
	}
	
	public function testFind_ModelWithNamedParameters() {
		$productId = 1;
		$price = 0.99;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = :product_id AND price > :price');
		
		$product = new Product;
		$this->assertFalse($product->exists());
		
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($product)->attachPdoStatement($pdoStatement);

		$product1 = $sqlResult->find(array('product_id' => $productId, 'price' => $price));
		$product2 = $sqlResult->find(array(':product_id' => $productId, 'price' => $price));
		
		$this->assertTrue($product1->exists());
		$this->assertTrue($product2->exists());
		
		$this->assertEquals($productId, $product1->id());
		$this->assertEquals($productId, $product2->id());

		$this->assertGreaterThan($price, $product1->getPrice());
		$this->assertGreaterThan($price, $product2->getPrice());
		
		$this->assertTrue($product1->equalTo($product2));
		$this->assertTrue($product2->equalTo($product1));
	}
	
	
	/**
	 * ##################################################
	 * findAll() with Array Iterators
	 * ##################################################
	 */
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testFindAll_RequiresPdoStatement() {
		$sqlResult = new SqlResult;
		
		$dbRow = $sqlResult->findAll(1);
	}
	
	public function testFindAll_EmptyIteratorWithNoResults() {
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id > ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbIterator = $sqlResult->findAll(10000);
		
		$this->assertEquals(0, $dbIterator->length());
	}
	
	public function testFindAll_ArrayIteratorWithNullParameter() {
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE date_updated IS NULL');

		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);
		
		$dbIterator = $sqlResult->findAll();
		
		$this->assertGreaterThan(0, $dbIterator->length());
	}
	
	public function testFindAll_ArrayIteratorWithAnonymousParameter() {
		$productId = 1;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbIterator = $sqlResult->findAll($productId);
		
		$this->assertEquals(1, $dbIterator->length());
	}
	
	public function testFindAll_ArrayIteratorWithAnonymousParameters() {
		$productId = 1;
		$price = 0.99;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ? AND price > ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbIterator = $sqlResult->findAll(array($productId, $price));

		$this->assertGreaterThanOrEqual(1, $dbIterator->length());
	}
	
	public function testFindAll_ArrayIteratorWithNamedParameter() {
		$productId = 1;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = :product_id');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbIterator1 = $sqlResult->findAll(array('product_id' => $productId));
		$dbIterator2 = $sqlResult->findAll(array(':product_id' => $productId));
		
		$this->assertEquals(1, $dbIterator1->length());
		$this->assertEquals(1, $dbIterator2->length());
	}
	
	public function testFindAll_ArrayIteratorWithNamedParameters() {
		$productId = 1;
		$price = 0.99;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = :product_id AND price > :price');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbIterator1 = $sqlResult->findAll(array('product_id' => $productId, 'price' => $price));
		$dbIterator2 = $sqlResult->findAll(array(':product_id' => $productId, 'price' => $price));
		
		$this->assertGreaterThanOrEqual(1, $dbIterator1->length());
		$this->assertGreaterThanOrEqual(1, $dbIterator2->length());
	}
	

	/**
	 * ##################################################
	 * findAll() with Model Iterators
	 * ##################################################
	 */
	public function testFindAll_ModelIteratorWithNullParameter() {
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE date_updated IS NULL');

		$product = new Product;

		$sqlResult = new SqlResult;
		$sqlResult->attachModel($product)->attachPdoStatement($pdoStatement);
		
		$dbIterator = $sqlResult->findAll();
		
		$this->assertGreaterThan(0, $dbIterator->length());
	}
	
	public function testFindAll_ModelIteratorWithAnonymousParameter() {
		$productId = 1;
		
		$product = new Product;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($product)->attachPdoStatement($pdoStatement);

		$dbIterator = $sqlResult->findAll($productId);
		
		$this->assertEquals(1, $dbIterator->length());
	}
	
	public function testFindAll_ModelIteratorWithAnonymousParameters() {
		$productId = 1;
		$available = true;
		
		$product = new Product;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id <> ? AND available = ?');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);

		$dbIterator = $sqlResult->findAll(array($productId, $available));

		$this->assertGreaterThan(0, $dbIterator->length());
	}
	
	public function testFindAll_ModelIteratorWithNamedParameter() {
		$productId = 1;
		
		$product = new Product;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = :product_id');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($product)->attachPdoStatement($pdoStatement);

		$dbIterator1 = $sqlResult->findAll(array('product_id' => $productId));
		$dbIterator2 = $sqlResult->findAll(array(':product_id' => $productId));
		
		$this->assertEquals(1, $dbIterator1->length());
		$this->assertEquals(1, $dbIterator2->length());
	}
	
	public function testFindAll_ModelIteratorWithNamedParameters() {
		$productId = 3;
		$available = false;
		
		$product = new Product;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = :product_id AND available = :available');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachModel($product)->attachPdoStatement($pdoStatement);

		$dbIterator1 = $sqlResult->findAll(array('product_id' => $productId, 'available' => $available));
		$dbIterator2 = $sqlResult->findAll(array(':product_id' => $productId, 'available' => $available));
		
		$this->assertGreaterThanOrEqual(1, $dbIterator1->length());
		$this->assertGreaterThanOrEqual(1, $dbIterator2->length());
	}

	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testFree_CantReExecuteFind() {
		$productId = 1;
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM products WHERE product_id = :product_id');
		
		$sqlResult = new SqlResult;
		$sqlResult->attachPdoStatement($pdoStatement);
		
		$dbRow1 = $sqlResult->find($productId);
		
		$sqlResult->free();
		
		$dbRow2 = $sqlResult->find($productId);
	}
}