<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\StringType;

require_once 'lib/Type/String.php';

class StringTypeTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}