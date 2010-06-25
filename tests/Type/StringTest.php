<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\String;

require_once 'lib/Type/String.php';

class StringTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}