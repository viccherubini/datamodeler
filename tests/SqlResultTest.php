<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModelerTest\TestCase,
	\DataModeler\SqlResult;

require_once 'DataModeler/SqlResult.php';

class SqlResultTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}