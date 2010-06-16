<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Adapter;

use DataModelerTest\TestCase,
	DataModeler\Adapter\Sql,
	DataModeler\Iterator;

require_once 'lib/Adapter/Sql.php';

class SqlTest extends TestCase {
	
	private $pdo = NULL;
	private $product = NULL;
	
	public function setUp() {
		$this->pdo = new \PDO('sqlite::memory:');
		
		$sqlFile = DIRECTORY_DATA . 'SqlTest.sql';
		if ( true === is_file($sqlFile) ) {
			$sqlData = @file_get_contents($sqlFile);
			$this->pdo->exec($sqlData);
		}
		
		$this->product = $this->buildMockProduct();
	}

	public function tearDown() {
		$this->pdo = NULL;
	}


	public function testAttachPdo_CanAttachPdo() {
		$sql = new Sql;
		
		$this->assertSql($sql->attachPdo($this->pdo));
	}

	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testPrepare_RequiresPdo() {
		$sql = new Sql;
		
		$sql->prepare($this->product);
	}
	
	public function testPrepare_OnlyPreparesOnceForSameModel() {
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
		$user1 = $this->buildMockModel('users', 'id');
		$user2 = $this->buildMockModel('users', 'id');
		
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->prepare($product1);
		$sql->prepare($product2);
		$sql->prepare($user1);
		$sql->prepare($user2);
		
		$this->assertEquals(2, $sql->getPrepareCount());
	}

	public function testGet_ReturnsExistingModelIfFound() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->prepare($this->product);
		$product = $sql->get(1);
		
		$this->assertModel($product);
		$this->assertTrue($product->exists());
	}
	
	public function testGet_ReturnsNonExistingModelIfNotFound() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->prepare($this->product);
		$product = $sql->get(10000);
		
		$this->assertModel($product);
		$this->assertFalse($product->exists());
	}
	
	
	
}