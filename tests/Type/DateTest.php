<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\Date;

require_once 'lib/Type/Date.php';

class DateTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}