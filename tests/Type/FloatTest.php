<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\Float;

require_once 'lib/Type/Float.php';

class FloatTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}