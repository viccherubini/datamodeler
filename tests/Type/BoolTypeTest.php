<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\BoolType;

require_once 'DataModeler/Type/Bool.php';

class BoolTypeTest extends TestCase {
	
	public function testTrue() {
		$bool = new BoolType;
		$this->assertTrue(true);
	}
	
}