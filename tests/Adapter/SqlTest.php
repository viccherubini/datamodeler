<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Adapter;

use DataModelerTest\TestCase, DataModeler\Adapter\Sql;

require_once 'lib/Adapter/Sql.php';

class SqlTest extends TestCase {

	public function testPdoCanBeAttached() {
		$pdo = $this->buildMockPdo('sqlite::memory:');
		
		$sql = new Sql;
		$sql->attachDb($pdo);
		
		$this->assertTrue($sql->getDb() instanceof \PDO);
	}
	
	
	
}