<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\Integer;

require_once 'lib/Type/Integer.php';

class IntegerTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}