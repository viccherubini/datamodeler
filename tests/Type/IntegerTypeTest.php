<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\IntegerType;

require_once 'DataModeler/Type/Integer.php';

class IntegerTypeTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}