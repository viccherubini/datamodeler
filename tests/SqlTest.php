<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModelerTest\TestCase,
	\DataModeler\Model,
	\DataModeler\Sql,
	\DataModeler\SqlResult;

require_once 'DataModeler/Sql.php';
require_once 'DataModeler/SqlResult.php';

require_once 'Product.php';
require_once 'User.php';

class SqlTest extends TestCase {
	
	public function setUp() {
		$this->buildPdo();
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
	 * @expectedException \DataModeler\Exception
	 */
	public function testPrepare_RequiresPdo() {
		$sql = new Sql;
		
		$sql->prepare($this->buildMockModel());
	}
	
	/**
	 * @dataProvider providerProductWhereClause
	 */
	public function testPrepare_ReturnsSqlResult($where) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sqlResult = $sql->prepare($this->buildMockProduct(), $where);
		
		$this->assertTrue($sqlResult instanceof \DataModeler\SqlResult);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testPreparePkey_RequiresPdo() {
		$sql = new Sql;
		
		$sql->preparePkey($this->buildMockModel());
	}
	
	public function testPreparePkey_ReturnsSqlResult() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sqlResult = $sql->preparePkey($this->buildMockProduct());
		
		$this->assertTrue($sqlResult instanceof \DataModeler\SqlResult);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testSave_RequiresPdo() {
		$sql = new Sql;
		
		$sql->save($this->buildMockProduct());
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testSave_RequiresPdoStatement() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = new Product;
		$product->id(1); // Fake load
		$product->setName('Product N')
			->setPrice(mt_rand(1, 100))
			->setCustomerId(mt_rand(1, 1000));
			
		$product = $sql->save($product);
		$this->assertTrue($product->exists()); // Ensure it actually updates properly
		
		$sql->attachPdoStatement(false); // Inject a bad statement
		
		$product->setPrice(mt_rand(101, 1000));
		$sql->save($product);
	}
	
	public function testSave_SetsInsertId() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = new Product;
		$product->setName('Product 1')
			->setPrice(10.99)
			->setCustomerId(10993);
		
		$product = $sql->save($product);
		
		$this->assertGreaterThan(0, $product->id());
		$this->assertEquals(1, $sql->getPrepareCount());
	}
	
	public function testSave_PreparesOnceForInsert() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		for ( $i=0; $i<5; $i++ ) {
			$product = new Product;
			$product->setName("Product {$i}")
				->setPrice(mt_rand(1, 100))
				->setCustomerId(mt_rand(1, 1000));
				
			$sql->save($product);
		}
		
		$this->assertEquals(1, $sql->getPrepareCount());
	}
	
	public function testSave_PreparesMultipleForInsert() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = new Product;
		$product->setName('Product N')
			->setPrice(mt_rand(1, 100))
			->setCustomerId(mt_rand(1, 1000));
			
		$user = new User;
		$user->setUsername('leftnode')
			->setPassword('password')
			->setAge(date('Y') - 1984);
			
		$sql->save($product);
		$sql->save($user);
		
		$this->assertEquals(2, $sql->getPrepareCount());
	}
	
	public function testSave_PreparesOnceForUpdate() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		for ( $i=0; $i<5; $i++ ) {
			$product = new Product;
			$product->id(1); // Fake load
			$product->setName("Product {$i}")
				->setPrice(mt_rand(1, 100))
				->setCustomerId(mt_rand(1, 1000));
				
			$sql->save($product);
		}
		
		$this->assertEquals(1, $sql->getPrepareCount());
	}
	
	public function testSave_PreparesMultipleForUpdate() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = new Product;
		$product->id(1);
		$product->setName('Product N')
			->setPrice(mt_rand(1, 100))
			->setCustomerId(mt_rand(1, 1000));
			
		$user = new User;
		$user->id(1);
		$user->setUsername('leftnode')
			->setPassword('password')
			->setAge(date('Y') - 1984);
			
		$sql->save($product);
		$sql->save($user);
		
		$this->assertEquals(2, $sql->getPrepareCount());
	}
	
	public function testSave_PreparesOnceForInsertOnceForUpdate() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = new Product;
		$product->setName('Product N')
			->setPrice(mt_rand(1, 100))
			->setCustomerId(mt_rand(1, 1000));
		$product = $sql->save($product); // Prepare #1 - INSERT
		
		$product->setname('Product N *updated*');
		$sql->save($product); // Prepare #2 - UPDATE
		
		$this->assertEquals(2, $sql->getPrepareCount());
	}
	
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testDelete_RequiresPdo() {
		$sql = new Sql;
		
		$sql->delete($this->buildMockProduct());
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testDelete_RequiresModel() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->delete(NULL);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testDelete_ModelMustExist() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->delete($this->buildMockProduct());
	}

	public function testDelete_CanDeleteModel() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = new Product;
		$product->id(1); // Fake load
		
		$deleted = $sql->delete($product);
		
		$this->assertTrue($deleted);
	}
	
	public function testDelete_CannotDeleteModel() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = new Product;
		$product->id(mt_rand(5000, 10000));
		
		$deleted = $sql->delete($product);
		
		$this->assertFalse($deleted);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testQuery_RequiresPdo() {
		$sql = new Sql;
		
		$sql->query('SELECT * FROM products');
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testCountOf_RequiresPdo() {
		$sql = new Sql;
		
		$sql->countOf($this->buildMockProduct());
	}
	
	/**
	 * @dataProvider providerProductWhereClauseAndParameters
	 */
	public function testCountOf_UsesWhere($where, $parameters) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$countOf = $sql->countOf($this->buildMockProduct(), $where, $parameters);
		
		$this->assertGreaterThan(0, $countOf);
	}
	
	public function testNow_ReturnsDatetime() {
		$sql = new Sql;
		
		$parsedDate = date_parse($sql->now());
		$this->assertEquals(0, count($parsedDate['errors']));
		
		$parsedDate = date_parse($sql->now(-1000));
		$this->assertEquals(0, count($parsedDate['errors']));
		
		$parsedDate = date_parse($sql->now(103990239));
		$this->assertEquals(0, count($parsedDate['errors']));
	}
	
	public function testNow_ReturnsDate() {
		$sql = new Sql;
		
		$parsedDate = date_parse($sql->now(0, true));
		$this->assertEquals(0, count($parsedDate['errors']));
		
		$parsedDate = date_parse($sql->now(-1000, true));
		$this->assertEquals(0, count($parsedDate['errors']));
		
		$parsedDate = date_parse($sql->now(103990239, true));
		$this->assertEquals(0, count($parsedDate['errors']));
	}
	
	public function testGetPdo_ReturnsPdoObject() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertTrue($sql->getPdo() instanceof \PDO);
	}
	
	public function providerProductWhereClause() {
		return array(
			array('product_id = ?'),
			array('product_id > ?'),
			array('product_id != ?'),
			array('product_id != ? AND name != ?'),
			array('price <> ?'),
			array('sku = ?'),
			array(NULL)
		);
	}

	public function providerProductWhereClauseAndParameters() {
		return array(
			array('product_id = ?', array(1)),
			array('product_id > ?', array(1)),
			array('product_id != ?', array(2)),
			array('product_id != ? AND name != ?', array(2, 'Product 1'))
		);
	}
}