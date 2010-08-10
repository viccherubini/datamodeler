<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\IntegerType;

require_once 'lib/Type/Integer.php';

class IntegerTypeTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}