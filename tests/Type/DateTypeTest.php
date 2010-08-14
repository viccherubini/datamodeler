<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\DateType;

require_once 'DataModeler/Type/Date.php';

class DateTypeTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}