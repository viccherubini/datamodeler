<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModelerTest\TestCase,
	\DataModeler\Model,
	\DataModeler\Sql,
	\DataModeler\SqlResult;

require_once 'DataModeler/Sql.php';
require_once 'DataModeler/SqlResult.php';

require_once 'Missing.php';
require_once 'Product.php';
require_once 'User.php';

class SqlTest extends TestCase {
	
	private $pdo = NULL;
	
	public function setUp() {
		$sqlFile = DIRECTORY_DATA . DB_TYPE . '.sql';

		if ( !is_file($sqlFile) ) {
			throw new \Exception("Failed to load file {$sqlFile}.");
		}

		$this->pdo = new \PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$this->pdo->exec(file_get_contents($sqlFile));
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
	public function testPrepare_RequiredPdo() {
		$sql = new Sql;
		
		$sql->prepare($this->buildMockModel());
	}
	
	public function testPrepare_ReturnsSqlResult() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sqlResult = $sql->prepare($this->buildMockProduct(), 'price > ?');
		
		$this->assertTrue($sqlResult instanceof \DataModeler\SqlResult);
	}
	
	public function testPreparePkey_ReturnsSqlResult() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sqlResult = $sql->preparePkey($this->buildMockProduct());
		
		$this->assertTrue($sqlResult instanceof \DataModeler\SqlResult);
	}
	
	public function testSave_SetsInsertId() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$product = new Product;
		$product->setName('Product 1')
			->setPrice(10.99)
			->setField('text')
			->setCustomerId(10993);
		
		$product = $sql->save($product);
		
		$this->assertGreaterThan(0, $product->id());
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
		$product = new Product;
		$product->id(1); // Fake Load
		
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$deleted = $sql->delete($product);
		
		$this->assertTrue($deleted);
	}
	
	public function testDelete_CannotDeleteModel() {
		$product = new Product;
		$product->id(mt_rand(5000, 10000));
		
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$deleted = $sql->delete($product);
		
		$this->assertFalse($deleted);
	}
	
	/**
	 * @dataProvider providerProductWhereClause
	 */
	public function testCountOf_UsesWhere($where, $parameters) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$countOf = $sql->countOf($this->buildMockProduct(), $where, $parameters);
		
		$this->assertGreaterThan(0, $countOf);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testCountOf_RequiresPdo() {
		$sql = new Sql;
		$sql->countOf($this->buildMockProduct());
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
			array('product_id = ?', array(1)),
			array('product_id > ?', array(1)),
			array('product_id != ?', array(2)),
			array('product_id != ? AND name != ?', array(2, 'Product 1'))
		);
	}
}