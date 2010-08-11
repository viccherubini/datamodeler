<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use DataModelerTest\TestCase,
	DataModeler\Model,
	DataModeler\Sql,
	DataModeler\Iterator;

require_once 'lib/Sql.php';

class SqlTest extends TestCase {
	
	private $pdo = NULL;
	private $product = NULL;
	private $user = NULL;
	
	public function setUp() {
		$this->pdo = new \PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		
		$sqlFile = DIRECTORY_DATA . 'SqlTest-' . DB_TYPE . '.sql';

		if ( !is_file($sqlFile) ) {
			throw new \Exception("Failed to load file {$sqlFile}.");
		}

		$sqlData = @file_get_contents($sqlFile);
		
		// Execute the data because in mysql 5.0 exec() keeps the buffer
		// open so successive queries can not be executed. This was fixed 
		// in mysql 5.1
		switch ( DB_TYPE ) {
			case 'sqlite': {
				$this->pdo->exec($sqlData);
				break;
			}
			
			case 'mysql': {
				$statement = $this->pdo->prepare($sqlData);
				$statement->execute();
				break;
			}
		}
		
		//$this->product = $this->buildMockProduct();
		//$this->user = $this->buildMockUser();
	}

	public function tearDown() {
		$this->pdo = NULL;
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testAttachPdo_AttachesPdo() {
		$sql = new Sql;
		
		$sql->attachPdo(NULL);
	}
	
	public function _testGetPdo_ReturnsPdoObject() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertTrue($sql->getPdo() instanceof \PDO);
	}
	
	public function _testGetPrepareCount_ReturnsIntegerPrepareCount() {
		$sql = new Sql;
		
		$prepareCount = 10;
		$sql->setPrepareCount($prepareCount);
		$this->assertEquals($prepareCount, $sql->getPrepareCount());
	}
	
	public function _testGetQueryString_ReturnsUnparsedQuery() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->prepare($this->product);
		$sql->get(1);
		
		$this->assertType('string', $sql->getQueryString());
	}
	
	public function _testGetQueryString_ReturnsNullWhenNoQuery() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertNull($sql->getQueryString());
	}
	
	public function _testSetSqlHash_MustBeSha1() {
		$sql = new Sql;
		
		$query = "SELECT * FROM products";
		$querySha1 = sha1($query);
		
		$sql->setSqlHash($querySha1);
		$this->assertEquals($querySha1, $sql->getSqlHash());
	}
	
	public function _testGetSqlHash_MustBeSha1() {
		// Probably a pointless test
		$sql = new Sql;
		
		$query = "SELECT * FROM products";
		$querySha1 = sha1($query);
		
		$sql->setSqlHash($querySha1);
		$this->assertEquals(strlen($querySha1), strlen($sql->getSqlHash()));
	}
	
	public function testBegin_StartsTransaction() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertTrue($sql->begin());
	}

	public function testCommit_SavesTransaction() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
/*
		$order1 = $this->buildMockOrder();
		$order2 = $this->buildMockOrder();
		
		$order1->setDateCreated($sql->now())
			->setCustomerId(mt_rand(1, 100))
			->setName('My New Order');
		
		$sql->begin();
		$order1 = $sql->save($order1);
		$sql->commit();
		
		// Reload the order
		$order2 = $sql->prepare($order2)->get($order1->id());
		
		$this->assertTrue($order2->exists());
*/
	}
	
	public function testRollback_RevertsATransaction() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
/*
		$order1 = $this->buildMockOrder();
		$order2 = $this->buildMockOrder();
		
		$order1->setDateCreated($sql->now())
			->setCustomerId(mt_rand(1, 100))
			->setName('My New Order');
		
		$sql->begin();
		$order1 = $sql->save($order1);
		$sql->rollback();
		
		// Reload the order
		$order2 = $sql->prepare($order2)->get($order1->id());
		
		$this->assertFalse($order2->exists());
*/
	}
	
	
	/**
	 * @_dataProvider providerFindModel
	 */
	public function _testFind_ReturnsModel(Model $model, $where, $inputParameters) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertFalse($model->exists());
		
		$sql->prepare($model, $where);
		$model = $sql->find($inputParameters);
		
		$this->assertModel($model);
		$this->assertTrue($model->exists());
	}
	
	/**
	 * @_dataProvider providerFindModel
	 */
	public function _testFindAll_ReturnsListOfModels(Model $model, $where, $inputParameters) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertFalse($model->exists());
		
		$sql->prepare($model, $where);
		$modelList = $sql->findAll($inputParameters);
		
		$this->assertModelList($modelList);
	}
	
	public function _testSave_SetsInsertId() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = $this->buildMockProduct();
		$product->setName('Product 1');
		$product->setPrice(10.99);
		
		$product = $sql->save($product);
		
		$this->assertGreaterThan(0, $product->id());
	}
	
	public function _testSave_PreparesOnceForSameModel() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		for ( $i=0; $i<10; $i++ ) {
			$price = round(mt_rand(1, 25) + (mt_rand(1, 25) / mt_rand(1, 25)), 2);
			
			$product = $this->buildMockProduct();
			$product->setName("Product {$i}");
			$product->setPrice($price);
			$product->setSku("P{$i}");
			
			$sql->save($product);
		}
		
		$this->assertEquals(1, $sql->getPrepareCount());
	}
	
	public function _testSave_PreparesTwiceForDifferentModels() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = $this->buildMockProduct();
		$product->setName('Product 4');
		$product->setPrice(10.99);
		$product->setSku('P4');
		
		$user = $this->buildMockUser();
		$user->setUsername('leftnode');
		$user->setPassword('password_test');
		
		$sql->save($product);
		$sql->save($user);
		
		$this->assertEquals(2, $sql->getPrepareCount());
	}
	
	public function _testSave_PreparesTwiceForDifferentSaveTypes() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product1 = $this->buildMockProduct();
		$product1->id(1);
		$product1->setName('Product 1 *Updated*');
		$product1->setPrice(42.56);
		$product1->setSku('P1_NEW');
		
		$product2 = $this->buildMockProduct();
		$product2->setName('Product 2');
		$product2->setPrice(8.56);
		$product2->setSku('P5');
		
		$sql->save($product1); // update
		$sql->save($product2); // insert
		
		$this->assertEquals(2, $sql->getPrepareCount());
	}
	
	public function _testSave_PreparesOnceForUpdatingSameModel() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product1 = $this->buildMockProduct();
		$product1->id(1);
		$product1->setName('Product 1 *updated*');
		
		$product2 = clone $product1;
		
		$sql->save($product1); // update
		$sql->save($product2); // same update
		
		$this->assertEquals(1, $sql->getPrepareCount());
	}
	
	public function _testSave_PreparesTwiceForInsertAndTwoUpdatesOnSameModel() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = $this->buildMockProduct();
		$product->setName('New Product');
		$product->setPrice(93.22);
		$product->setSku('AKDUU_DKAE19');
		
		$product = $sql->save($product); // insert
		
		$product->setPrice(99.33);
		$sql->save($product); // update #1
		
		$product->setSku('DKEOE19');
		$sql->save($product); // update #2
		
		$this->assertTrue($product->exists());
		$this->assertEquals(2, $sql->getPrepareCount());
	}
	
	public function _testSave_PreparesEachTimeForNewAttributes() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$productNameUpdated = 'Product 1 Prime';
		
		$product = $this->buildMockProduct();
		$product->id(1); // fake load
		$product->setPrice(99.33);
		
		$sql->save($product); // update, first prepare
		
		$product->setName($productNameUpdated);
		$sql->save($product); // update, but should prepare again
		
		$this->assertEquals(2, $sql->getPrepareCount());
		
		// Reload the product to test values were actually updated
		$sql->prepare($product);
		$product = $sql->get(1);
		
		$this->assertEquals($productNameUpdated, $product->getName());
		$this->assertEquals(3, $sql->getPrepareCount());
	}
	
	public function _testSave_CanInsertLargeObjects() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		// Create 1MB of fake data
		$objectData = str_repeat('A', 1024*1024);
		
		$largeObject = $this->buildMockModel('large_object', 'large_object_id');
		$largeObject->setObjectData($objectData);
		
		$largeObject = $sql->save($largeObject);
		
		$this->assertTrue($largeObject->exists());
		
		$sql->prepare($largeObject)->get($largeObject->id());
		
		$this->assertEquals($objectData, $largeObject->getObjectData());
		
		unset($objectData);
		unset($largeObject);
	}
	
	/**
	 * @dataProvider providerQueryAndInputParameters
	 */
	public function _testQuery_CanExecuteValidQuery($query, $inputParameters) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->query($query, $inputParameters);
		
		$this->assertPdoStatement($sql->getStatement());
	}
	
	public function testCountOf_ReturnsRowCount() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$rowCount = $sql->countOf($this->buildMockProduct());
		
		$this->assertGreaterThan(0, $rowCount);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testCountOf_RequiresPdo() {
		$sql = new Sql;
		$sql->countOf($this->buildMockProduct());
	}
	
	public function providerFindModel() {
		$user = $this->buildMockUser();
		$product = $this->buildMockProduct();
		
		return array(
			array($product, 'id = :id', array('id' => 1)),
			array($product, 'id = ?', array(1)),
			array($product, 'id = :id OR sku = :sku', array('id' => 1, 'sku' => 'P2')),
			array($product, 'id = :id AND sku = :sku', array('id' => 1, 'sku' => 'P1')),
			array($product, 'price > :price', array('price' => 10.99)),
			array($product, 'name = :name', array('name' => 'Product 1')),
			array($user, 'username = :username', array('username' => 'vcherubini')),
			array($user, 'username = :username AND password = :password', array('username' => 'vcherubini', 'password' => 'password1')),
			array($user, 'age = :age AND favorite_book = :favorite_book', array('age' => 25, 'favorite_book' => 'xUnit Test Patterns'))
		);
	}
	
	public function providerQueryAndInputParameters() {
		return array(
			array("SELECT * FROM products WHERE id = :id", array('id' => 1)),
			array("SELECT * FROM products WHERE id <> :id", array('id' => 1)),
			array("SELECT * FROM products WHERE name = :name", array('name' => 'Product 1')),
			array("UPDATE products SET name = ?, price = ?, sku = ? WHERE id = ?", array('Product 1 *updated*', 893.99, 'P1U', 1)),
			array("INSERT INTO products VALUES(NULL, ?, ?, ?)", array('New Product', 98.33, 'NP1'))
		);
	}
}