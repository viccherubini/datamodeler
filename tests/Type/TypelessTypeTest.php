<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\TypelessType;

require_once 'lib/Type/Typeless.php';

class TypelessTypeTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}