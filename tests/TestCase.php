<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

class TestCase extends \PHPUnit_Framework_TestCase {
	
	public static function assertArray($a, $message = '') {
		self::assertThat(is_array($a), self::isTrue(), $message);
	}
	
	
	public static function assertEmptyArray($a, $message = '') {
		self::assertArray($a);
		self::assertEquals(0, count($a), $message);
	}
	
	
	public static function assertNotEmptyArray($a, $message = '') {
		self::assertArray($a);
		self::assertGreaterThan(0, count($a), $message);
	}

	
	protected function buildMockAdapter() {
		$adapter = $this->getMockForAbstractClass('\DataModeler\Adapter\Adapter');
	
		return $adapter;
	}

	
	protected function buildMockModel() {
		$model = $this->getMockForAbstractClass('\DataModeler\Model');
		
		return $model;
	}
}