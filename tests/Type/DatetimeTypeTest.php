<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\DatetimeType;

require_once 'DataModeler/Type/Datetime.php';

class DatetimeTypeTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}