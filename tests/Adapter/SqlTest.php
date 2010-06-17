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
		$this->pdo = new \PDO('sqlite::memory:');
		
		$sqlFile = DIRECTORY_DATA . 'SqlTest.sql';
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
	
	public function testSave_PreparesTwiceForDifferentSaveMethods() {
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
}