<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\Text;

require_once 'lib/Type/Text.php';

class TextTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}