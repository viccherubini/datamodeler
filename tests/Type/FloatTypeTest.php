<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\FloatType;

require_once 'lib/Type/Float.php';

class FloatTypeTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}