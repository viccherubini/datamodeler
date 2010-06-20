<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Adapter;

use DataModelerTest\TestCase,
	DataModeler\Model,
	DataModeler\Adapter\Sql,
	DataModeler\Iterator;

require_once 'lib/Adapter/Sql.php';

class SqlTest extends TestCase {
	
	private $pdo = NULL;
	private $product = NULL;
	private $user = NULL;
	
	public function setUp() {
		$this->pdo = new \PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		
		$sqlFile = DIRECTORY_DATA . 'SqlTest-' . DB_TYPE . '.sql';
		if ( true === is_file($sqlFile) ) {
			$sqlData = @file_get_contents($sqlFile);
			$this->pdo->exec($sqlData);
		}
		
		$this->product = $this->buildMockProduct();
		$this->user = $this->buildMockUser();
	}

	public function tearDown() {
		$this->pdo = NULL;
	}

	public function testAttachPdo_CanAttachPdo() {
		$sql = new Sql;
		
		$this->assertSql($sql->attachPdo($this->pdo));
	}
	
	public function testGetStatement_ReturnsPdoStatement() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->prepare($this->product);
		$sql->get(1);
		
		$this->assertTrue($sql->getStatement() instanceof \PDOStatement);
	}
	
	public function testGetQueryString_ReturnsUnparsedQuery() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->prepare($this->product);
		$sql->get(1);
		
		$this->assertType('string', $sql->getQueryString());
	}
	
	public function testGetQueryString_ReturnsNullWhenNoQuery() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertNull($sql->getQueryString());
	}

	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testPrepare_RequiresPdo() {
		$sql = new Sql;
		
		$sql->prepare($this->product);
	}
	
	public function testPrepare_PreparesOnceForSameModel() {
		$product1 = $this->buildMockProduct();
		$product2 = $this->buildMockProduct();
		
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->prepare($product1);
		$sql->prepare($product2);
		
		$this->assertEquals(1, $sql->getPrepareCount());
	}
	
	public function testPrepare_PreparesOnceForEachDifferentModel() {
		$product1 = $this->buildMockProduct();
		$product2 = $this->buildMockProduct();
		$user1 = $this->buildMockUser();
		$user2 = $this->buildMockUser();
		
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->prepare($product1);
		$sql->prepare($product2);
		$sql->prepare($user1);
		$sql->prepare($user2);
		
		$this->assertEquals(2, $sql->getPrepareCount());
	}

	public function testGet_ReturnsModelIfFound() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->prepare($this->product);
		$product = $sql->get(1);
		
		$this->assertModel($product);
		$this->assertTrue($product->exists());
	}
	
	public function testGet_ReturnsEmptyModelIfNotFound() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->prepare($this->product);
		$product = $sql->get(10000);
		
		$this->assertModel($product);
		$this->assertFalse($product->exists());
	}
	
	/**
	 * @dataProvider providerFindModel
	 */
	public function testFind_ReturnsModel(Model $model, $where, $inputParameters) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertFalse($model->exists());
		
		$sql->prepare($model, $where);
		$model = $sql->find($inputParameters);
		
		$this->assertModel($model);
		$this->assertTrue($model->exists());
	}
	
	/**
	 * @dataProvider providerFindModel
	 */
	public function testFindAll_ReturnsListOfModels(Model $model, $where, $inputParameters) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertFalse($model->exists());
		
		$sql->prepare($model, $where);
		$modelList = $sql->findAll($inputParameters);
		
		$this->assertModelList($modelList);
	}
	
	public function testSave_SetsInsertId() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = $this->buildMockProduct();
		$product->setName('Product 1');
		$product->setPrice(10.99);
		
		$product = $sql->save($product);
		
		$this->assertGreaterThan(0, $product->id());
	}
	
	public function testSave_PreparesOnceForSameModel() {
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
	
	public function testSave_PreparesTwiceForDifferentModels() {
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
	
	public function testSave_PreparesTwiceForDifferentSaveTypes() {
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
	
	public function testSave_PreparesOnceForUpdatingSameModel() {
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
	
	public function testSave_PreparesTwiceForInsertAndTwoUpdatesOnSameModel() {
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
	
	public function testSave_PreparesEachTimeForNewAttributes() {
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
	
	public function testSave_CanInsertLargeObjects() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		// Create 1MB of fake data
		$objectData = str_repeat('A', 1024*1024);
		
		$largeObject = $this->buildMockModel('large_object', 'large_object_id');
		$largeObject->setObjectData($objectData);
		
		$largeObject = $sql->save($largeObject);
		
		$this->assertTrue($largeObject->exists());
	}
	
	/**
	 * @dataProvider providerQueryAndInputParameters
	 */
	public function testQuery_CanExecuteValidQuery($query, $inputParameters) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->query($query, $inputParameters);
		
		$this->assertPdoStatement($sql->getStatement());
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