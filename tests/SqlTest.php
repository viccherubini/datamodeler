<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModelerTest\TestCase,
	\DataModeler\Model,
	\DataModeler\Sql,
	\DataModeler\Iterator;

require_once 'DataModeler/Sql.php';

require_once DIRECTORY_MODELS . 'Order.php';
require_once DIRECTORY_MODELS . 'Product.php';
require_once DIRECTORY_MODELS . 'User.php';

class SqlTest extends TestCase {
	
	private $pdo = NULL;
	private $order = NULL;
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
		
		$this->order = new Order;
		$this->product = new Product;
		$this->user = new User;
	}

	public function tearDown() {
		$this->pdo = NULL;
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testAttachPdo_MustBePdo() {
		$sql = new Sql;
		
		$sql->attachPdo(NULL);
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testAttachModel_MustBeModel() {
		$sql = new Sql;
		
		$sql->attachModel(new \stdClass);
	}
	
	/**
	 * @dataProvider providerModelAndParameters
	 */
	public function testSingleQuery_FindsModel($model, $where, $parameters) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$matchedModel = $sql->singleQuery($model, $where, $parameters);
		
		$this->assertModel($matchedModel);
		$this->assertTrue($matchedModel->exists());
	}
	
	public function testSingleQuery_EmptyModelWhenNotFound() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$matchedModel = $sql->singleQuery($this->product, 'product_id > ?', array(100));
		
		$this->assertModel($matchedModel);
		$this->assertFalse($matchedModel->exists());
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testMultiQuery_PdoMustBeAttached() {
		$sql = new Sql;
		
		$sql->multiQuery($this->product, 'product_id > ?', array(100));
	}
	
	public function testMultiQuery_PreparesOnceForMultipleFetches() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->multiQuery($this->product, 'id != ?');
		
		//$product1 = $sql->fetch(array(1));
		//$product2 = $sql->fetch(array(2));
		
		//$this->assertTrue($product1->exists());
		//$this->assertTrue($product2->exists());
		
		//$this->assertFalse($product1->equalTo($product2));
		//$this->assertFalse($product2->equalTo($product1));
	}
	
	public function testBegin_StartsTransaction() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertTrue($sql->begin());
	}

	public function testCommit_SavesTransaction() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$order1 = clone $this->order;
		$order2 = clone $this->order;
		
		$order1->setDateCreated($sql->now())
			->setCustomerId(mt_rand(1, 100))
			->setTotal(193.336421344)
			->setName('My New Order');
		
		$sql->begin();
			$order1 = $sql->save($order1);
		$sql->commit();
		
		// Reload the order
		$order2 = $sql->singleQuery($order2, 'order_id = ?', array($order1->id()));
		
		$this->assertTrue($order2->exists());
	}
	
	public function testRollback_RevertsATransaction() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$order1 = clone $this->order;
		$order2 = clone $this->order;
		
		$order1->setDateCreated($sql->now())
			->setCustomerId(mt_rand(1, 100))
			->setName('My New Order');
		
		$sql->begin();
			$order1 = $sql->save($order1);
		$sql->rollback();
		
		// Reload the order
		$order2 = $sql->singleQuery($order2, 'order_id = ?', array($order1->id()));
		
		$this->assertFalse($order2->exists());
	}
	
	public function testSave_SetsInsertId() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->product->setName('Product 1');
		$this->product->setPrice(10.99);
		
		$product = $sql->save($this->product);
		
		$this->assertGreaterThan(0, $product->id());
	}
	
	public function testSave_PreparesOnceForSameModel() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		for ( $i=0; $i<10; $i++ ) {
			$price = round(mt_rand(1, 25) + (mt_rand(1, 25) / mt_rand(1, 25)), 2);
			
			$product = clone $this->product;
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
		
		$product = clone $this->product;
		$product->setName('Product 4');
		$product->setPrice(10.99);
		$product->setSku('P4');
		
		$user = clone $this->user;
		$user->setUsername('leftnode');
		$user->setPassword('password_test');
		
		$sql->save($product);
		$sql->save($user);
		
		$this->assertEquals(2, $sql->getPrepareCount());
	}
	
	public function testSave_PreparesTwiceForDifferentSaveTypes() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product1 = clone $this->product;
		$product1->id(1);
		$product1->setName('Product 1 *Updated*');
		$product1->setPrice(42.56);
		$product1->setSku('P1_NEW');
		
		$product2 = clone $this->product;
		$product2->setName('Product 2 *Updated*');
		$product2->setPrice(8.56);
		$product2->setSku('P5');
		
		$sql->save($product1); // update
		$sql->save($product2); // insert
		
		$this->assertEquals(2, $sql->getPrepareCount());
	}
	
	public function testSave_PreparesOnceForUpdatingSameModel() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product1 = clone $this->product;
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
		
		$product = clone $this->product;
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
	
	public function testGetPdo_ReturnsPdoObject() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertTrue($sql->getPdo() instanceof \PDO);
	}
	
	public function providerModelAndParameters() {
		$product = new Product;
		$user = new User;
		
		return array(
			array($product, 'product_id = :product_id', array('product_id' => 1)),
			array($product, 'product_id = ?', array(1)),
			array($product, 'product_id = :product_id OR sku = :sku', array('product_id' => 1, 'sku' => 'P2')),
			array($product, 'product_id = :product_id AND sku = :sku', array('product_id' => 1, 'sku' => 'P1')),
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