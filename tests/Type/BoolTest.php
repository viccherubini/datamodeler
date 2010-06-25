<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\Bool;

require_once 'lib/Type/Bool.php';

class BoolTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}