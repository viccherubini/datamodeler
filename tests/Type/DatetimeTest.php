<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\Datetime;

require_once 'lib/Type/Datetime.php';

class DatetimeTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}