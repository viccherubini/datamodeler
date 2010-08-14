<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

use DataModelerTest\TestCase,
	DataModeler\Type\TextType;

require_once 'DataModeler/Type/Text.php';

class TextTypeTest extends TestCase {
	
	public function testTrue() {
		$this->assertTrue(true);
	}
	
}